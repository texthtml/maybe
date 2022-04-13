<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Provider;

class InspectTest extends TestCase
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
    }

    public function testInspectNone(): void
    {
        $result = Result\err(null);

        ["result" => $result, "calls" => $calls] = $this->inspect($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([], $calls);
    }

    /**
     * @template T
     * @param Result<T, mixed> $result
     * @return array{result:Result<T, mixed>, calls: array<T>}
     */
    private function inspect(Result $result): array
    {
        $calls = [];

        $result = $result->inspect(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ["result" => $result, "calls" => $calls];
    }
}
