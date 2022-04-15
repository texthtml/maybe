<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ConvertToOptionTest extends TestCase
{
    use Provider\Transpose;

    /**
     * @dataProvider OkMatrix
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
    public function testOk(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->ok());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public function okMatrix(): iterable
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
     * @dataProvider ErrMatrix
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
    public function testErr(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->err());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public function errMatrix(): iterable
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
     * @dataProvider transposeMatrix
     * @param Result<Option<mixed>, mixed> $result
     * @param Option<mixed> $option
     */
    public function testTranspose(Option $option, Result $result): void
    {
        Assert::assertEquals($option, $option2 = Result\transpose($result));

        Assert::assertResultUsed($result);
        $option->map(Assert::assertResultNotUsed(...));
        $option2->map(Assert::assertResultNotUsed(...));
    }
}
