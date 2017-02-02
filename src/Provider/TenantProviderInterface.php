<?php
namespace Dende\MultitenancyBundle\Provider;

use Dende\MultitenancyBundle\DTO\Tenant;

interface TenantProviderInterface
{
    /**
     * @return Tenant
     */
    public function getTenant();

    /**
     * @param $id
     * @return void
     */
    public function setTenantId($id);
}