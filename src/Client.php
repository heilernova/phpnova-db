<?php
namespace Phpnova\Database;

use DateTime;
use DateTimeZone;
use Exception;
use PDO;
use PDOStatement;
use Throwable;

class Client 
{
    private PDO $pdo;
    private Table $table;
    private array $config = [];
    private ClientConfig $_config;

    public function __construct(PDO $pdo, array $config = [])
    {
        $this->pdo = $pdo;
        $config = array_merge(['driver' => $pdo->getAttribute($pdo::ATTR_DRIVER_NAME)], $config);
        
        $this->config = $config;
        $this->_config = new ClientConfig($this->config);
        
        # Validamos la configuración
        if (array_key_exists('timezone', $config)) {
            $this->setTimezone($config['timezone']);
        }
        
        $this->table = new Table($this);
    }

    public function getConfig(): ClientConfig {
        return $this->_config;
    }

    public function setTimezone(string $timezone): void
    {
        try {
            if (!strlen($timezone) == 4 || !preg_match('/[\+,\-]\d{2}:\d{2}/', $timezone)) {
                try {
                    $timezone = (new DateTime('now', new DateTimeZone($timezone)))->format('P');
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            if ($this->config['driver']) {
                $sql = "SET TIME_ZONE = '$timezone'";
                $this->pdo->exec($sql);
            }
        } catch (Throwable $th) {
            throw new ErrorDatabase(new Exception("Error al establecer la zona horaria\nMessage: " . $th->getMessage() . "\nSQL: $sql"));
        }
    }

    private function exec(string $sql, array $params = null): PDOStatement|false
    {
        $stmt = $this->pdo->prepare($sql);

        if ($params){
            foreach ($params as $key => $val) {
                if (is_array($val) || is_object($val)) $params[$key] = json_encode($val);
            }
        }
        
        try {
            $stmt->execute($params);
            return $stmt;
        } catch (\Throwable $th) {
            $message = "# Error al ejecutar las cunsulta SQL \n\n";
            $message .= "Message: " . $th->getMessage();
            $message .= "\nSQL: $sql";
            if ($params) {
                $message .= "\nParameters: ";
                foreach ($params as $key => $val) {
                    $message .= "\n - $key : " . gettype($val) . " => $val ";
                }
            }
            throw new Exception($message, (int)$th->getCode(), $th);
        }

        return false;
    }

    public function query(string $sql, array $params = null)
    {
        try {
            $res = $this->exec($sql, $params);
            return new Result( $res, $this->config);
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    public function table(string $table): Table
    {
        $this->table->setTableName($table);
        return $this->table;
    }
}