<?php
namespace Phpnova\Database\Settings;

use PDO;
use Phpnova\Database\DBConnection;
use Phpnova\Database\DBError;

class Connect
{
    private function setDefault(PDO $pdo, string $timezone = null): DBConnection
    {
        $config = [];
        if ($timezone) $config['timezone'] = $timezone;
        $client = new DBConnection($pdo, $config);
        return $client;
    }

    /**
     * Crea una conexión PDO a MYSQL con los parametros ingresado
     */
    public function mysql(string $username, string $password, string $database, string $hostname = 'localhost', string $port = null, ?string $timezone = null, ?array $options = null): DBConnection
    {
        try {
            return $this->setDefault(new PDO("mysql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password, $options), $timezone);
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /**
     * Crea una conexión PDO a PostgreSQL con los parametros ingresados
     */
    public function postgreSQL(string $username, string $password, string $database, string $hostname = 'localhost', $port = null, ?string $timezone = null, ?array $options = null): DBConnection
    {
        try {
            return $this->setDefault(new PDO("pgsql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password, $options), $timezone);
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /**
     * Crae una conexión PDO para Microsft Access con el parametro ingreado
     * @param string $path path of the access file location
     */
    public function microsftAccess(string $path): DBConnection
    {
        try {
            return $this->setDefault(new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)};charset=UTF-8; DBQ=$path; Uid=; Pwd=;"));
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    public function pdo(PDO $pdo, ?string $timezone = null): DBConnection
    {
        return $this->setDefault($pdo, $timezone);
    }
}