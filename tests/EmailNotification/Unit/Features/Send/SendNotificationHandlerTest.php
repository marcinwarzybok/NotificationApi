<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\Send;

use App\EmailNotification\Features\Send\CannotResendEmailNotificationException;
use App\EmailNotification\Features\Send\SendNotificationCommand;
use App\EmailNotification\Features\Send\SendNotificationHandler;
use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Event\EmailNotification\EmailNotificationSendingFailed;
use App\Shared\Event\EmailNotification\EmailNotificationWasSent;
use App\Shared\Mailer\Email;
use App\Shared\Mailer\EmailSenderInterface;
use App\Shared\Mailer\SendingEmailException;
use App\Shared\Message\EventBusInterface;
use App\Shared\Message\MessageException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class SendNotificationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private const TEST_UUID = 'test-uuid';
    private const ERROR_MESSAGE = 'SMTP connection failed';
    private const EVENT_BUS_ERROR = 'Event bus failed';

    private SendNotificationHandler $testedClass;
    private SendNotificationCommand $command;
    private Email $email;
    /** @var ObjectProphecy<EmailSenderInterface> */
    private ObjectProphecy $emailSender;
    /** @var ObjectProphecy<EventBusInterface> */
    private ObjectProphecy $eventBus;
    /** @var ObjectProphecy<EmailNotification> */
    private ObjectProphecy $emailNotification;

    protected function setUp(): void
    {
        $notificationRepository = $this->prophesize(EmailNotificationRepositoryInterface::class);
        $this->emailSender = $this->prophesize(EmailSenderInterface::class);
        $this->eventBus = $this->prophesize(EventBusInterface::class);
        $this->emailNotification = $this->prophesize(EmailNotification::class);

        $this->emailNotification->getUuid()->willReturn(self::TEST_UUID);
        $this->emailNotification->getRecipients()->willReturn(['test@example.com']);
        $this->emailNotification->getSubject()->willReturn('Test Subject');
        $this->emailNotification->getMessage()->willReturn('Test Message');
        $this->emailNotification->isSent()->willReturn(true);

        $notificationRepository->getByUuid(self::TEST_UUID)->willReturn($this->emailNotification->reveal());

        $this->testedClass = new SendNotificationHandler(
            $notificationRepository->reveal(),
            $this->emailSender->reveal(),
            $this->eventBus->reveal()
        );

        $this->command = new SendNotificationCommand(self::TEST_UUID);
        $this->email = new Email(['test@example.com'], 'Test Subject', 'Test Message');
    }

    public function testSendEmailWhenNotificationIsNotSent(): void
    {
        $this->emailNotification->isSent()->willReturn(false);
        $this->emailNotification->markAsSent()->shouldBeCalled();

        $this->emailSender->send($this->email)->shouldBeCalled();
        $this->eventBus->dispatchAfterCurrentBusStamp(
            Argument::type(EmailNotificationWasSent::class)
        )->shouldBeCalled();

        $this->testedClass->__invoke($this->command);
    }

    public function testPreventResendingAlreadySentNotification(): void
    {
        $this->emailSender->send(Argument::any())->shouldNotBeCalled();
        $this->eventBus->dispatchAfterCurrentBusStamp(Argument::any())->shouldNotBeCalled();

        $this->expectException(CannotResendEmailNotificationException::class);
        $this->testedClass->__invoke($this->command);
    }

    public function testDispatchFailureEventWhenEmailSendingFails(): void
    {
        $this->emailNotification->isSent()->willReturn(false);

        $this->emailSender->send(Argument::any())->willThrow(new SendingEmailException(self::ERROR_MESSAGE));
        $this->eventBus->dispatch(
            Argument::that(fn ($event): bool => $event instanceof EmailNotificationSendingFailed
                && self::TEST_UUID === $event->uuid
                && self::ERROR_MESSAGE === $event->errorMessage)
        )->shouldBeCalled();

        $this->expectException(SendingEmailException::class);
        $this->testedClass->__invoke($this->command);
    }

    public function testNotifyAboutUnableToSendEmail(): void
    {
        $this->emailNotification->isSent()->willReturn(false);

        $this->emailSender->send(Argument::any())->willThrow(new SendingEmailException(self::ERROR_MESSAGE));
        $this->eventBus->dispatch(Argument::any())->willThrow(new MessageException(self::EVENT_BUS_ERROR));

        $this->expectException(SendingEmailException::class);
        $this->testedClass->__invoke($this->command);
    }
}
