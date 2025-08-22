<?php

namespace App\Tests\Utils;

use Symfony\Component\HttpFoundation\Response;

trait ClientTrait
{
    /** @param array<string|int, mixed>|null $data */
    public function jsonRequest(string $method, string $uri, ?array $data = null): Response
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        $content = null !== $data ? json_encode($data, JSON_THROW_ON_ERROR) : null;

        self::$client->request(
            method: $method,
            uri: $uri,
            server: $headers,
            content: $content
        );

        return self::$client->getResponse();
    }
}
