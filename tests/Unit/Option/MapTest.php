<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class MapTest extends TestCase
{
    /**
     * @template T
     * @template U
     * @param Option<T> $option
     * @param U $mapResult
     * @param Option<U> $expected
     * @param array<T> $expectedCalls
    */
    #[DataProvider('mapMatrix')]
    public function testMap(Option $option, mixed $mapResult, Option $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $option->map(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Option<mixed>,
     *   mixed,
     *   Option<mixed>,
     *   array<mixed>
     * }>
     */
    public static function mapMatrix(): iterable
    {
        yield "none" => [
            Option\none(),
            "fish",
            Option\none(),
            [],
        ];

        yield "some" => [
            Option\some(42),
            "fish",
            Option\some("fish"),
            [42],
        ];
    }

    /**
     * @template T
     * @template U
     * @param Option<T> $option
     * @param U $mapResult
     * @param U $default
     * @param U $expected
     * @param array<T> $expectedCalls
    */
    #[DataProvider('mapOrMatrix')]
    public function testMapOr(
        Option $option,
        mixed $mapResult,
        mixed $default,
        mixed $expected,
        array $expectedCalls,
    ): void {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $option->mapOr(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }, $default),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Option<mixed>,
     *   mixed,
     *   mixed,
     *   mixed,
     *   array<mixed>
     * }>
     */
    public static function mapOrMatrix(): iterable
    {
        yield "none" => [
            Option\none(),
            "fish",
            "fishes",
            "fishes",
            [],
        ];

        yield "some" => [
            Option\some(42),
            "fish",
            "fishes",
            "fish",
            [42],
        ];
    }
}
