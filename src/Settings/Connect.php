<?php
namespace Phpnova\Database\Settings;

use PDO;
use Phpnova\Database\DbConnection;
use Phpnova\Database\DBError;

class Connect
{
    private function setDefault(PDO $pdo, string $timezone = null): DbConnection
    {
        $config = [];
        if ($timezone) $config['timezone'] = $timezone;
        $client = new DbConnection($pdo, $config);
        return $client;
    }

    /**
     * Crea una conexión PDO a MYSQL con los parametros ingresado
     */
    public function mysql(string $username, string $password, string $database, string $hostname = 'localhost', string $port = null, ?string $timezone = null): DbConnection
    {
        try {
            return $this->setDefault(new PDO("mysql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password), $timezone);
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /**
     * Crea una conexión PDO a PostgreSQL con los parametros ingresados
     */
    public function postgreSQL(string $username, string $password, string $database, string $hostname = 'localhost', $port = null, ?string $timezone = null): DbConnection
    {
        try {
            return $this->setDefault(new PDO("pgsql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password), $timezone);
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /**
     * Crae una conexión PDO para Microsft Access con el parametro ingreado
     * @param string $path path of the access file location
     */
    public function microsftAccess(string $path): DbConnection
    {
        try {
            return $this->setDefault(new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)};charset=UTF-8; DBQ=$path; Uid=; Pwd=;"));
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    public function pdo(PDO $pdo, ?string $timezone = null): DbConnection
    {
        return $this->setDefault($pdo, $timezone);
    }
}