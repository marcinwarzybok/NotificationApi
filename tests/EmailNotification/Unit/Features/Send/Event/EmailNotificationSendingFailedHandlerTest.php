<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\Send\Event;

use App\EmailNotification\Features\Send\Event\EmailNotificationSendingFailedHandler;
use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Event\EmailNotification\EmailNotificationSendingFailed;
use App\Shared\Logger\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class EmailNotificationSendingFailedHandlerTest extends TestCase
{
    use ProphecyTrait;

    private const TEST_UUID = 'test-uuid';
    private const ERROR_MESSAGE = 'SMTP connection failed';

    private EmailNotificationSendingFailedHandler $testedClass;
    private EmailNotificationSendingFailed $event;
    /** @var ObjectProphecy<EmailNotificationRepositoryInterface> */
    private ObjectProphecy $repository;
    /** @var ObjectProphecy<LoggerInterface> */
    private ObjectProphecy $logger;
    /** @var ObjectProphecy<EmailNotification> */
    private ObjectProphecy $emailNotification;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(EmailNotificationRepositoryInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->emailNotification = $this->prophesize(EmailNotification::class);
        $this->testedClass = new EmailNotificationSendingFailedHandler(
            $this->repository->reveal(),
            $this->logger->reveal()
        );
        $this->event = new EmailNotificationSendingFailed(self::TEST_UUID, self::ERROR_MESSAGE);

        $this->repository->getByUuid(self::TEST_UUID)->willReturn($this->emailNotification->reveal());
    }

    public function testMarkNotificationAsFailed(): void
    {
        $this->emailNotification->markAsFailed()->shouldBeCalled();
        $this->repository->save($this->emailNotification->reveal())->shouldBeCalled();
        $this->logger->error('Email notification failed to send for uuid: test-uuid. SMTP connection failed')->shouldBeCalled();

        $this->testedClass->__invoke($this->event);
    }
}
