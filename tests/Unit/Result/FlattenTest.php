<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;

final class FlattenTest extends TestCase
{
    /**
     * @param Result<mixed, null> $expected
     * @param Result<Result<mixed, null>, null> $result
     */
     #[DataProvider('flattenMatrix')]
    public function testFlatten(Result $expected, Result $result): void
    {
        Assert::assertResultNotUsed($result);

        Assert::assertEquals($expected, $result2 = Result\flatten($result));

        Assert::assertResultNotUsed($expected);
        Assert::assertResultNotUsed($result2);
        Assert::assertResultUsed($result);
    }

    /**
     * @return iterable<array{Result<mixed, null>, Result<Result<mixed, null>, null>}>
     */
    public static function flattenMatrix(): iterable
    {
        yield "err" => [Result\err(null), Result\err(null)];

        yield "ok(err)" => [Result\err(null), Result\ok(Result\err(null))];

        yield "ok(ok(…))" => [Result\ok(null), Result\ok(Result\ok(null))];
    }
}
