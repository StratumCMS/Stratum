<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class EnvEditor {
    public static function updateEnv(array $values, ?string $path = null): void {
        $envPath = $path ?? app()->environmentFilePath();

        if (!File::exists($envPath)) {
            throw new RuntimeException("Fichier .env manquant ${envPath}");
        }

        $content = File::get($envPath);

        foreach($values as $key => $value) {
            $oldValue = self::getCurrentValue($content, $key);
            $espacedValue = self::escapeValue($value);

            if ($oldValue !== null) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$espacedValue}",
                    $content
                );
            }else{
                $content .= "\n{$key}={$espacedValue}";
            }
        }

        File::put($envPath, $content);
    }

    protected static function getCurrentValue(string $content, string $key): ?string {
        if (preg_match("/^{$key}=([^\r\n]*)/m", $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected static function escapeValue($value): string
    {
        $value = (string) $value;

        if (str_contains($value, ' ') || str_contains($value, '#')) {
            return "\"{$value}\"";
        }

        return $value;
    }

}
