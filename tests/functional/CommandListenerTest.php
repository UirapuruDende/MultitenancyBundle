<?php
namespace Tests\Functional;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

class CommandListenerTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function listener_adds_parameter_and_description_to_allowed_commands()
    {
        $command = 'doctrine:schema:update --help';

        $client = self::createClient();
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        $config = $client->getContainer()->getParameter('dende_multitenancy.config.command_listener_config');

        foreach($config as $connection) {
            $this->assertContains("--".$connection['param'], $output);
            $this->assertContains($connection['desc'], $output);
        }
    }

    /**
     * @test
     */
    public function listener_does_not_add_parameter_to_disallowed_commands()
    {
        $command = 'clear:cache --help';

        $client = self::createClient();
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        $config = $client->getContainer()->getParameter('dende_multitenancy.config.command_listener_config');

        foreach($config as $connection) {
            $this->assertNotContains("--".$connection['param'], $output);
            $this->assertNotContains($connection['desc'], $output);
        }
    }

    /**
     * @test
     */
    public function listener_switches_connections()
    {
        $command = 'doctrine:schema:update --subdomain=test_subdomain_tenant_1';

        $client = self::createClient();
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        /** @var Connection $connection */
        $connection = $client->getContainer()->get('doctrine.dbal.first_connection');

        $params = $connection->getParams();

        $this->assertEquals('1_test_subdomain_tenant_dbname', $params['dbname']);
    }
}
