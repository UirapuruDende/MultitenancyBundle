<?php
namespace Dende\MultitenancyBundle\Provider;

use Dende\MultitenancyBundle\DTO\Tenant;

interface TenantProviderInterface
{
    public function getTenant() : ?Tenant;

    public function setTenantId(string $id) : void;
}