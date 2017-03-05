<?php
namespace Dende\MultitenancyBundle\Exception;

use Exception;

class TenantNotFoundException extends Exception
{
    protected $messageTemplate = 'Tenant \'%s\' not found';

    protected $tenantId;

    protected $connectionName;

    /**
     * TenantNotFoundException constructor.
     * @param $tenantId
     * @param $connectionName
     */
    public function __construct($tenantId, $connectionName)
    {
        $this->tenantId = $tenantId;
        $this->connectionName = $connectionName;

        $this->message = sprintf($this->messageTemplate, $tenantId, $connectionName);
    }

    /**
     * @return string
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * @return int
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }
}