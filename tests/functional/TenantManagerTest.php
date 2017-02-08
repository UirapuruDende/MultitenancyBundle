<?php
namespace Tests\Functional;

use Dende\MultitenancyBundle\Manager\TenantManager;
use Doctrine\DBAL\Connection;

class TenantManagerTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function switching_connection_directly()
    {
        $client = $this->createClient();
        /** @var Connection $connection */
        $connection = $client->getContainer()->get('doctrine.dbal.first_connection');
        /** @var TenantManager $manager */
        $manager = $client->getContainer()->get('dende_multitenancy.tenant_manager');

        $manager->switchConnection('first', 'test_subdomain_tenant_3');
        $this->assertEquals('3_test_subdomain_tenant_username', $connection->getParams()['user']);

        $manager->switchConnection('first', 'test_subdomain_tenant_2');
        $this->assertEquals('2_test_subdomain_tenant_username', $connection->getParams()['user']);
    }

    /**
     * @test
     */
    public function switching_connection_in_provider()
    {
        $client = $this->createClient();
        /** @var Connection $connection */
        $connection = $client->getContainer()->get('doctrine.dbal.first_connection');
        /** @var TenantManager $manager */
        $manager = $client->getContainer()->get('dende_multitenancy.tenant_manager');
        $provider = $client->getContainer()->get('provider.subdomain');

        $provider->setTenantId('test_subdomain_tenant_3');
        $manager->switchConnection('first');
        $this->assertEquals('3_test_subdomain_tenant_username', $connection->getParams()['user']);

        $provider->setTenantId('test_subdomain_tenant_2');
        $manager->switchConnection('first');
        $this->assertEquals('2_test_subdomain_tenant_username', $connection->getParams()['user']);
    }
}