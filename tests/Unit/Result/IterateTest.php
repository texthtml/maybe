<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;

final class IterateTest extends TestCase
{
    public function testIterateResults(): void
    {
        Assert::assertIsIterable($result = Result\err(null));
        Assert::assertResultNotUsed($result);

        Assert::assertSame([], \iterator_to_array($result = Result\err(null)));
        Assert::assertResultUsed($result);

        Assert::assertIsIterable($result = Result\ok(42));
        Assert::assertResultNotUsed($result);
        Assert::assertSame([42], \iterator_to_array($result = Result\ok(42)));
        Assert::assertResultUsed($result);
    }
}
