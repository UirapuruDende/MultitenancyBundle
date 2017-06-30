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

    /**
     * TenantManager constructor.
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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

    /**
     * @param string $connectionName
     * @param string $tenantId
     */
    public function switchConnection($connectionName, $tenantId = null)
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
