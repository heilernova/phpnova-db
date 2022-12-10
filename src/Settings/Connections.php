<?php
namespace Phpnova\Database\Settings;

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

    public function register($name, DBConnection $connection): void
    {
        $this->connections[$name] = $connection;
    }

    public function setDefault(string $name): void
    {
        if (!array_key_exists($name, $this->connections)) throw new DBError("No es entro la conexón [$name] en el registro");
        $this->nameDefault = $name;
    }
}