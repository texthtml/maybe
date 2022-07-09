<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ConvertToResultTest extends TestCase
{
    use Provider\Transpose;

    /**
     * @dataProvider okOrMatrix
     * @param Option<mixed> $option
     * @param Result<mixed, mixed> $expected
     */
    public function testOkOr(Option $option, mixed $err, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $option->okOr($err));

        Assert::assertResultNotUsed($expected);
        Assert::assertResultNotUsed($result);
    }

    /**
     * @dataProvider okOrMatrix
     * @param Option<mixed> $option
     * @param Result<mixed, mixed> $expected
     */
    public function testOkOrElse(Option $option, mixed $err, Result $expected, int $expectedCalls): void
    {
        $calls = 0;

        Assert::assertEquals($expected, $result = $option->okOrElse(static function() use ($err, &$calls): mixed {
            $calls++;

            return $err;
        }));

        Assert::assertResultNotUsed($expected);
        Assert::assertResultNotUsed($result);

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, Result<mixed, mixed>, int}>
     */
    public function okOrMatrix(): iterable
    {
        yield "none" => [
            Option\none(),
            "Don't panic !",
            Result\err("Don't panic !"),
            1,
        ];

        yield "some" => [
            Option\some(42),
            "Don't panic !",
            Result\ok(42),
            0,
        ];
    }

    /**
     * @dataProvider transposeMatrix
     * @param Option<Result<mixed, mixed>> $option
     * @param Result<mixed, mixed> $expected
     */
    public function testTranspose(Option $option, Result $expected): void
    {
        Assert::assertEquals($expected, $result = Option\transpose($option));

        Assert::assertResultNotUsed($expected);
        Assert::assertResultNotUsed($result);
    }
}
