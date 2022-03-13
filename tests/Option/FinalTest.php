<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

class FinalTest extends TestCase
{
    public function testCannotInstanciateThirdPartyOption(): void
    {
        $this->expectErrorMessage(
            "Call to private TH\Maybe\Option::__construct() from scope TH\Maybe\Tests\Option\FinalTest",
        );

        // @phpstan-ignore-next-line
        new class () extends Option {
            public function expect(string $message): mixed
            {
                return null;
            }

            public function unwrap(): mixed
            {
                return null;
            }
        };
    }
}
