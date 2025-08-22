<?php

declare(strict_types=1);

namespace App\EmailNotification\Shared\Model;

use App\EmailNotification\Features\Create\EmailNotificationFactory;
use DaveLiddament\PhpLanguageExtensions\Friend;

class EmailNotification
{
    private ?int $id = null;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private NotificationStatus $status = NotificationStatus::PENDING;

    /** @param list<string> $recipients */
    #[Friend(EmailNotificationFactory::class)]
    public function __construct(
        private string $uuid,
        private string $message,
        private array $recipients,
        private string $subject,
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /** @return list<string> */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatus::SENT;
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatus::FAILED;
    }

    public function isSent(): bool
    {
        return NotificationStatus::SENT === $this->status;
    }
}
