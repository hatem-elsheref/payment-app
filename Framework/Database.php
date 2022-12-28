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
        $this->connection();
    }

    private function prepareDsn()
    {
        return 'mysql:host=' . $this->configurations['hostname'] . ';dbname=' . $this->configurations['db_name'];
    }


    private function connection()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try{
            self::$connectionHandler = new PDO($this->prepareDsn(), $this->configurations['db_user'], $this->configurations['db_pass'], $options);
        }catch (PDOException $exception){
            throw new Exception($exception->getMessage());
            exit(0);
        }

        return  self::$connectionHandler;

    }


}