<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

trait Serializable
{
    /**
     * @return iterable<mixed>
     */
    public static function serializableValues(): iterable
    {
        yield "null" => [null];

        yield "int" => [42];

        yield "string" => ["So Long, and Thanks for All the Fish"];

        yield "array" => [[null, 42, true, 3.14]];

        yield "object" => [new \DateTimeImmutable("@42")];
    }
}
