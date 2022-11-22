<?php
namespace Phpnova\Database;

use Exception;

class ClientConfig
{
    public function __construct(private array &$config)
    {
        
    }

    /**
     * @param 'camelcase'|'snakecase'
     */
    public function setWritengStyleResult(string $style): void
    {
        if ($style != "camelcase" && $style != "snakecase") throw new ErrorDatabase("El parametro ingresado solo pude ser 'camelcase' o 'snakecase' no [$style]");
        $this->config['writing_style']['results'] = $style;
    }

    /**
     * @param 'camelcase'|'snakecase'
     */
    public function setWritengStyleQuery(string $style): void
    {
        if ($style != "camelcase" && $style != "snakecase") throw new ErrorDatabase("El parametro ingresado solo pude ser 'camelcase' o 'snakecase' no [$style]");
        $this->config['writing_style']['queries'] = $style;
    }
}