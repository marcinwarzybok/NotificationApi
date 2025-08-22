<?php

declare(strict_types=1);

namespace App\Tests\EmailNotification\Functional;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\Tests\Utils\TestCase\DatabaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EmailNotificationApiTest extends DatabaseTestCase
{
    private const EMAIL_NOTIFICATION_URI = '/api/email-notifications';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUBJECT = 'Test Subject';
    private const TEST_MESSAGE = 'Test Message';

    public function testCreateEmailNotification(): void
    {
        $response = $this->createEmailNotificationResponse();

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());

        $notifications = $this->getEmailNotificationRepository()->findAll();

        $this->assertCount(1, $notifications);
        $notification = $notifications[0];
        $this->assertSame([self::TEST_EMAIL], $notification->getRecipients());
        $this->assertSame(self::TEST_SUBJECT, $notification->getSubject());
        $this->assertSame(self::TEST_MESSAGE, $notification->getMessage());
        $this->assertSame('pending', $notification->getStatus()->value);
    }

    public function testCreateEmailNotificationWithMissingFields(): void
    {
        $requestDataWithoutMessage = [
            'email' => self::TEST_EMAIL,
            'subject' => self::TEST_SUBJECT,
        ];

        $response = $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: self::EMAIL_NOTIFICATION_URI,
            data: $requestDataWithoutMessage
        );

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode(), (string) $response->getContent());

        $notifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(0, $notifications);
    }

    public function testGetEmailNotificationsForEmptyDb(): void
    {
        $response = $this->getAllNotifications();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());

        $data = json_decode((string) $response->getContent(), true);

        $this->assertSame([], $data);

        $notifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(0, $notifications);
    }

    public function testGetEmailNotificationsWithData(): void
    {
        $createResponse = $this->createEmailNotificationResponse();

        $this->assertSame(Response::HTTP_CREATED, $createResponse->getStatusCode(), (string) $createResponse->getContent());

        $getNotificationsResponse = $this->getAllNotifications();

        $this->assertSame(Response::HTTP_OK, $getNotificationsResponse->getStatusCode(), (string) $getNotificationsResponse->getContent());

        $data = json_decode((string) $getNotificationsResponse->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        $notification = $data[0];
        $this->assertIsString($notification['uuid'] ?? '');
        $this->assertSame([self::TEST_EMAIL], $notification['recipients'] ?? []);
        $this->assertSame(self::TEST_SUBJECT, $notification['subject'] ?? '');
        $this->assertSame(self::TEST_MESSAGE, $notification['body'] ?? '');
        $this->assertSame('pending', $notification['status'] ?? '');
        $this->assertIsString($notification['createdAt'] ?? '');

        $repositoryNotifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(1, $repositoryNotifications);
        $repositoryNotification = $repositoryNotifications[0];
        $this->assertSame([self::TEST_EMAIL], $repositoryNotification->getRecipients());
        $this->assertSame(self::TEST_SUBJECT, $repositoryNotification->getSubject());
        $this->assertSame(self::TEST_MESSAGE, $repositoryNotification->getMessage());
        $this->assertSame('pending', $repositoryNotification->getStatus()->value);
    }

    public function testSendEmailNotification(): void
    {
        $createResponse = $this->createEmailNotificationResponse();

        $this->assertSame(Response::HTTP_CREATED, $createResponse->getStatusCode(), (string) $createResponse->getContent());

        $getNotificationsResponse = $this->getAllNotifications();

        $notifications = json_decode((string) $getNotificationsResponse->getContent(), true);
        $uuid = $notifications[0]['uuid'];

        $sendResponse = $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: sprintf('/api/email-notifications/%s/send', $uuid)
        );

        $this->assertSame(Response::HTTP_NO_CONTENT, $sendResponse->getStatusCode(), (string) $sendResponse->getContent());

        $notifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(1, $notifications);
        $notification = $notifications[0];
        $this->assertSame('sent', $notification->getStatus()->value);
    }

    public function testSendNonExistentEmailNotification(): void
    {
        $notExistingUuid = 'non-existent-uuid';

        $response = $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: sprintf('/api/email-notifications/%s/send', $notExistingUuid)
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode(), (string) $response->getContent());

        $notifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(0, $notifications);
    }

    public function testSendAlreadySentEmailNotification(): void
    {
        $createResponse = $this->createEmailNotificationResponse();

        $this->assertSame(Response::HTTP_CREATED, $createResponse->getStatusCode(), (string) $createResponse->getContent());

        $getNotificationsResponse = $this->getAllNotifications();
        $notifications = json_decode((string) $getNotificationsResponse->getContent(), true);
        $uuid = $notifications[0]['uuid'];

        $sendResponse = $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: sprintf('/api/email-notifications/%s/send', $uuid)
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $sendResponse->getStatusCode(), (string) $sendResponse->getContent());

        $resendResponse = $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: sprintf('/api/email-notifications/%s/send', $uuid)
        );

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $resendResponse->getStatusCode(), (string) $resendResponse->getContent());

        $notifications = $this->getEmailNotificationRepository()->findAll();
        $this->assertCount(1, $notifications);
        $notification = $notifications[0];
        $this->assertSame('sent', $notification->getStatus()->value);
    }

    private function getEmailNotificationRepository(): EmailNotificationRepositoryInterface
    {
        /** @var EmailNotificationRepositoryInterface $entityRepository */
        $entityRepository = $this->getContainer()->get(EmailNotificationRepositoryInterface::class);

        return $entityRepository;
    }

    private function createEmailNotificationResponse(): Response
    {
        $requestData = [
            'email' => self::TEST_EMAIL,
            'subject' => self::TEST_SUBJECT,
            'message' => self::TEST_MESSAGE,
        ];

        return $this->jsonRequest(
            method: Request::METHOD_POST,
            uri: self::EMAIL_NOTIFICATION_URI,
            data: $requestData
        );
    }

    private function getAllNotifications(): Response
    {
        return $this->jsonRequest(
            method: Request::METHOD_GET,
            uri: self::EMAIL_NOTIFICATION_URI
        );
    }
}
