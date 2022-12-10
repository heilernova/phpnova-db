<?php

namespace Phpnova\Database;

use Exception;
use PDO;
use Phpnova\Database\Settings\Connect;
use Phpnova\Database\Settings\Connections;

class db
{
    private static Connections $connections;
    private static Connect $connect;

    public static function connect(): Connect
    {
        return self::$connect;
    }

    public static function connections(): Connections
    {
        return self::$connections;
    }

    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case "setConnection":
                self::$connections = $arguments[0];
                return;
            case "setConnect":
                self::$connect = $arguments[0];
                return;
            default:
                throw new DBError("Método estatico no soportado");
        }
    }
}

db::setConnection(new Connections());
db::setConnect(new Connect());