<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\ETL\Infrastructure\Step\Transformer;

use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\AbstractTransformerStep;
use Flow\ETL\Row;

use Flow\ETL\Row\Entry\StringEntry;
use function Flow\ETL\DSL\array_to_row;
use function Flow\ETL\DSL\data_frame;
use function Flow\ETL\DSL\from_array;
use function Flow\ETL\DSL\to_array;
use function Symfony\Component\String\s;

class ImportProductMapperTransformer extends AbstractTransformerStep
{
    public const CODE = 'etl.transformer.import_product_mapper';

    protected string $code = self::CODE;

    public function transform(mixed $data, array $configuration = []): array
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        /** @var array<int, array<string, mixed>> $result */
        $result = [];
        data_frame()
            ->read(from_array($data))
            ->collect()
            ->map(function (Row $line) {
                $schema = [
                    'product' => [],
                    'variant' => [],
                ];

                $line->entries()->map(function (StringEntry $f) use (&$schema) {
                    $split = s($f->name())->split(':');

                    if (count($split) === 3) {
                        [$type, $field, $subField] = $split;
                        if (null != $f->value()) {
                            $schema[$type->toString()][$field->toString()][$subField->toString()] = $f->value();
                        }
                    }

                    if (count($split) == 2) {
                        [$type, $field] = $split;
                        if (null != $f->value()) {
                            $schema[$type->toString()][$field->toString()] = $f->value();
                        }
                    }

                    return $schema;
                });

                return array_to_row($schema);
            })
            ->write(to_array($result))
            ->run();

        return $result;
    }
}
