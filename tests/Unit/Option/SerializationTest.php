<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class SerializationTest extends TestCase
{
    use Provider\Serializable;

    public function testWithNone(): void
    {
        /** @var Option<never> $none */
        $none = Option\none();

        $this->testSerializableOption($none);
    }

    /**
     * @dataProvider serializableValues
     */
    public function testWithSomeValidValues(mixed $value): void
    {
        $this->testSerializableOption(Option\some($value));
    }

    /**
     * @param Option<mixed> $option
     */
    private function testSerializableOption(Option $option): void
    {
        $serialized = \serialize($option);

        Assert::assertEquals($option, \unserialize($serialized));
    }
}
