<?php
namespace Dende\MultitenancyBundle\Connection;

use Dende\MultitenancyBundle\DTO\Tenant;
use Doctrine\DBAL\Connection;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;

class Wrapper extends Connection
{
    /**
     * @var bool
     */
    private $isConnected = false;

    /**
     * @var array
     */
    protected $_params = [];

    /**
     * @param array $params
     * @param Driver $driver
     * @param Configuration|null $config
     * @param EventManager|null $eventManager
     */
    public function __construct(
        array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null
    ) {
        $this->_params = $params;
        parent::__construct($params, $driver, $config, $eventManager);
    }
    /**
     * @param $host
     * @param $dbname
     * @param $username
     * @param $password
     */
    public function forceSwitch(Tenant $tenant, ?bool $connect = true)
    {
        if ($this->isConnected()) {
            $this->close();
        }

        list($username, $password, $dbname, $host, $path) = array_values($tenant->getArray());

        $this->_params['host'] = $host;
        $this->_params['dbname'] = $dbname;
        $this->_params['user'] = $username;
        $this->_params['password'] = $password;
        $this->_params['path'] = $path;

        if($connect) {
            $this->connect();
        }
    }

    public function connect() : bool
    {
        if ($this->isConnected()) {
            return true;
        }

        $this->_conn = $this->_driver->connect(
            $this->_params,
            $this->_params['user'],
            $this->_params['password'],
            $this->_params['driverOptions']
        );

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $eventArgs = new ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        $this->isConnected = true;

        return true;
    }

    public function isConnected() : bool
    {
        return $this->isConnected;
    }

    public function close() : void
    {
        if ($this->isConnected()) {
            parent::close();
            $this->isConnected = false;
        }
    }

    public function getParams() : ?array
    {
        return $this->_params;
    }
}
