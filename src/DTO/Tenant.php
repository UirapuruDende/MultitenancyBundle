<?php
namespace Dende\MultitenancyBundle\DTO;

class Tenant
{
    /** @var  string */
    protected $username;

    /** @var  string */
    protected $password;

    /** @var  string */
    protected $dbname;

    /** @var  string */
    protected $host;

    /**
     * Tenant constructor.
     * @param $username
     * @param $password
     * @param $dbname
     * @param $host
     */
    public function __construct($username, $password, $dbname, $host)
    {
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->host = $host;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'dbname'   => $this->dbname,
            'host'     => $this->host
        ];
    }
}