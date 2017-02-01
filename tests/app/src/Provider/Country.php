<?php
namespace Tests\App\Provider;

use Dende\MultitenancyBundle\DTO\Tenant;
use Dende\MultitenancyBundle\Provider\TenantProviderInterface;

class Country implements TenantProviderInterface
{
    /**
     * @return Tenant
     */
    public function getTenant() {
        return;
    }
}