<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Helpers;

use TH\Maybe\Result;

/**
 * @extends Result\Ok<string>
 */
final class ExtendedOk extends Result\Ok
{
    /** @psalm-suppress MissingThrowsDocblock 🙈 */
    public function __toString(): string {
        return $this->unwrap();
    }
}
