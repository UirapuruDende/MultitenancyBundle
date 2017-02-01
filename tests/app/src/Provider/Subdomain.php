<?php
namespace Tests\App\Provider;

use Dende\MultitenancyBundle\DTO\Tenant;
use Dende\MultitenancyBundle\Provider\TenantProviderInterface;

class Subdomain implements TenantProviderInterface
{
    /**
     * @return Tenant
     */
    public function getTenant() {
        return new Tenant('username', 'password', 'database', 'host');
    }
}