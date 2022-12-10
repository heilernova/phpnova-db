<?php

namespace Phpnova\Database;

use Exception;
use Throwable;

class DBError extends Exception
{
    public function __construct(string|Throwable $value)
    {
        if (is_string($value)){
            $this->message = $value;
        } else {
            $this->message = $value->getMessage();
            $this->code = $value->getFile();
        }

        # Modificamos el archivo y la linea para que muestre el donde se ejecuta la función que crea el error
        $backtrace = debug_backtrace()[1] ?? null;
        if ($backtrace) {
            $this->file = $backtrace['file'];
            $this->line = $backtrace['line'];
        }
    }
}