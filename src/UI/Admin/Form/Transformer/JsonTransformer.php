<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms between JSON string (view) and array (model).
 *
 * @implements DataTransformerInterface<array<mixed>, string>
 */
class JsonTransformer implements DataTransformerInterface
{
    /**
     * Transform model (array) → view (string).
     *
     * @param array<mixed>|null $value
     *
     * @throws \JsonException
     */
    public function transform(mixed $value): string
    {
        if (empty($value)) {
            return (string) json_encode([]);
        }

        return (string) json_encode($value);
    }

    /**
     * Transform view (string) → model (array).
     *
     * @return array<mixed>
     *
     * @throws \JsonException
     */
    public function reverseTransform(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }

        /** @var array<mixed> $decoded */
        $decoded = json_decode((string) $value, true, 512, \JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
