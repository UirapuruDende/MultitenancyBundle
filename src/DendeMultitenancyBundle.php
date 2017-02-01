<?php
namespace Dende\MultitenancyBundle;

use Dende\MultitenancyBundle\DependencyInjection\CompilerPass\RegisterProviders;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DendeMultitenancyBundle extends Bundle
{
//    /**
//     * Boots the Bundle.
//     */
//    public function boot()
//    {
//        parent::boot();
//
//        if ($this->container->getParameter('kernel.environment') !== 'prod') {
//            $this->container->get('dende.multidatabase.doctrine_fixtures_load_listener')->setOptions([
//                'default' => $this->container->getParameter('standardfixtures'),
//                'tenant'    => $this->container->getParameter('tenantfixtures'),
//            ]);
//        }
//    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterProviders());
    }
}
