<?php
namespace Phpnova\Database;

use PDO;
use PDOStatement;

class Result
{
    public readonly array $fields;
    /** Number of affected rows */
    public readonly int $rowCount;
    public readonly array $rows;

    public function __construct(PDOStatement $stmt , array $config)
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