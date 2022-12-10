<?php
namespace Phpnova\Database\Settings;

use Phpnova\Database\Client;
use Phpnova\Database\db;
use Phpnova\Database\DBConnection;
use Phpnova\Database\DBError;

class Config
{
    private static array $connections = [];
    public function connect()
    {

    }

    public function registerConnection(string $name, DBConnection $connection): void
    {
        db::addConnection($name, $connection);
    }

    public function getConnection(string $name): DBConnection
    {
        return self::$connections[$name] ?? throw new DBError("No se contro una conexión");
    }
}