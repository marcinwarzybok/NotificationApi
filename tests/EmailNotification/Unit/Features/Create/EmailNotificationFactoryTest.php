<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\Create;

use App\EmailNotification\Features\Create\CreateEmailNotificationDto;
use App\EmailNotification\Features\Create\EmailNotificationFactory;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\EmailNotification\Shared\Model\NotificationStatus;
use App\Shared\Uuid\UuidProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class EmailNotificationFactoryTest extends TestCase
{
    use ProphecyTrait;

    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUBJECT = 'Test Subject';
    private const TEST_MESSAGE = 'Test Message';
    private const TEST_UUID = 'test-uuid';

    private EmailNotificationFactory $testedClass;

    protected function setUp(): void
    {
        $uuidProvider = $this->prophesize(UuidProviderInterface::class);
        $uuidProvider->generate()->willReturn(self::TEST_UUID);

        $this->testedClass = new EmailNotificationFactory($uuidProvider->reveal());
    }

    public function testCreateEmailNotification(): void
    {
        $dto = new CreateEmailNotificationDto(self::TEST_EMAIL, self::TEST_SUBJECT, self::TEST_MESSAGE);
        $emailNotification = $this->testedClass->create($dto);

        $this->assertInstanceOf(EmailNotification::class, $emailNotification);
        $this->assertSame(self::TEST_UUID, $emailNotification->getUuid());
        $this->assertSame([self::TEST_EMAIL], $emailNotification->getRecipients());
        $this->assertSame(self::TEST_SUBJECT, $emailNotification->getSubject());
        $this->assertSame(self::TEST_MESSAGE, $emailNotification->getMessage());
        $this->assertSame(NotificationStatus::PENDING, $emailNotification->getStatus());
        $this->assertFalse($emailNotification->isSent());
    }
}
