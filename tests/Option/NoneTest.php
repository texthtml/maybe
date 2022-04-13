<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

class NoneTest extends TestCase
{
    public function testNoneIsASingleton(): void
    {
        Assert::assertEquals(
            Option\none(),
            Option\none(),
        );

        Assert::assertSame(
            Option\none(),
            Option\none(),
        );
    }
}
