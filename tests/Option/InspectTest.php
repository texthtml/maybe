<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class InspectTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testInspectSome(mixed $value): void
    {
        $option = Option\some($value);

        ["result" => $result, "calls" => $calls] = $this->inspect($option);

        Assert::assertSame($option, $result);
        Assert::assertSame([$value], $calls);
    }

    public function testInspectNone(): void
    {
        $option = Option\none();

        ["result" => $result, "calls" => $calls] = $this->inspect($option);

        Assert::assertSame($option, $result);
        Assert::assertSame([], $calls);
    }

    /**
     * @template T
     * @param Option<T> $option
     * @return array{result:Option<T>, calls: array<T>}
     */
    private function inspect(Option $option): array
    {
        $calls = [];

        $option = $option->inspect(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ["result" => $option, "calls" => $calls];
    }
}
