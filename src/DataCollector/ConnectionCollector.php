<?php
namespace Dende\MultitenancyBundle\DataCollector;

use Doctrine\DBAL\Connection;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ConnectionCollector extends DataCollector
{
    /** @var Connection */
    private $connection;

    /**
     * MultiDbCollector constructor.
     * @param $default
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Exception|null $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        if($this->connection->isConnected()) {
            $this->data['dbname'] =  $this->connection->getDatabase();
            $this->data['username'] =  $this->connection->getUsername();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'database_connections';
    }
}
