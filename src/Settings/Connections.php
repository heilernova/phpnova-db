<?php
namespace Phpnova\Database\Settings;

use PDO;
use Phpnova\Database\db;
use Phpnova\Database\DBConnection;
use Phpnova\Database\DBError;

class Connections
{
    /**
     * @var DBConnection[]
     */
    private array $connections = [];
    private ?string $nameDefault = null;

    public function getDefault(): DBConnection
    {
        $conn = false;
        if (is_null($this->nameDefault)){
            $conn = current($this->connections);
        } else {
            $conn = $this->connections[$this->nameDefault] ?? false;
        }

        if ($conn == false) throw new DBError("No hay una conexión por default");

        return $conn;
    }

    public function get(string $name): DBConnection
    {
        try {
            return $this->connections[$name];
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /** 
     * Register a connection to be used
     * @param string $name Name that is assigned to the connection for its future call
     * @param DBConnection|PDO Connection, can be a previously created connection with a PDO object
    */
    public function register(string $name, DBConnection|PDO $connection): void
    {
        if ($connection instanceof PDO){
            $connection = db::connect()->pdo($connection);
        }
        $this->connections[$name] = $connection;
    }

    /**
     * Establishes the default conection to perform SQL queries
     * @param string $name Connection name
     * @throws DBError Returns an error in case the connection name is not found in the registry
     */
    public function setDefault(string $name): void
    {
        if (!array_key_exists($name, $this->connections)) throw new DBError("No es entro la conexón [$name] en el registro");
        $this->nameDefault = $name;
    }
}