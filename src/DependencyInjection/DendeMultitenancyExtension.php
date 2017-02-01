<?php
namespace Dende\MultitenancyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DendeMultitenancyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('dende_multitenancy.config.patched_commands', $this->createPatchedCommandsConfig($config));

//        $container->setParameter("dende_multidatabase.tenant_provider", $config["provider"]);
//        $container->setParameter("dende_multidatabase.connection_name", $config["connection"]);
//        $container->setParameter("dende_multidatabase.model_manager_name", $config["entity_manager"]);
//        $container->setParameter("dende_multidatabase.parameter_name", $config["parameter_name"]);
//        $container->setParameter("dende_multidatabase.parameter_description", $config["parameter_description"]);
//        $container->setParameter("dende_multidatabase.fixtures", $config["fixtures"]);
//        $container->setParameter("dende_multidatabase.commands", $config["commands"]);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    private function createPatchedCommandsConfig($config)
    {
        $connections = $config['connections'];

        return array_map(function($connection, $connectionName){
            return [
                'name' => $connectionName,
                'param' => $connection['command_parameter_name'],
                'desc'  => $connection['command_parameter_description'],
            ];
        }, $connections, array_keys($connections));
    }
}
