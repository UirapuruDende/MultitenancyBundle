<?php
namespace Dende\MultitenancyBundle\Listener;

use Dende\MultitenancyBundle\Manager\TenantManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Exception;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;

class CommandListener
{
    /** @var  TenantManager */
    private $tenantManager;

    /** @var  array|string[] */
    private $allowedCommands;

    /** @var AbstractSchemaManager */
    private $schemaManager;

    /** @var array */
    private $config;

    public function __construct(TenantManager $tenantManager, AbstractSchemaManager $schemaManager, $allowedCommands = [], $config = [])
    {
        $this->tenantManager = $tenantManager;
        $this->schemaManager = $schemaManager;
        $this->allowedCommands = $allowedCommands;
        $this->config = $config;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event) : void
    {
        $command = $event->getCommand();
        $input = $event->getInput();

        if (!$this->isProperCommand($command)) {
            return;
        }

        foreach($this->config as $connection) {
            $newOptionToNameTenant = new InputOption($connection['param'], null, InputOption::VALUE_OPTIONAL, $connection['desc'], null);

            $commandDefinition = $event->getCommand()->getDefinition();
            $commandDefinition->addOption($newOptionToNameTenant);

            $inputDefinition = $event->getCommand()->getApplication()->getDefinition();
            $inputDefinition->addOption($newOptionToNameTenant);

            $input->bind($commandDefinition);

            if($tenantId = $input->getOption($connection['param'])) {
                if($commandDefinition->hasOption('connection')) {
                    $connectionOptionArgument = $commandDefinition->getOption('connection');
                    $connectionOptionArgument->setDefault($connection['name']);
                } elseif ($commandDefinition->hasOption('em')) {
                    $connectionOptionArgument = $commandDefinition->getOption('em');
                    $connectionOptionArgument->setDefault($connection['name']);
                }

                $this->tenantManager->switchConnection($connection['name'], $tenantId);
            }
        }
    }

    private function isProperCommand(Command $command) : bool
    {
        $testedName = $command->getName();

        if($testedName === 'help') {
            $reflectionClass = new ReflectionClass(HelpCommand::class);
            $reflectionProperty = $reflectionClass->getProperty('command');
            $reflectionProperty->setAccessible(true);
            /** @var Command $command */
            $command = $reflectionProperty->getValue($command);

            if($command === null) {
                return false;
            }

            $testedName = $command->getName();
        }

        $result = array_map(function($allowedCommand) use ($testedName){
            $prepared = strtr(preg_quote($allowedCommand, '#'), ['\*' => '.*', '\?' => '.']);
            return preg_match("#^".$prepared."$#i", $testedName);
        }, $this->allowedCommands);

        return array_sum($result) > 0 || in_array($command->getName(), $this->allowedCommands);
    }
}
