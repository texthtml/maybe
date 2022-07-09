<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class NoneTest extends TestCase
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
