<?php

declare(strict_types=1);

namespace App\Tests\Utils\TestCase;

use App\Tests\Utils\ClientTrait;
use App\Tests\Utils\DbTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DatabaseTestCase extends WebTestCase
{
    use ClientTrait;
    use DbTrait;

    protected static KernelBrowser $client;
    protected static ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = static::createClient();
        self::$container = self::$client->getKernel()->getContainer();
        $this->initDatabase(self::$client);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        return $entityManager;
    }
}
