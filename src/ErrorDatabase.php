<?php

namespace Phpnova\Database;

use Exception;
use Throwable;

class ErrorDatabase extends Exception
{
    public function __construct(string|Throwable $th)
    {
        if (is_string($th)){
            $this->message = $th;
        } else {
            $this->message = $th->getMessage();
            $this->code = $th->getFile();
        }

        # Modificamos el archivo y la linea para que muestre el donde se ejecuta la función que crea el error
        $backtrace = debug_backtrace()[1] ?? null;
        if ($backtrace) {
            $this->file = $backtrace['file'];
            $this->line = $backtrace['line'];
        }
    }
}