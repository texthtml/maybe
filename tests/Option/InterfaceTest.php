<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class InterfaceTest extends TestCase
{
    public function testInstanceOfOption(): void
    {
        Assert::assertInstanceOf(Option::class, Option\none());
        Assert::assertInstanceOf(Option::class, Option\some(null));
    }

    public function testInstanceOfNone(): void
    {
        Assert::assertInstanceOf(Option\None::class, Option\none());
        Assert::assertNotInstanceOf(Option\None::class, Option\some(null));
    }

    public function testInstanceOfSome(): void
    {
        Assert::assertNotInstanceOf(Option\Some::class, Option\none());
        Assert::assertInstanceOf(Option\Some::class, Option\some(null));
    }
}
