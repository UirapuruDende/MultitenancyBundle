<?php
namespace Tests\App\Provider;

use Dende\MultitenancyBundle\DTO\Tenant;
use Dende\MultitenancyBundle\Provider\TenantProviderInterface;
use Exception;

class Subdomain implements TenantProviderInterface
{
    /** @var string */
    private $subdomain;

    /** @var array */
    private $storage;

    /**
     * Subdomain constructor.
     * @param array $storage
     */
    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }

    public function setTenantId($id)
    {
        $this->subdomain = $id;
    }

    /**
     * @return Tenant
     */
    public function getTenant() {

        if($this->subdomain === null) {
            throw new Exception('No subdomain set, use setTenantId() before getTenant()');
        }

        $data = $this->storage[$this->subdomain];
        return Tenant::fromArray($data);
    }
}