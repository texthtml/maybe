<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;

final class MapTest extends TestCase
{
    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param Result<U, null> $expected
     * @param array<T> $expectedCalls
     */
     #[DataProvider('mapMatrix')]
    public function testMap(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result2 = $result->map(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertResultNotUsed($expected);
        Assert::assertResultUsed($result);
        Assert::assertResultNotUsed($result2);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result<mixed, null>,
     *   mixed,
     *   Result<mixed, null>,
     *   array<mixed>
     * }>
     */
    public static function mapMatrix(): iterable
    {
        yield "err" => [
            Result\err(null),
            "fish",
            Result\err(null),
            [],
        ];

        yield "ok" => [
            Result\ok(42),
            "fish",
            Result\ok("fish"),
            [42],
        ];
    }

    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param Result<U, null> $expected
     * @param array<T> $expectedCalls
     */
     #[DataProvider('mapErrMatrix')]
    public function testMapErr(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result2 = $result->mapErr(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertResultNotUsed($expected);
        Assert::assertResultUsed($result);
        Assert::assertResultNotUsed($result2);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result<mixed, mixed>,
     *   mixed,
     *   Result<mixed, mixed>,
     *   array<mixed>
     * }>
     */
    public static function mapErrMatrix(): iterable
    {
        yield "ok" => [
            Result\ok(null),
            "fish",
            Result\ok(null),
            [],
        ];

        yield "err" => [
            Result\err(42),
            "fish",
            Result\err("fish"),
            [42],
        ];
    }

    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param U $default
     * @param U $expected
     * @param array<T> $expectedCalls
     */
     #[DataProvider('mapOrMatrix')]
    public function testMapOr(
        Result $result,
        mixed $mapResult,
        mixed $default,
        mixed $expected,
        array $expectedCalls,
    ): void {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result->mapOr(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }, $default),
        );

        Assert::assertResultUsed($result);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result<mixed, null>,
     *   mixed,
     *   mixed,
     *   mixed,
     *   array<mixed>
     * }>
     */
    public static function mapOrMatrix(): iterable
    {
        yield "err" => [
            Result\err(null),
            "fish",
            "fishes",
            "fishes",
            [],
        ];

        yield "ok" => [
            Result\ok(42),
            "fish",
            "fishes",
            "fish",
            [42],
        ];
    }
}
