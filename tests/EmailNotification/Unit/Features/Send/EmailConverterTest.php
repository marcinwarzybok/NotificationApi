<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Unit\Features\Send;

use App\EmailNotification\Features\Send\EmailConverter;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Mailer\Email;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class EmailConverterTest extends TestCase
{
    use ProphecyTrait;

    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUBJECT = 'Test Subject';
    private const TEST_MESSAGE = 'Test Message';

    /** @var ObjectProphecy<EmailNotification> */
    private ObjectProphecy $emailNotification;

    protected function setUp(): void
    {
        $this->emailNotification = $this->prophesize(EmailNotification::class);
        $this->emailNotification->getRecipients()->willReturn([self::TEST_EMAIL]);
        $this->emailNotification->getSubject()->willReturn(self::TEST_SUBJECT);
        $this->emailNotification->getMessage()->willReturn(self::TEST_MESSAGE);
    }

    public function testConvertEmailNotificationToEmail(): void
    {
        $email = EmailConverter::fromEmailNotification($this->emailNotification->reveal());

        $this->assertInstanceOf(Email::class, $email);
        $this->assertSame([self::TEST_EMAIL], $email->recipients);
        $this->assertSame(self::TEST_SUBJECT, $email->subject);
        $this->assertSame(self::TEST_MESSAGE, $email->body);
    }
}
