<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\TransposeProvider;

class ConvertToOptionTest extends TestCase
{
    use TransposeProvider;

    /**
     * @dataProvider extractOkMatrix
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
    public function testExtractOk(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->extractOk());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public function extractOkMatrix(): iterable
    {
        yield "err" => [
            Result::err("Don't panic !"),
            Option::none(),
        ];

        yield "ok" => [
            Result::ok(42),
            Option::some(42),
        ];
    }

    /**
     * @dataProvider extractErrMatrix
     * @param Result<mixed, mixed> $result
     * @param Option<mixed> $expected
     */
    public function testExtractErr(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->extractErr());
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public function extractErrMatrix(): iterable
    {
        yield "err" => [
            Result::err("Don't panic !"),
            Option::some("Don't panic !"),
        ];

        yield "ok" => [
            Result::ok(42),
            Option::none(),
        ];
    }

    /**
     * @dataProvider transposeMatrix
     * @param Result<Option<mixed>, mixed> $result
     * @param Option<mixed> $option
     */
    public function testTranspose(Option $option, Result $result): void
    {
        Assert::assertEquals($option, Result::transpose($result));
    }
}
