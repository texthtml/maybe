<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class BooleanTest extends TestCase
{
    use ResultsProvider;

    /**
     * @dataProvider andMatrix
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    public function testAnd(Result $left, Result $right, Result $expected): void
    {
        Assert::assertSame($expected, $left->and($right));
    }

    /**
     * @dataProvider andMatrix
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    public function testAndThen(Result $left, Result $right, Result $expected): void
    {
        $calls = [];
        $expectedCalls = $left instanceof Result\Err
            ? []
            : [$left->unwrap()];

        Assert::assertSame($expected, $left->andThen(static function (mixed $value) use ($right, &$calls): mixed {
            $calls[] = $value;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @dataProvider orMatrix
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    public function testOrElse(Result $left, Result $right, Result $expected): void
    {
        $calls = 0;
        $expectedCalls = $left instanceof Result\Err
            ? 1
            : 0;

        Assert::assertSame($expected, $left->orElse(static function () use ($right, &$calls): mixed {
            $calls++;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @dataProvider orMatrix
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    public function testOr(Result $left, Result $right, Result $expected): void
    {
        Assert::assertSame($expected, $left->or($right));
    }
}
