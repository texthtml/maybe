<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Result;

trait MustBeUsed
{
    /** @var \ArrayAccess<Result<mixed,mixed>,\Exception> */
    private static \ArrayAccess $toBeUsed;

    /**
     * @return \ArrayAccess<Result<mixed,mixed>,\Exception>
     */
    private static function toBeUsedMap(): \ArrayAccess
    {
        /** @var \ArrayAccess<Result<mixed,mixed>,\Exception> */
        return self::$toBeUsed ??= new \WeakMap();
    }

    /**
     * Mark a result as needed to be used
     */
    protected function mustBeUsed(): void
    {
        self::toBeUsedMap()[$this] = new \LogicException("Result must be used. Created");
    }

    /**
     * Mark a result as used
     */
    protected function used(): void
    {
        unset(self::toBeUsedMap()[$this]);
    }

    public function __clone()
    {
        $this->mustBeUsed();
    }

    public function __destruct()
    {
        $map = self::toBeUsedMap();

        if (isset($map[$this])) {
            $previous = $map[$this];
            unset($map[$this]);

            throw new \Exception("Dropped", previous: $previous);
        }
    }
}
