<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class TrapTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testTrapOk(mixed $value): void
    {
        $callback = static fn () => $value;

        Assert::assertEquals($value, Result\trap($callback)->unwrap());
    }

    public function testTrapCheckedException(): void
    {
        Assert::assertEquals(
            new \Exception("Ooops"),
            // @phpstan-ignore-next-line
            Result\trap(static fn () => throw new \Exception("Ooops"))->unwrapErr(),
        );
    }

    public function testTrapUncheckedException(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->expectExceptionMessage("Division by zero");

        // @phpstan-ignore-next-line
        Result\trap(static fn () => 1 / 0);
    }
}
