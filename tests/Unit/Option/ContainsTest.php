<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class ContainsTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider containsMatrix
     * @param Option<mixed> $option
     */
    public function testContains(Option $option, mixed $value, bool $expect, bool $strict = true): void
    {
        Assert::assertSame($expect, $option->contains($value, strict: $strict));
    }

    /**
     * @return iterable<array{0:Option<mixed>, 1:mixed, 2:bool}>
     */
    public function containsMatrix(): iterable
    {
        $o = (object)[];

        yield [Option\none(),     null, false];
        yield [Option\some(null), null, true];

        yield [Option\none(),  0, false];
        yield [Option\some(0), 0, true];
        yield [Option\some(0), 1, false];

        yield [Option\none(),    [], false];
        yield [Option\some([1]), [1], true];
        yield [Option\some([1]), [2], false];

        yield [Option\none(),   $o, false];
        yield [Option\some($o), $o, true];
        yield [Option\some($o), (object)[], false];
        yield [Option\some($o), (object)[], true, false];
    }

    public function testContainsDefaultsToStrict(): void
    {
        $o = (object)[];

        Assert::assertFalse(Option\some($o)->contains((object)[]));
        Assert::assertTrue(Option\some($o)->contains((object)[], strict: false));
    }
}
