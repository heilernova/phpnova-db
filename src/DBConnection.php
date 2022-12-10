<?php
namespace Phpnova\Database;

use PDO;
use Phpnova\Database\Queries\DBQueries;
use Phpnova\Database\Settings\ConnectionConfig;

class DBConnection
{
    private readonly PDO $pdo;
    private readonly DBTable $table;
    // public readonly DBQueries $execute;
    public readonly ConnectionConfig $config;


    public function __construct(PDO $pdo, array $config = [])
    {
        $this->pdo = $pdo;
        $this->config = new ConnectionConfig($pdo, $config);
        $this->table = new DBTable($this);
        // $this->execute = new DBQueries($this);
    }

    /**
     * @throws DBError If there is already a transaction started or the driver does not support transactions
     */
    public function beginTransaction(): bool
    {
        try {
            return $this->pdo->beginTransaction();
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    /**
     * Commits changes to the database if a transaction has been started
     * @throws DBError — if there is no active transaction.
     */
    public function commid(): bool
    {
        try {
            return $this->pdo->commit();
        } catch (\Throwable $th) {
            throw new DBError($th);
        }
    }

    public function query(string $sql, ?array $params = null): DBResult
    {
        if ($this->config->getDriver() == 'pgsql') $sql = str_replace("`", '"', $sql);
        
        # Combert parameters objects and arrays to json format
        if ($params) {
            foreach ($params as &$val){
                if (is_object($val) || is_array($val)) $val = json_encode($val);
            }
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute($params);

            return new DBResult($stmt, $this->config);

        } catch (\Throwable $th) {
            $message = "Problemas al ejecutar cunsulta SQL \n\n";
            $message .= "Message: " . $th->getMessage();
            $message .= "\nSQL: $sql";
            if ($params) {
                $message .= "\nParameters: ";
                foreach ($params as $key => $val) {
                    $message .= "\n - $key : " . gettype($val) . " => $val ";
                }
            }
            throw new DBError($message);
        }
    }

    public function table(string $name): DBTable
    {
        $this->table->setTableName($name);
        return $this->table;
    }
}