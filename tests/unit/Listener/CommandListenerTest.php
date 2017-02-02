<?php
namespace Tests\Unit\Listener;

use Dende\MultitenancyBundle\Listener\CommandListener;
use Dende\MultitenancyBundle\Manager\TenantManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PHPUnit_Framework_TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;

class CommandListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $commands
     * @param $bool
     * @dataProvider isProperCommandDataProvider
     */
    public function testIsProperCommand($commands, $tested, $bool)
    {
        /** @var ObjectProphecy|TenantManager $tenantManager */
        $tenantManager = $this->prophesize(TenantManager::class)->reveal();
        /** @var ObjectProphecy|AbstractSchemaManager $schemaManager */
        $schemaManager = $this->prophesize(AbstractSchemaManager::class)->reveal();
        /** @var ObjectProphecy|Command $command */
        $command = $this->prophesize(Command::class);
        $command->getName()->willReturn($tested);
        $command = $command->reveal();

        $object = new CommandListener($tenantManager, $schemaManager, $commands, []);

        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod('isProperCommand');
        $method->setAccessible(true);

        $this->assertEquals($bool, $method->invokeArgs($object, [$command]));
    }

    public function isProperCommandDataProvider()
    {
        return [
            'correct with wildcard' => [
                'commands' => [
                    'test:abc',
                    'test:',
                    'different:test',
                    'test:c*',
                    'test:correct',
                ],
                'tested' => 'test:correct',
                'bool'  => true
            ],
            'correct exact' => [
                'commands' => [
                    'test:correct',
                ],
                'tested' => 'test:correct',
                'bool'  => true
            ],
            'incorrect' => [
                'commands' => [
                    'test:abc',
                    'test:',
                    'different:test',
                    'test:correct',
                    'test:c*',
                ],
                'tested' => 'test',
                'bool'  => false
            ],
            'empty command name' => [
                'commands' => [
                    'test:abc',
                    'test:',
                    'different:test',
                    'test:correct',
                    'test:c*',
                ],
                'tested' => null,
                'bool'  => false
            ],
            'empty list' => [
                'commands' => [],
                'tested' => 'test',
                'bool'  => false
            ],
        ];
    }
}