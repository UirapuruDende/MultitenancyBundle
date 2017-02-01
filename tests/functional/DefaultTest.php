<?php
namespace Tests\Functional;

use Dende\MultitenancyBundle\Manager\TenantManager;
use Doctrine\DBAL\Connection;

class DefaultTest extends FunctionalTestCase
{
    public function testTest()
    {
        $client = $this->createClient();

        /** @var TenantManager $manager */
        $manager = $client->getContainer()->get('dende_multitenanacy.tenant_manager');

        $manager->switchConnection('first');

        /** @var Connection $connection */
        $connection = $client->getContainer()->get('doctrine.dbal.first_connection');

        $this->assertEquals('username', $connection->getParams()['user']);

        $this->assertTrue(true);
    }
}