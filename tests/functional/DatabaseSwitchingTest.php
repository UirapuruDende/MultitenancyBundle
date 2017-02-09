<?php
namespace Tests\Functional;

use DateTime;
use Doctrine\ORM\Tools\SchemaTool;
use Tests\App\Entity\Invoice;

class DatabaseSwitchingTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function get_entities_from_switched_connection()
    {
        setup : {
            $client = $this->createClient();
            $container = $client->getContainer();
            $entityManager = $container->get('doctrine.orm.first_entity_manager');
            $tenantManager = $container->get('dende_multitenancy.tenant_manager');
            $invoiceRepository = $container->get('repository.invoice');
            $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
            $schemaTool = new SchemaTool($entityManager);;
        }

        insert_data: {
            $tenantManager->switchConnection('first', 'test_subdomain_tenant_1');
            $schemaTool->createSchema($metadata);

            $invoiceRepository->insert(new Invoice(null, 'aaa', new DateTime()));
            $invoiceRepository->insert(new Invoice(null, 'bbb', new DateTime()));
            $invoiceRepository->insert(new Invoice(null, 'ccc', new DateTime()));

            $tenantManager->switchConnection('first', 'test_subdomain_tenant_2');
            $schemaTool->createSchema($metadata);

            $invoiceRepository->insert(new Invoice(null, 'ddd', new DateTime()));

            $tenantManager->switchConnection('first', 'test_subdomain_tenant_3');
            $schemaTool->createSchema($metadata);

            $invoiceRepository->insert(new Invoice(null, 'eee', new DateTime()));
            $invoiceRepository->insert(new Invoice(null, 'fff', new DateTime()));
        }

        $tenantManager->switchConnection('first', 'test_subdomain_tenant_1');
        $this->assertCount(3, $invoiceRepository->findAll());

        $tenantManager->switchConnection('first', 'test_subdomain_tenant_2');
        $this->assertCount(1, $invoiceRepository->findAll());

        $tenantManager->switchConnection('first', 'test_subdomain_tenant_3');
        $this->assertCount(2, $invoiceRepository->findAll());
    }
}