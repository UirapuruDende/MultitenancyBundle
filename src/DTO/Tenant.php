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

    /** @var  string */
    protected $path;

    /**
     * Tenant constructor.
     * @param $username
     * @param $password
     * @param $dbname
     * @param $host
     */
    public function __construct($username, $password, $dbname, $host, $path = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->host = $host;
        $this->path = $path;
    }

    /**
     * @param $array
     * @return Tenant
     */
    public static function fromArray(array $array = [])
    {
        return new self($array["username"], $array["password"], $array["dbname"], $array["host"], $array["path"]);
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
            'host'     => $this->host,
            'path'     => $this->path,
        ];
    }
}