<?php

namespace Dende\MultitenancyBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProviders implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) {
        $tenantManagerDefinition = $container->getDefinition(
            'dende_multitenanacy.tenant_manager'
        );

        $tenantProvidersDefinitions = $container->findTaggedServiceIds(
            'tenant_provider'
        );
        foreach ($tenantProvidersDefinitions as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {

                $tenantManagerDefinition->addMethodCall(
                    'registerConnection',
                    array($attributes["connection_name"], new Reference(sprintf('doctrine.dbal.%s_connection', $attributes["connection_name"])))
                );

                $tenantManagerDefinition->addMethodCall(
                    'registerProvider',
                    array($attributes["connection_name"], new Reference($id))
                );
            }
        }
    }
}