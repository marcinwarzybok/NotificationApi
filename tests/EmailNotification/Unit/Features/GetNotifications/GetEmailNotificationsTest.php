<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\GetNotifications;

use App\EmailNotification\Features\GetNotifications\GetEmailNotifications;
use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\EmailNotification\Shared\Model\NotificationStatus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GetEmailNotificationsTest extends TestCase
{
    use ProphecyTrait;

    private GetEmailNotifications $testedClass;
    private ObjectProphecy $repository;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(EmailNotificationRepositoryInterface::class);
        $this->testedClass = new GetEmailNotifications($this->repository->reveal());
    }

    public function testGetAllEmailNotifications(): void
    {
        $notification = $this->prophesize(EmailNotification::class);
        $notification->getUuid()->willReturn('uuid-1');
        $notification->getRecipients()->willReturn(['test1@example.com']);
        $notification->getSubject()->willReturn('Subject 1');
        $notification->getMessage()->willReturn('Message 1');
        $notification->getStatus()->willReturn(NotificationStatus::PENDING);
        $notification->getCreatedAt()->willReturn(new \DateTimeImmutable('2023-01-01 10:00:00'));

        $this->repository->findAll()->willReturn([$notification->reveal()]);

        $result = $this->testedClass->execute();

        $expected = [[
            'uuid' => 'uuid-1',
            'recipients' => ['test1@example.com'],
            'subject' => 'Subject 1',
            'body' => 'Message 1',
            'status' => 'pending',
            'createdAt' => '2023-01-01 10:00:00',
        ]];

        $this->assertSame($expected, $result);
    }

    public function testGetEmptyListWhenNoNotifications(): void
    {
        $this->repository->findAll()->willReturn([]);

        $result = $this->testedClass->execute();

        $this->assertSame([], $result);
    }
}
