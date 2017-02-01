<?php
namespace Dende\MultitenancyBundle\Manager;

use Dende\MultitenancyBundle\Connection\Wrapper;
use Dende\MultitenancyBundle\Provider\TenantProviderInterface;
use Doctrine\DBAL\Connection;
use Exception;

class TenantManager
{
    /**
     * @var array|Wrapper[]
     */
    protected $connections = [];

    /**
     * @var array|TenantProviderInterface[]
     */
    protected $providers = [];

    /**
     * @param $name
     * @param Connection $connection
     */
    public function registerConnection($name, Connection $connection)
    {
        $this->connections[$name] = $connection;
    }

    /**
     * @param $connectionName
     * @param TenantProviderInterface $provider
     * @throws Exception
     */
    public function registerProvider($connectionName, TenantProviderInterface $provider)
    {
        if(!in_array($connectionName, array_keys($this->connections))) {
            throw new Exception(sprintf('Connection %s is not registered in TenantManager!', $connectionName));
        }

        $this->providers[$connectionName] = $provider;
    }

    public function switchConnection($connectionName)
    {
        $this->connections[$connectionName]->forceSwitch(
            $this->providers[$connectionName]->getTenant()
        );
    }
}