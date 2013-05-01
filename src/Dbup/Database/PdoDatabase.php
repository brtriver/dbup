<?php
namespace Dbup\Database;

use Dbup\Exception\RuntimeException;

class PdoDatabase
{
    private $connection = null ;
    private $dsn;
    private $user;
    private $password;
    private $driverOptions;

    public function __construct($dsn, $user, $password, $driverOptions = [])
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->driverOptions = $driverOptions;
    }

    public function connection($new = false){
        if (null === $this->connection || true === $new) {
            try {
                $this->connection = new \PDO($this->dsn, $this->user, $this->password, $this->driverOptions);
                $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch(\PDOException $e) {
                throw new RuntimeException($e->getMessage());
            }
        }
        return $this->connection;
    }
}