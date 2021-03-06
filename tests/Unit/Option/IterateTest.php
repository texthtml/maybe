<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class IterateTest extends TestCase
{
    public function testIterateOptions(): void
    {
        Assert::assertIsIterable(Option\none());
        Assert::assertSame([], \iterator_to_array(Option\none()));

        Assert::assertIsIterable(Option\some(42));
        Assert::assertSame([42], \iterator_to_array(Option\some(42)));
    }
}
