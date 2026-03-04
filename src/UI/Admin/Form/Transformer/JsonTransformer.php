<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonTransformer implements DataTransformerInterface
{

    /** @throws \JsonException */
    public function transform(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    public function reverseTransform(mixed $value): string
    {
        if (empty($value)) {
            return json_encode([]);
        }

        return json_encode($value);
    }
}
