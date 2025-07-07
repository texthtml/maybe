<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class BooleanTest extends TestCase
{
    use Provider\Results;

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
     #[DataProvider('andMatrix')]
    public function testAnd(Result $left, Result $right, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $left->and($right));

        Assert::assertResultUsed($left);
        Assert::assertResultUsed($right);
        Assert::assertResultNotUsed($result);
        Assert::assertResultNotUsed($expected);
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
     #[DataProvider('andMatrix')]
    public function testAndThen(Result $left, Result $right, Result $expected): void
    {
        $calls = [];
        $expectedCalls = $left instanceof Result\Err
            ? []
            : [$left->unwrap()];

        Assert::assertEquals($expected, $result = $left->andThen(
            static function (mixed $value) use ($right, &$calls): mixed {
                $calls[] = $value;

                return $right;
            },
        ));

        if (!$left instanceof Result\Err) {
            Assert::assertSame($result, $right);
        } else {
            Assert::assertResultNotUsed($right);
        }

        Assert::assertResultUsed($left);
        Assert::assertResultNotUsed($result);
        Assert::assertResultNotUsed($expected);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
     #[DataProvider('orMatrix')]
    public function testOrElse(Result $left, Result $right, Result $expected): void
    {
        $calls = 0;
        $expectedCalls = $left instanceof Result\Err
            ? 1
            : 0;

        Assert::assertEquals($expected, $result = $left->orElse(static function () use ($right, &$calls): mixed {
            $calls++;

            return $right;
        }));

        if ($left instanceof Result\Err) {
            Assert::assertSame($result, $right);
        } else {
            Assert::assertResultNotUsed($right);
        }

        Assert::assertResultUsed($left);
        Assert::assertResultNotUsed($result);
        Assert::assertResultNotUsed($expected);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
     #[DataProvider('orMatrix')]
    public function testOr(Result $left, Result $right, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $left->or($right));

        Assert::assertResultUsed($left);
        Assert::assertResultUsed($right);
        Assert::assertResultNotUsed($expected);
        Assert::assertResultNotUsed($result);
    }
}
