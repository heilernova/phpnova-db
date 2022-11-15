<?php

/**
 * Parce the values
 */
function nv_db_parce_result(array $values, PDOStatement $stmt, array $config)
{
    $params = [];
    foreach ($values as $key => $val) {
        $column_meta = $stmt->getColumnMeta($key);
        $native_type = $column_meta['native_type'];

        foreach ($values as $index => $value) {
            $column_meta = $stmt->getColumnMeta($index);
            $native_type = $column_meta['native_type'];

            if ($config['driver'] == 'mysql') {
                if ($native_type == 'NEWDECIMAL'){
                    $value = (float)$value;
                } else if ($native_type == 'BLOB' || $native_type == 'VAR_STRING'){
                    if (is_string($value)){
                        if ( preg_match('/^\{?.+\}/', $value) > 0 || preg_match('/^\[?.+\]/', $value ) > 0){
                            $json = json_decode($value);
                            if (json_last_error() == JSON_ERROR_NONE){
                                $value = $json;
                            }
                        }
                    }
                }
            }

            if ($config['driver'] == 'pgsql') {
                if ($native_type == 'numeric'){
                    $value = (float)$value;
                }

                if ($native_type == 'json'){
                    $value = json_decode($value);
                }
            }

            $name_field = $column_meta['name'];
            $writing_style = $config['writing_style']['results'] ?? null;

            if ($writing_style == 'camelcase') $name_field = nv_db_parse_camelcase_to_snakecase($name_field);
            if ($writing_style == 'snakecase') $name_field = nv_db_parse_snakecase_to_camelcase($name_field);

            $params[$name_field] = $value;
        }
    }
    return (object)$params;
}