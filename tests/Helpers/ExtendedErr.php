<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Helpers;

use TH\Maybe\Result;

/**
 * @extends Result\Err<string>
 */
final class ExtendedErr extends Result\Err
{
    /** @psalm-suppress MissingThrowsDocblock 🙈 */
    public function __toString(): string {
        return $this->unwrapErr();
    }
}
