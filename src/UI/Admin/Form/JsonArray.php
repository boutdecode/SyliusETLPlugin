<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form;

final class JsonArray
{
    /**
     * Decodes a JSON object or list typed as the etl-core models do.
     *
     * @return array<string, mixed>
     */
    public static function decode(mixed $json): array
    {
        if (!\is_string($json)) {
            return [];
        }

        $decoded = json_decode($json, true);
        if (!\is_array($decoded)) {
            return [];
        }

        return array_combine(array_map('strval', array_keys($decoded)), $decoded);
    }
}
