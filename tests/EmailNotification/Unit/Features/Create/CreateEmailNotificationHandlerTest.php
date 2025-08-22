<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\Create;

use App\EmailNotification\Features\Create\CreateEmailNotificationCommand;
use App\EmailNotification\Features\Create\CreateEmailNotificationDto;
use App\EmailNotification\Features\Create\CreateEmailNotificationHandler;
use App\EmailNotification\Features\Create\EmailNotificationFactoryInterface;
use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Event\EmailNotification\EmailNotificationWasCreated;
use App\Shared\Message\EventBusInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateEmailNotificationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUBJECT = 'Test Subject';
    private const TEST_MESSAGE = 'Test Message';
    private const TEST_UUID = 'test-uuid';

    private CreateEmailNotificationHandler $testedClass;
    private CreateEmailNotificationCommand $command;
    /** @var ObjectProphecy<EmailNotificationFactoryInterface> */
    private ObjectProphecy $factory;
    /** @var ObjectProphecy<EmailNotificationRepositoryInterface> */
    private ObjectProphecy $repository;
    /** @var ObjectProphecy<EventBusInterface> */
    private ObjectProphecy $eventBus;
    /** @var ObjectProphecy<EmailNotification> */
    private ObjectProphecy $emailNotification;

    protected function setUp(): void
    {
        $this->factory = $this->prophesize(EmailNotificationFactoryInterface::class);
        $this->repository = $this->prophesize(EmailNotificationRepositoryInterface::class);
        $this->eventBus = $this->prophesize(EventBusInterface::class);
        $this->emailNotification = $this->prophesize(EmailNotification::class);

        $this->emailNotification->getUuid()->willReturn(self::TEST_UUID);

        $this->testedClass = new CreateEmailNotificationHandler(
            $this->factory->reveal(),
            $this->repository->reveal(),
            $this->eventBus->reveal()
        );

        $this->command = new CreateEmailNotificationCommand(
            self::TEST_EMAIL,
            self::TEST_SUBJECT,
            self::TEST_MESSAGE
        );
    }

    public function testCreateEmailNotification(): void
    {
        $this->factory->create(
            Argument::that(fn ($dto): bool => $dto instanceof CreateEmailNotificationDto
                && self::TEST_EMAIL === $dto->email
                && self::TEST_SUBJECT === $dto->subject
                && self::TEST_MESSAGE === $dto->message)
        )->willReturn($this->emailNotification->reveal());

        $this->repository->saveDeferred($this->emailNotification->reveal())->shouldBeCalled();
        $this->eventBus->dispatchAfterCurrentBusStamp(
            Argument::type(EmailNotificationWasCreated::class)
        )->shouldBeCalled();

        $this->testedClass->__invoke($this->command);
    }
}
