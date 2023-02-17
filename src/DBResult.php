<?php
namespace Phpnova\Database;

use PDO;
use PDOStatement;
use Phpnova\Database\Settings\ConnectionConfig;

class DBResult
{
    public readonly array $fields;

    /** Number of affected rows */
    public readonly int $rowCount;

    /**
     * Returns an array with the result of the rows in an associated array
     * @var array[]
     * */
    public readonly array $rows;

    public function __construct(PDOStatement $stmt , ConnectionConfig  $config)
    {
        $this->rowCount = $stmt->rowCount();
        $this->rows = $stmt->fetchAll(PDO::FETCH_FUNC, function() use ($stmt, $config) {
            return nv_db_parce_result(func_get_args(), $stmt, $config);
        });

        $fields = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) { 
            $meta = $stmt->getColumnMeta($i);
            if ($meta) $fields[] = $meta['name'];
        }

        $this->fields = $fields;
    }
}