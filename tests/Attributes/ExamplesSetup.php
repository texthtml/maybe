<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Attributes;

#[\Attribute]
final class ExamplesSetup
{
    public function __construct(
        /** @var string-class */
        public readonly string $setup,
    ) {
    }
}
