<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Shared\Model;

use App\EmailNotification\Shared\Model\EmailNotification;
use App\EmailNotification\Shared\Model\NotificationStatus;
use PHPUnit\Framework\TestCase;

final class EmailNotificationTest extends TestCase
{
    private const TEST_UUID = 'test-uuid';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUBJECT = 'Test Subject';
    private const TEST_MESSAGE = 'Test Message';

    private EmailNotification $emailNotification;

    protected function setUp(): void
    {
        $this->emailNotification = new EmailNotification(
            self::TEST_UUID,
            self::TEST_MESSAGE,
            [self::TEST_EMAIL],
            self::TEST_SUBJECT
        );
    }

    public function testCreateEmailNotificationWithSpecificProperties(): void
    {
        $this->assertSame([self::TEST_EMAIL], $this->emailNotification->getRecipients());
        $this->assertSame(self::TEST_SUBJECT, $this->emailNotification->getSubject());
        $this->assertSame(self::TEST_UUID, $this->emailNotification->getUuid());
        $this->assertSame(self::TEST_MESSAGE, $this->emailNotification->getMessage());
    }

    public function testCreateNotificationWithPendingStatus(): void
    {
        $this->assertSame(self::TEST_UUID, $this->emailNotification->getUuid());
        $this->assertSame(self::TEST_MESSAGE, $this->emailNotification->getMessage());
        $this->assertSame(NotificationStatus::PENDING, $this->emailNotification->getStatus());
        $this->assertFalse($this->emailNotification->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->emailNotification->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->emailNotification->getUpdatedAt());
    }

    public function testSendNotification(): void
    {
        $this->emailNotification->markAsSent();

        $this->assertSame(NotificationStatus::SENT, $this->emailNotification->getStatus());
        $this->assertTrue($this->emailNotification->isSent());
    }

    public function testMarkNotificationAsFailed(): void
    {
        $this->emailNotification->markAsFailed();

        $this->assertSame(NotificationStatus::FAILED, $this->emailNotification->getStatus());
        $this->assertFalse($this->emailNotification->isSent());
    }

    public function testSentNotificationRemainsInSentStatus(): void
    {
        $this->emailNotification->markAsSent();

        $this->assertTrue($this->emailNotification->isSent());
    }

    public function testFailedNotificationIsNotSent(): void
    {
        $this->emailNotification->markAsFailed();

        $this->assertFalse($this->emailNotification->isSent());
    }
}
