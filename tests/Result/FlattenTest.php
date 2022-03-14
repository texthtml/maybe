<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class FlattenTest extends TestCase
{
    /**
     * @dataProvider flattenMatrix
     * @param Result<mixed, null> $expected
     * @param Result<Result<mixed, null>, null> $result
     */
    public function testFlatten(Result $expected, Result $result): void
    {
        Assert::assertSame($expected, Result::flatten($result));
    }

    /**
     * @return iterable<array{Result<mixed, null>, Result<Result<mixed, null>, null>}>
     */
    public function flattenMatrix(): iterable
    {
        $err = Result::err(null);

        yield "err" => [$err, $err];

        yield "ok(err)" => [$err, Result::ok($err)];

        $leaf = Result::ok(null);

        yield "ok(ok(…))" => [$leaf, Result::ok($leaf)];
    }
}
