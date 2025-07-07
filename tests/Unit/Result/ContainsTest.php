<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ContainsTest extends TestCase
{
    use Provider\Values;

    /**
     * @param Result<mixed, null> $result
     */
     #[DataProvider('containsMatrix')]
    public function testContains(Result $result, mixed $value, bool $expect, bool $strict = true): void
    {
        Assert::assertSame($expect, $result->contains($value, strict: $strict));

        Assert::assertResultUsed($result);
    }

    /**
     * @return iterable<array{0:Result<mixed, null>, 1:mixed, 2:bool}>
     */
    public static function containsMatrix(): iterable
    {
        $o = (object)[];

        yield [Result\err(null), null, false];
        yield [Result\ok(null) , null, true];

        yield [Result\err(null), 0, false];
        yield [Result\ok(0)    , 0, true];
        yield [Result\ok(0)    , 1, false];

        yield [Result\err(null), [], false];
        yield [Result\ok([1])  , [1], true];
        yield [Result\ok([1])  , [2], false];

        yield [Result\err(null), $o, false];
        yield [Result\ok($o)   , $o, true];
        yield [Result\ok($o)   , (object)[], false];
        yield [Result\ok($o)   , (object)[], true, false];
    }

    public function testContainsDefaultsToStrict(): void
    {
        $o = (object)[];

        Assert::assertFalse(Result\ok($o)->contains((object)[]));
        Assert::assertTrue(Result\ok($o)->contains((object)[], strict: false));
    }

    /**
     * @param Result<mixed, null> $result
     */
     #[DataProvider('containsErrMatrix')]
    public function testContainsErr(Result $result, mixed $value, bool $expect, bool $strict = true): void
    {
        Assert::assertSame($expect, $result->containsErr($value, strict: $strict));
    }

    /**
     * @return iterable<array{0:Result<mixed, mixed>, 1:mixed, 2:bool}>
     */
    public static function containsErrMatrix(): iterable
    {
        $o = (object)[];

        yield [Result\ok(null) , null, false];
        yield [Result\err(null), null, true];

        yield [Result\ok(null), 0, false];
        yield [Result\err(0)  , 0, true];
        yield [Result\err(0)  , 1, false];

        yield [Result\ok(null), [], false];
        yield [Result\err([1]), [1], true];
        yield [Result\err([1]), [2], false];

        yield [Result\ok(null), $o, false];
        yield [Result\err($o) , $o, true];
        yield [Result\err($o) , (object)[], false];
        yield [Result\err($o) , (object)[], true, false];
    }

    public function testContainsErrDefaultsToStrict(): void
    {
        $o = (object)[];

        Assert::assertFalse(Result\err($o)->containsErr((object)[]));
        Assert::assertTrue(Result\err($o)->containsErr((object)[], strict: false));
    }
}
