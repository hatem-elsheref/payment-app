<?php


namespace Framework;
use PDO;
use PDOException;
use Exception;

class Database
{
    private array $configurations = [];
    public static $connectionHandler = null;

    public function __construct(array $configurations = [])
    {
        $this->configurations = $configurations;
       // $this->connection();
    }

    private function prepareDsn()
    {
        return 'mysql:host=' . $this->configurations['hostname'] . ';dbname=' . $this->configurations['db_name'];
    }


    private function connection()
    {
        try{
            self::$connectionHandler = new PDO($this->prepareDsn(), $this->configurations['db_user'], $this->configurations['db_pass']);
        }catch (PDOException $exception){
            throw new Exception($exception->getMessage());
            exit(0);
        }

        return  self::$connectionHandler;

    }


}