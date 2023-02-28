<?php

use Phpnova\Database\Settings\ConnectionConfig;

/**
 * Parce the values
 */
function nv_db_parce_result(array $values, PDOStatement $stmt, ConnectionConfig $config)
{
    $params = [];
    foreach ($values as $index => $value) {
        $column_meta = $stmt->getColumnMeta($index);
        $native_type = $column_meta['native_type'];
        if ($config->getDriver() == 'mysql') {
            if ($native_type == 'NEWDECIMAL'){
                $value = (float)$value;
            }
            else if (is_string($value) && (preg_match('/^\{?.+\}/', $value) > 0 || preg_match('/^\[?.+\]/', $value ) > 0)){
                $json = json_decode($value);
                if (json_last_error() == JSON_ERROR_NONE){
                    $value = $json;
                }
            }
        }

        if ($config->getDriver() == 'pgsql') {
            if ($native_type == 'numeric'){
                $value = (float)$value;
            }

            if ($native_type == 'json'){
                $value = json_decode($value);
            }
        }

        $name_field = $column_meta['name'];
        if ($config->getWritingStyleResultFields() == 'camelcase') $name_field = nv_db_parse_snakecase_to_camelcase($name_field);
        if ($config->getWritingStyleResultFields() == 'snakecase') $name_field = nv_db_parse_camelcase_to_snakecase($name_field);

        $params[$name_field] = $value;
    }
    return (object)$params;
}