<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class BooleanTest extends TestCase
{
    use Provider\Options;

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
     #[DataProvider('andMatrix')]
    public function testAnd(Option $left, Option $right, Option $expected): void
    {
        Assert::assertSame($expected, $left->and($right));
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
     #[DataProvider('andMatrix')]
    public function testAndThen(Option $left, Option $right, Option $expected): void
    {
        $calls = [];
        $expectedCalls = $left instanceof Option\None
            ? []
            : [$left->unwrap()];

        Assert::assertSame($expected, $left->andThen(static function (mixed $value) use ($right, &$calls): mixed {
            $calls[] = $value;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
     #[DataProvider('orMatrix')]
    public function testOrElse(Option $left, Option $right, Option $expected): void
    {
        $calls = 0;
        $expectedCalls = $left instanceof Option\None
            ? 1
            : 0;

        Assert::assertSame($expected, $left->orElse(static function () use ($right, &$calls): mixed {
            $calls++;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
     #[DataProvider('orMatrix')]
    public function testOr(Option $left, Option $right, Option $expected): void
    {
        Assert::assertSame($expected, $left->or($right));
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
     #[DataProvider('xorMatrix')]
    public function testXor(Option $left, Option $right, Option $expected): void
    {
        Assert::assertEquals($expected, $left->xor($right));
    }
}
