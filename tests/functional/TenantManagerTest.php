<?php
namespace Tests\Functional;

use Dende\MultitenancyBundle\Manager\TenantManager;
use Doctrine\DBAL\Connection;

class TenantManagerTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function switching_connection()
    {
        $this->markTestSkipped();
        $client = $this->createClient();

        /** @var TenantManager $manager */
        $manager = $client->getContainer()->get('dende_multitenancy.tenant_manager');

        $manager->switchConnection('first');

        /** @var Connection $connection */
        $connection = $client->getContainer()->get('doctrine.dbal.first_connection');

        $this->assertEquals('username', $connection->getParams()['user']);

        $this->assertTrue(true);
    }
}