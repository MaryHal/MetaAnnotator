<?php

namespace meta;

require_once 'DbSettings.php';

class Database
{
    public static function makeConnection()
    {
        try
        {
            $host = DB_SERVER;
            $name = DB_NAME;
            $pdo = new \PDO("pgsql:host={$host};port=5432;dbname={$name};", DB_USER, DB_PASSWORD);

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $pdo;
        }
        catch (\PDOException $e)
        {
            echo __LINE__, $e->getMessage();
            die($e->getMessage());
        }
    }
}

?>