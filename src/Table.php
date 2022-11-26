<?php

namespace Phpnova\Database;

/**
 * @author Heiler Nova <https://github.com/heilernova>
 */
class Table
{
    private string $table = "";
    public function __construct(private Client $client)
    {
        
    }

    public function __call($name, $arguments)
    {
        if ($name == "setTableName" && is_string($arguments[0])){
            $this->table = $arguments[0];
        }
    }

    /**
     * @param string $condition Data condition, whithout !WHERE¡
     * @param ?array $params Condition parameters
     * @return object|null Returns the first result of the query or null if there are no results.
     */
    public function get(string $condition, ?array $params = null): ?object
    {
        try {
            $sql = "SELECT * FROM `$this->table` WHERE $condition LIMIT 1";
            return $this->client->query($sql, $params)->rows[0] ?? null;
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * @param ?string $condition Condition for the data result, whithout !WHERE¡
     * @param ?array $params Condition parameters
     * @return object[] Returns an array of objects with the information of each row
     */
    public function getAll(string $condition = null, ?array $params = null): array
    {
        try {
            $sql = "SELECT * FROM `$this->table`";
            if ($condition) $sql .= " WHERE $condition";
            return $this->client->query($sql, $params)->rows;
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * Execute a data insert
     * @param array $values Associative array of data to insert
     */
    public function insert(array $values, string $returning = null): bool|object
    {
        try {
            $table = $this->table;
            $values = (array)$values;
            $fields = "";
            $values_sql = "";
            $params = [];
            foreach ($values as $key => $val) {
                $write_style = $this->client->getConfig()->getWritengStyleQuery();
                if ($write_style) {
                    $key = $write_style == 'snakecase' ? nv_db_parse_camelcase_to_snakecase($key) : nv_db_parse_snakecase_to_camelcase($key);
                }

                $fields .= ", `$key`";
                if (is_bool($val)) {
                    $values_sql .= ", " . ($val ? 'TRUE' : 'FALSE');
                    continue;
                }

                $params[$key] = $val;
                $values_sql .= ", :$key";
            }

            $values_sql = ltrim($values_sql, ', ');
            $fields = ltrim($fields, ', ');

            $res = $this->client->query("INSERT INTO $table($fields) VALUES($values_sql)" . ($returning ? " RETURNING $returning"  : "") , $params);
            return $res->rows[0] ?? true;
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * Run a update
     * @param array $values Associative array of data to update
     * @param string $condition Condition to update the data, whithout !WHERE¡
     * @param ?array $params Condition parameters
     */
    public function update(array $values, string $condition, ?array $params = null): Result
    {
        try {
            $table = $this->table;
            $values = (array)$values;
            $sql_values = "";
            $sql_parms = [];

            foreach($values as $key => $val) {
                $write_style = $this->client->getConfig()->getWritengStyleQuery();
                if ($write_style) {
                    $key = $write_style == 'snakecase' ? nv_db_parse_camelcase_to_snakecase($key) : nv_db_parse_snakecase_to_camelcase($key);
                }

                if (is_bool($val)) {
                    $sql_values .= ", `$key` = " . ($val ? 'TRUE' : 'FALSE');
                    continue;
                }

                $sql_parms[$key] = $val;
                $sql_values .= ", `$key` = :$key";
            }

            $sql_values = ltrim($sql_values, ', ');

            # Generamos la condición.
            $sql_condition = $condition;
            $sql_condition_params = [];

            if (str_contains($condition, '?')) {
                $index = -1;
                $sql_condition = preg_replace_callback("/\?/", function($matches) use (&$index) {
                    $index++;
                    return ":$index";
                }, $condition);
            }

            $sql_condition = str_replace(':', ':pw_', $sql_condition);

            foreach($params ?? [] as $key => $val) {
                $sql_condition_params["pw_$key"] = $val;
            }
            
            $res = $this->client->query("UPDATE `$table` SET $sql_values WHERE $sql_condition", array_merge($sql_parms, $sql_condition_params));

            return $res;

        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }

    /**
     * Run a delete
     * @param string $condition Condition to delete the data whithout !WHERE¡
     * @param ?array $params Condition parameters
     * @return int Number of rows delete
     */
    public function delete(string $condition, ?array $params = null): int
    {
        try {
            $res = $this->client->query("DELETE FROM `$this->table` WHERE $condition", $params);
            return $res->rowCount;
        } catch (\Throwable $th) {
            throw new ErrorDatabase($th);
        }
    }
}