<?php
namespace Phpnova\Database\Settings;

use DateTime;
use DateTimeZone;
use Exception;
use PDO;
use Phpnova\Database\DBError;
use Throwable;

class ConnectionConfig
{
    public function __construct(private PDO $pdo, private array $config)
    {
        if (array_key_exists('timezone', $config)) {
            $this->setTimezone($config['timezone']);
        }
    }

    /**
     * Set the database time zone
     * @param string $timezone Example -05:00 or America/Bogota
     */
    public function setTimezone(string $timezone): void
    {
        try {
            if (!strlen($timezone) == 6 || !preg_match('/[\+,\-]\d{2}:\d{2}/', $timezone)) {
                try {
                    $timezone = (new DateTime('now', new DateTimeZone($timezone)))->format('P');
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            if ($this->getDriver() == "mysql") {
                $sql = "SET TIME_ZONE = '$timezone'";
                $this->pdo->exec($sql);
            }
        } catch (Throwable $th) {
            throw new DBError(new Exception("Error al establecer la zona horaria\n\n: " . $th->getMessage() . "\n\nSQL: $sql"));
        }
    }

    public function getTimezone(): ?string
    {
        return $this->config['time_zone'] ?? null;
    }

    public function getDriver(): string
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @param 'camelcase' | 'snakecase'
     */
    public function setWritingStyleQueryFields(string $writingStyle): void
    {
        if ($writingStyle != "camelcase" && $writingStyle != "snakecase") throw new DBError("Solo se adminten los valores 'camelcase' o 'snakecase'");
        $this->config['writhing_style_fields_querys'] = $writingStyle;
    }

    /**
     * @param 'camelcase' | 'snakecase'
     */
    public function getWritingStyleQueryFields(): ?string
    {
        return $this->config['writhing_style_fields_querys'] ?? null;
    }

    /**
     * @param 'camelcase' | 'snakecase'
     */
    public function setWritingStyleResultFields(string $writingStyle): void
    {
        if ($writingStyle != "camelcase" && $writingStyle != "snakecase") throw new DBError("Solo se adminten los valores 'camelcase' o 'snakecase'");
        $this->config['writhing_style_fields_result'] = $writingStyle;
    }

    /**
     * @param 'camelcase' | 'snakecase'
     */
    public function getWritingStyleResultFields(): ?string
    {
        return $this->config['writhing_style_fields_result'] ?? null;
    }
}