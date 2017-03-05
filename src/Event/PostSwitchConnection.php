<?php
namespace Dende\MultitenancyBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Dende\MultitenancyBundle\DTO\Tenant;
use Dende\MultitenancyBundle\Connection\Wrapper;

class PostSwitchConnection extends Event
{
    const NAME = 'multitenancy.post_switch_connection';

    /**
     * @var Tenant
     */
    private $tenant;

    /**
     * @var string
     */
    private $tenantId;

    /**
     * @var Wrapper
     */
    private $connection;

    /**
     * SwitchConnection constructor.
     * @param Tenant $tenant
     * @param string $tenantId
     * @param Wrapper $connection
     */
    public function __construct(Tenant $tenant, $tenantId, Wrapper $connection)
    {
        $this->tenant = $tenant;
        $this->tenantId = $tenantId;
        $this->connection = $connection;
    }

    /**
     * @return Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * @return string
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * @return Wrapper
     */
    public function getConnection()
    {
        return $this->connection;
    }
}