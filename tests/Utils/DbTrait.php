<?php

namespace App\Tests\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait DbTrait
{
    public function initDatabase(KernelBrowser $client): KernelBrowser
    {
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);

        return $client;
    }
}
