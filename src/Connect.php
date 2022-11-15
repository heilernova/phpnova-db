<?php
namespace Phpnova\Database;

use Exception;
use PDO;
use Phpnova\Database\ErrorDatabase;
use Phpnova\Nova\Bin\ErrorCore;

class Connect
{
    public function __construct(private bool $set_dafault = true)
    {
        
    }

    private function setDefault(PDO $pdo, string $timezone = null): Client
    {
        $config = [];
        if ($timezone) $config['timezone'] = $timezone;
        $client = new Client($pdo, $config);

        db::setConnection($client);

        return $client;
    }

    /**
     * Crea una conexión PDO a MYSQL con los parametros ingresado
     */
    public function mysql(string $username, string $password, string $database, string $hostname = 'localhost', string $port = null, ?string $timezone = null): Client
    {
        try {
            return $this->setDefault(new PDO("mysql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password), $timezone);
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * Crea una conexión PDO a PostgreSQL con los parametros ingresados
     */
    public function postgreSQL(string $username, string $password, string $database, string $hostname = 'localhost', $port = null, ?string $timezone = null): Client
    {
        try {
            return $this->setDefault(new PDO("pgsql:host=$hostname; dbname=$database;" . ($port ? " port=$port;" : ''), $username, $password), $timezone);
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * Crae una conexión PDO para Microsft Access con el parametro ingreado
     */
    public function microsftAccess(string $path)
    {
        try {
            //code...
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }
}