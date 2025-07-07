<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ConvertToOptionTest extends TestCase
{
    use Provider\Transpose;

    /**
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
     #[DataProvider('okMatrix')]
    public function testOk(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->ok());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public static function okMatrix(): iterable
    {
        yield "err" => [
            Result\err("Don't panic !"),
            Option\none(),
        ];

        yield "ok" => [
            Result\ok(42),
            Option\some(42),
        ];
    }

    /**
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
     #[DataProvider('errMatrix')]
    public function testErr(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->err());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public static function errMatrix(): iterable
    {
        yield "err" => [
            Result\err("Don't panic !"),
            Option\some("Don't panic !"),
        ];

        yield "ok" => [
            Result\ok(42),
            Option\none(),
        ];
    }

    /**
     * @template T
     * @template E
     * @param Result<Option<T>, E> $expected
     * @param Option<Result<T,E>> $option
     */
     #[DataProvider('transposeMatrix')]
    public function testTranspose(Option $option, Result $expected): void
    {
        Assert::assertEquals($option, $option2 = Result\transpose($expected));

        Assert::assertResultUsed($expected);
        $option->map(Assert::assertResultNotUsed(...));
        $option2->map(Assert::assertResultNotUsed(...));
    }
}
