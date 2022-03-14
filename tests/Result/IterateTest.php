<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class IterateTest extends TestCase
{
    public function testIterateResults(): void
    {
        Assert::assertIsIterable(Result::err(null));
        Assert::assertSame([], \iterator_to_array(Result::err(null)));

        Assert::assertIsIterable(Result::ok(42));
        Assert::assertSame([42], \iterator_to_array(Result::ok(42)));
    }
}
