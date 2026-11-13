<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Result\Err;
use TH\Maybe\Result\Ok;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ContainsTest extends TestCase
{
    use Provider\Values;

    /**
     * @param Result<mixed, null> $result
     */
    #[dataProvider('containsMatrix')]
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
        $o = (object) [];

        yield [Result\err(null), null, false];
        yield [Result\ok(null), null, true];

        yield [Result\err(null), 0, false];
        yield [Result\ok(0), 0, true];
        yield [Result\ok(0), 1, false];

        yield [Result\err(null), [], false];
        yield [Result\ok([1]), [1], true];
        yield [Result\ok([1]), [2], false];

        yield [Result\err(null), $o, false];
        yield [Result\ok($o), $o, true];
        yield [Result\ok($o), (object) [], false];
        yield [Result\ok($o), (object) [], true, false];
    }

    public function testContainsDefaultsToStrict(): void
    {
        $o = (object) [];

        Assert::assertFalse(Result\ok($o)->contains((object) []));
        Assert::assertTrue(Result\ok($o)->contains((object) [], strict: false));
        Assert::assertFalse(Result\err($o)->contains((object) [], strict: false));
    }

    /**
     * The `strict` default of `true` is part of the public API contract of every
     * variant, even where the implementation ignores it.
     *
     * @throws \ReflectionException
     */
    public function testContainsStrictParameterDefaultsToTrue(): void
    {
        Assert::assertTrue((new \ReflectionMethod(Ok::class, 'contains'))->getParameters()[1]->getDefaultValue());
        Assert::assertTrue((new \ReflectionMethod(Err::class, 'contains'))->getParameters()[1]->getDefaultValue());
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainsErrStrictParameterDefaultsToTrue(): void
    {
        Assert::assertTrue((new \ReflectionMethod(Ok::class, 'containsErr'))->getParameters()[1]->getDefaultValue());
        Assert::assertTrue((new \ReflectionMethod(Err::class, 'containsErr'))->getParameters()[1]->getDefaultValue());
    }

    /**
     * @param Result<mixed, null> $result
     */
    #[dataProvider('containsErrMatrix')]
    public function testContainsErr(Result $result, mixed $value, bool $expect, bool $strict = true): void
    {
        Assert::assertSame($expect, $result->containsErr($value, strict: $strict));
    }

    /**
     * @return iterable<array{0:Result<mixed, mixed>, 1:mixed, 2:bool}>
     */
    public static function containsErrMatrix(): iterable
    {
        $o = (object) [];

        yield [Result\ok(null), null, false];
        yield [Result\err(null), null, true];

        yield [Result\ok(null), 0, false];
        yield [Result\err(0), 0, true];
        yield [Result\err(0), 1, false];

        yield [Result\ok(null), [], false];
        yield [Result\err([1]), [1], true];
        yield [Result\err([1]), [2], false];

        yield [Result\ok(null), $o, false];
        yield [Result\err($o), $o, true];
        yield [Result\err($o), (object) [], false];
        yield [Result\err($o), (object) [], true, false];
    }

    public function testContainsErrDefaultsToStrict(): void
    {
        $o = (object) [];

        Assert::assertFalse(Result\err($o)->containsErr((object) []));
        Assert::assertTrue(Result\err($o)->containsErr((object) [], strict: false));
        Assert::assertFalse(Result\ok($o)->containsErr((object) [], strict: false));
    }
}
