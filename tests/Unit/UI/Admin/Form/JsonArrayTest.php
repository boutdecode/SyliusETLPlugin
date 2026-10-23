<?php

declare(strict_types=1);

namespace Tests\BoutDeCode\SyliusETLPlugin\Unit\UI\Admin\Form;

use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\JsonArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JsonArrayTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed, array<string, mixed>}>
     */
    public static function provideDecoded(): iterable
    {
        yield 'object' => ['{"a": 1}', ['a' => 1]];
        yield 'list' => ['[{"code": "x"}]', [['code' => 'x']]];
        yield 'empty list' => ['[]', []];
        yield 'invalid json' => ['{', []];
        yield 'scalar json' => ['5', []];
        yield 'not a string' => [null, []];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideDecoded')]
    public function testDecode(mixed $json, array $expected): void
    {
        self::assertSame($expected, JsonArray::decode($json));
    }
}
