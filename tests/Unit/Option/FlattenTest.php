<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class FlattenTest extends TestCase
{
    /**
     * @param Option<mixed> $expected
     * @param Option<Option<mixed>> $option
    */
    #[DataProvider('flattenMatrix')]
    public function testFlatten(Option $expected, Option $option): void
    {
        Assert::assertEquals($expected, Option\flatten($option));
    }

    /**
     * @return iterable<array{Option<mixed>, Option<Option<mixed>>}>
     */
    public static function flattenMatrix(): iterable
    {
        /** @var Option<mixed> $none */
        $none = Option\none();

        yield "none" => [$none, $none];

        yield "some(none)" => [$none, Option\some($none)];

        $leaf = Option\some(null);

        yield "some(some(…))" => [$leaf, Option\some($leaf)];
    }
}
