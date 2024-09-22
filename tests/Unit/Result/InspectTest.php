<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class InspectTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testInspectOk(mixed $value): void
    {
        $result = Result\ok($value);

        ["result" => $result, "calls" => $calls] = $this->inspect($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([$value], $calls);

        Assert::assertResultNotUsed($result);
    }

    public function testInspectNone(): void
    {
        $result = Result\err(null);

        ["result" => $result, "calls" => $calls] = $this->inspect($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([], $calls);

        Assert::assertResultNotUsed($result);
    }

    public function testInspectErrOk(): void
    {
        $result = Result\ok(null);

        ["result" => $result, "calls" => $calls] = $this->inspectErr($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([], $calls);

        Assert::assertResultNotUsed($result);
    }

    /**
     * @dataProvider values
     */
    public function testInspectErrNone(mixed $value): void
    {
        $result = Result\err($value);

        ["result" => $result, "calls" => $calls] = $this->inspectErr($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([$value], $calls);

        Assert::assertResultNotUsed($result);
    }

    /**
     * @template T
     * @template E
     * @param Result<T, E> $result
     * @return array{result:Result<T, E>, calls: array<T>}
     */
    private function inspect(Result $result): array
    {
        $calls = [];

        $result = $result->inspect(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ["result" => $result, "calls" => $calls];
    }

    /**
     * @template T
     * @template E
     * @param Result<T, E> $result
     * @return array{result:Result<T, E>, calls: array<E>}
     */
    private function inspectErr(Result $result): array
    {
        $calls = [];

        $result = $result->inspectErr(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ["result" => $result, "calls" => $calls];
    }
}
