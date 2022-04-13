<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

class FinalTest extends TestCase
{
    // @phpstan-ignore
    public function testCannotInstanciateThirdPartyNone(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Class TH\Maybe\Option\None@anonymous cannot extend final class TH\Maybe\Option\None",
        );

        // @phpstan-ignore-next-line
        new class () extends Option\None {};
    }

    public function testCannotInstanciateThirdPartySome(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Class TH\Maybe\Option\Some@anonymous cannot extend final class TH\Maybe\Option\Some",
        );

        // @phpstan-ignore-next-line
        new class () extends Option\Some {};
    }

    public function testCannotInstanciateThirdPartyOption(): void
    {
        self::markTestSkipped("Don't know how to prevent that");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Call to private TH\Maybe\Option::__construct() from scope TH\Maybe\Tests\Option\FinalTest",
        );

        // @phpstan-ignore-next-line
        new class () implements Option {
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
