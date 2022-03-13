<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

class IsTest extends TestCase
{
    public function testIsSome(): void
    {
        Assert::assertFalse(Option::none()->isSome());
        Assert::assertTrue(Option::some(null)->isSome());
    }

    public function testIsNone(): void
    {
        Assert::assertTrue(Option::none()->isNone());
        Assert::assertFalse(Option::some(null)->isNone());
    }
}
