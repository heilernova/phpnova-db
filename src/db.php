<?php

namespace Phpnova\Database;

use Exception;
use PDO;
use Phpnova\Database\Connect;

class db
{
    private static Client|null $default = null;
    private static array $connections = [];

    public static function connect(): Connect
    {
        return new Connect();
    }

    public static function registerConnection(string $name, Client $client): void
    {
        self::$connections[$name] = $client;
    }

    public static function setConnection(Client $client): void 
    {
        self::$default = $client;
    }

    public static function getConnection(string $name = null): Client
    {
        if ($name && array_key_exists($name, self::$connections)) {
            throw new ErrorDatabase( new Exception("No se entro la conexión [$name] en el registro"));
        }

        if (is_null(self::$default)) throw new ErrorDatabase(new Exception('No se ha establecido una conexión por defecto'));

        return self::$default;
    }

    
    public static function query(string $sql, array $params = null): Result
    {
        try {
            return self::getConnection()->query($sql, $params);
        } catch (\Throwable $th) {
            return new ErrorDatabase($th);
        }
    }

    public static function table(string $name): Table
    {
        return self::getConnection()->table($name);
    }
}