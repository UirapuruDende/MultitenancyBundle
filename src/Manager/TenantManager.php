<?php
namespace Dende\MultitenancyBundle\Manager;

use Dende\MultitenancyBundle\Connection\Wrapper;
use Dende\MultitenancyBundle\Event\PostSwitchConnection;
use Dende\MultitenancyBundle\Exception\TenantNotFoundException;
use Dende\MultitenancyBundle\Provider\TenantProviderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\DBAL\Exception\ConnectionException;

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
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function registerConnection(string $name, Connection $connection) : void
    {
        $this->connections[$name] = $connection;
    }

    public function registerProvider(string $connectionName, TenantProviderInterface $provider) : void
    {
        if(!in_array($connectionName, array_keys($this->connections))) {
            throw new Exception(sprintf('Connection %s is not registered in TenantManager!', $connectionName));
        }

        $this->providers[$connectionName] = $provider;
    }

    public function switchConnection(string $connectionName, ?string $tenantId = null)
    {
        if($tenantId) {
            $this->providers[$connectionName]->setTenantId($tenantId);
        }

        $tenant = $this->providers[$connectionName]->getTenant();

        if(null === $tenant) {
            throw new TenantNotFoundException($tenantId, $connectionName);
        }

        try {
            $this->connections[$connectionName]->forceSwitch($tenant);
        } catch (ConnectionException $e) {
            if(!strstr($e->getMessage(), 'Unknown database')) {
                throw $e;
            }
            return;
        }

        $this->dispatcher->dispatch(PostSwitchConnection::NAME, new PostSwitchConnection(
            $tenant,
            $tenantId,
            $this->connections[$connectionName]
        ));
    }
}
