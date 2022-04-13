<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class MapTest extends TestCase
{
    /**
     * @dataProvider mapMatrix
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param Result<U, null> $expected
     * @param array<T> $expectedCalls
     */
    public function testMap(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result->map(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

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
    public function mapMatrix(): iterable
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
     * @dataProvider mapErrMatrix
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param Result<U, null> $expected
     * @param array<T> $expectedCalls
     */
    public function testMapErr(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result->mapErr(static function(mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

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
    public function mapErrMatrix(): iterable
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
     * @dataProvider mapOrMatrix
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U $mapResult
     * @param U $default
     * @param U $expected
     * @param array<T> $expectedCalls
     */
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
    public function mapOrMatrix(): iterable
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
