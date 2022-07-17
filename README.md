# Maybe: Option & Result for PHP

Help yourself by not returning `null`, `false`, `-1` or throwing exception
for everything by using `Option` & `Result` instead.

Using those makes it harder to make mistake, make it easy to do common
operations on unknown returned values and can help static analysis tool detect
incorrect logic in PHP code.

`doctest` looks for php examples in your functions, methods classes & interfaces
comments and execute them to ensure they are correct.

# Installation

```sh
composer req --dev texthtml/maybe
```

# Usage

## `Option`s

Type `Option` represents an optional value: every `Option` is either
`Option\Some` and contains a value, or `Option\None`, and does not. `Option`
types have a number of uses:

* Initial values
* Return values for functions that are not defined over their entire input range (partial functions)
* Return value for otherwise reporting simple errors, where `None` is returned on error
* Optional class properties
* Swapping things out of difficult situations

`Option`s are commonly paired with `instanceof Option\{Some|None}` to query the
presence of a value and take action, always accounting for the `None` case.

```php
/**
 * @param Option<float>
 */
function divide(float $numerator, float $denominator) -> Option {
    return match ($denomintor) {
        0.0 => Option\none(),
        _ => Option\some($numerator / $denominator)
    };
}

// The return value of the function is an option
$result = divide(2.0, 3.0);

// Pattern match to retrieve the value
if ($result instanceof Option\Some) {
    // The division was valid
    echo "Result: {$option->unwrap()}");
} else {
    // The division was invalid
    echo "Cannot divide by 0";
}
```

## `Result`s

`Result<T, E>` is the type used for returning and propagating errors. It two
variants, `Ok(T)`, representing success and containing a value, and `Err(E)`,
representing error and containing an error value.

Functions return `Result` whenever errors are expected and recoverable.

A simple function returning Result might be defined and used like so:

```php
/**
 * @param Result<Version,string>
 */
function parse_version(string $header) -> Result {
    return match $header[0] ?? null {
        null => Result\err("invalid header length"),
        1 => Result\ok(Version::Version1),
        2 => Result\ok(Version::Version2),
        _ => Result\err("invalid version"),
    };
}

$version = parse_version("1234");
if ($version instanceof Result\Ok) {
    echo "working with version: {$version->unwrap()}";
    echo "error parsing header: {$version->unwrapErr()}";
}

# Documentation

`Option` & `Result` each have plenty of methods helper, explore their source
code comments to learn about them and how to to use them.

# TODO

* Generate documentation from comments in file
* To prevent other implementations of `Option` & `Result`, try another implementation with:
    * a class with a `final private constructor` and final unserialize, etc. methods (eg: `class Option`)
    * 1 interface for each variant of the class (eg: `interface Option\Some` & `interface Option\None`)
    * 1 static public constructors for each variant of the class (eg: `Option::Some($value)` & `Option::None()`
        * return a dynamic class extending the reference class and implementing the corresponding interface variant
        * question: will it be possible to serialize / unserialize those objects?
