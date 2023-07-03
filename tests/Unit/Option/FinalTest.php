<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\TestCase;

final class FinalTest extends TestCase
{
    // @phpstan-ignore
    public function testCannotInstanciateThirdPartyNone(): void
    {
        $this->expectExceptionMessage("Class MyNone cannot extend final class TH\Maybe\Option\None");

        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        eval("class MyNone extends \TH\Maybe\Option\None {}");
    }

    public function testCannotInstanciateThirdPartySome(): void
    {
        $this->expectExceptionMessage("Class MySome cannot extend final class TH\Maybe\Option\Some");

        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        eval("class MySome extends \TH\Maybe\Option\Some {}");
    }

    public function testCannotInstanciateThirdPartyOption(): void
    {
        $this->expectExceptionMessage(
            "Call to private TH\Maybe\Option::__construct() from scope TH\Maybe\Tests\Unit\Option\FinalTest",
        );

        self::markTestSkipped("Don't know how to prevent that");

        eval("class MyOption implements \TH\Maybe\Option {}");
    }
}
