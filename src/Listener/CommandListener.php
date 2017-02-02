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

    /**
     * ClubConnectionCommandListener constructor.
     * @param array $config
     */
    public function __construct(TenantManager $tenantManager, AbstractSchemaManager $schemaManager, $allowedCommands = [], $config = [])
    {
        $this->tenantManager = $tenantManager;
        $this->schemaManager = $schemaManager;
        $this->allowedCommands = $allowedCommands;
        $this->config = $config;
    }

    /**
     * @param ConsoleCommandEvent $event
     * @throws Exception
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        $input = $event->getInput();

        if (!$this->isProperCommand($command)) {
            return;
        }

        foreach($this->config as $connection) {
            $option = new InputOption($connection['param'], null, InputOption::VALUE_OPTIONAL, $connection['desc'], null);

            $commandDefinition = $event->getCommand()->getDefinition();
            $commandDefinition->addOption($option);

            $inputDefinition = $event->getCommand()->getApplication()->getDefinition();
            $inputDefinition->addOption($option);

            $input->bind($commandDefinition);

            if($tenantId = $input->getOption($connection['param'])) {
                $this->tenantManager->switchConnection($connection['name'], $tenantId);
            }
        }
    }

    /**
     * @param Command $command
     * @return bool
     */
    private function isProperCommand(Command $command)
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
            $prepared = strtr(preg_quote($allowedCommand, '#'), array('\*' => '.*', '\?' => '.'));
            return preg_match("#^".$prepared."$#i", $testedName);
        }, $this->allowedCommands);

        return array_sum($result) > 0 || in_array($command->getName(), $this->allowedCommands);
    }
}
