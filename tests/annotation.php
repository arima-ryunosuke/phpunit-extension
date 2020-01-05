<?php

namespace ryunosuke\PHPUnit;

/**
 * @see \ryunosuke\PHPUnit\Constraint\EqualsFile
 * @method \ryunosuke\PHPUnit\Actual allEqualsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFileAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFileAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\EqualsIgnoreWS
 * @method \ryunosuke\PHPUnit\Actual allEqualsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWSAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWSAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\FileContains
 * @method \ryunosuke\PHPUnit\Actual allFileContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notFileContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContainsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContainsAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\FileEquals
 * @method \ryunosuke\PHPUnit\Actual allFileEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notFileEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEqualsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEqualsAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsBetween
 * @method \ryunosuke\PHPUnit\Actual allIsBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isNotBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isBetweenAny(array $minmaxs)
 * @method \ryunosuke\PHPUnit\Actual isBetweenAll(array $minmaxs)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsBlank
 * @method \ryunosuke\PHPUnit\Actual allIsBlank(bool $trim = true)
 * @method \ryunosuke\PHPUnit\Actual isBlank(bool $trim = true)
 * @method \ryunosuke\PHPUnit\Actual isNotBlank(bool $trim = true)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType
 * @method \ryunosuke\PHPUnit\Actual allIsCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isNotCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isCTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isCTypeAll(array $types)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsFalsy
 * @method \ryunosuke\PHPUnit\Actual allIsFalsy()
 * @method \ryunosuke\PHPUnit\Actual isFalsy()
 * @method \ryunosuke\PHPUnit\Actual isNotFalsy()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsTruthy
 * @method \ryunosuke\PHPUnit\Actual allIsTruthy()
 * @method \ryunosuke\PHPUnit\Actual isTruthy()
 * @method \ryunosuke\PHPUnit\Actual isNotTruthy()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid
 * @method \ryunosuke\PHPUnit\Actual allIsValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidAny(array $types, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidAll(array $types, $flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\OutputMatches
 * @method \ryunosuke\PHPUnit\Actual allOutputMatches($value)
 * @method \ryunosuke\PHPUnit\Actual outputMatches($value)
 * @method \ryunosuke\PHPUnit\Actual notOutputMatches($value)
 * @method \ryunosuke\PHPUnit\Actual outputMatchesAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual outputMatchesAll(array $values)
 *
 * @see \ryunosuke\PHPUnit\Constraint\StringLengthEquals
 * @method \ryunosuke\PHPUnit\Actual allStringLengthEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual notStringLengthEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEqualsAny(array $lengths, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEqualsAll(array $lengths, bool $multibyte = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\Throws
 * @method \ryunosuke\PHPUnit\Actual allThrows($orValues)
 * @method \ryunosuke\PHPUnit\Actual throws($orValues)
 * @method \ryunosuke\PHPUnit\Actual notThrows($orValues)
 *
 * @see \PHPUnit\Framework\Constraint\ArrayHasKey
 * @method \ryunosuke\PHPUnit\Actual allArrayHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual notArrayHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKeyAny(array $keys)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKeyAll(array $keys)
 *
 * @see \PHPUnit\Framework\Constraint\ArraySubset
 * @method \ryunosuke\PHPUnit\Actual allArraySubset(iterable $subset, bool $strict = false)
 * @method \ryunosuke\PHPUnit\Actual arraySubset(iterable $subset, bool $strict = false)
 * @method \ryunosuke\PHPUnit\Actual notArraySubset(iterable $subset, bool $strict = false)
 * @method \ryunosuke\PHPUnit\Actual arraySubsetAny(array $subsets, bool $strict = false)
 * @method \ryunosuke\PHPUnit\Actual arraySubsetAll(array $subsets, bool $strict = false)
 *
 * @see \PHPUnit\Framework\Constraint\Attribute
 * @method \ryunosuke\PHPUnit\Actual allAttribute(\PHPUnit\Framework\Constraint\Constraint $constraint, string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual attribute(\PHPUnit\Framework\Constraint\Constraint $constraint, string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual notAttribute(\PHPUnit\Framework\Constraint\Constraint $constraint, string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual attributeAny(array $constraintattributeNames)
 * @method \ryunosuke\PHPUnit\Actual attributeAll(array $constraintattributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\Callback
 * @method \ryunosuke\PHPUnit\Actual allCallback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual callback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual notCallback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual callbackAny(array $callbacks)
 * @method \ryunosuke\PHPUnit\Actual callbackAll(array $callbacks)
 *
 * @see \PHPUnit\Framework\Constraint\ClassHasAttribute
 * @method \ryunosuke\PHPUnit\Actual allClassHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual notClassHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classHasAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\ClassHasStaticAttribute
 * @method \ryunosuke\PHPUnit\Actual allClassHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual notClassHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\Count
 * @method \ryunosuke\PHPUnit\Actual allCount(int $expected)
 * @method \ryunosuke\PHPUnit\Actual count(int $expected)
 * @method \ryunosuke\PHPUnit\Actual notCount(int $expected)
 * @method \ryunosuke\PHPUnit\Actual countAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual countAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\DirectoryExists
 * @method \ryunosuke\PHPUnit\Actual allDirectoryExists()
 * @method \ryunosuke\PHPUnit\Actual directoryExists()
 * @method \ryunosuke\PHPUnit\Actual notDirectoryExists()
 *
 * @see \PHPUnit\Framework\Constraint\Exception
 * @method \ryunosuke\PHPUnit\Actual allException(string $className)
 * @method \ryunosuke\PHPUnit\Actual exception(string $className)
 * @method \ryunosuke\PHPUnit\Actual notException(string $className)
 * @method \ryunosuke\PHPUnit\Actual exceptionAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual exceptionAll(array $classNames)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionCode
 * @method \ryunosuke\PHPUnit\Actual allExceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionCodeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionCodeAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionMessage
 * @method \ryunosuke\PHPUnit\Actual allExceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression
 * @method \ryunosuke\PHPUnit\Actual allExceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpressionAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpressionAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\FileExists
 * @method \ryunosuke\PHPUnit\Actual allFileExists()
 * @method \ryunosuke\PHPUnit\Actual fileExists()
 * @method \ryunosuke\PHPUnit\Actual notFileExists()
 *
 * @see \PHPUnit\Framework\Constraint\GreaterThan
 * @method \ryunosuke\PHPUnit\Actual allGreaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual greaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual notGreaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual greaterThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual greaterThanAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsAnything
 * @method \ryunosuke\PHPUnit\Actual allIsAnything()
 * @method \ryunosuke\PHPUnit\Actual isAnything()
 * @method \ryunosuke\PHPUnit\Actual isNotAnything()
 *
 * @see \PHPUnit\Framework\Constraint\IsEmpty
 * @method \ryunosuke\PHPUnit\Actual allIsEmpty()
 * @method \ryunosuke\PHPUnit\Actual isEmpty()
 * @method \ryunosuke\PHPUnit\Actual isNotEmpty()
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual
 * @method \ryunosuke\PHPUnit\Actual allIsEqual($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqual($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotEqual($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqualAny(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqualAll(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsFalse
 * @method \ryunosuke\PHPUnit\Actual allIsFalse()
 * @method \ryunosuke\PHPUnit\Actual isFalse()
 * @method \ryunosuke\PHPUnit\Actual isNotFalse()
 *
 * @see \PHPUnit\Framework\Constraint\IsFinite
 * @method \ryunosuke\PHPUnit\Actual allIsFinite()
 * @method \ryunosuke\PHPUnit\Actual isFinite()
 * @method \ryunosuke\PHPUnit\Actual isNotFinite()
 *
 * @see \PHPUnit\Framework\Constraint\IsIdentical
 * @method \ryunosuke\PHPUnit\Actual allIsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isNotIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isIdenticalAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsInfinite
 * @method \ryunosuke\PHPUnit\Actual allIsInfinite()
 * @method \ryunosuke\PHPUnit\Actual isInfinite()
 * @method \ryunosuke\PHPUnit\Actual isNotInfinite()
 *
 * @see \PHPUnit\Framework\Constraint\IsInstanceOf
 * @method \ryunosuke\PHPUnit\Actual allIsInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isNotInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOfAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOfAll(array $classNames)
 *
 * @see \PHPUnit\Framework\Constraint\IsJson
 * @method \ryunosuke\PHPUnit\Actual allIsJson()
 * @method \ryunosuke\PHPUnit\Actual isJson()
 * @method \ryunosuke\PHPUnit\Actual isNotJson()
 *
 * @see \PHPUnit\Framework\Constraint\IsNan
 * @method \ryunosuke\PHPUnit\Actual allIsNan()
 * @method \ryunosuke\PHPUnit\Actual isNan()
 * @method \ryunosuke\PHPUnit\Actual isNotNan()
 *
 * @see \PHPUnit\Framework\Constraint\IsNull
 * @method \ryunosuke\PHPUnit\Actual allIsNull()
 * @method \ryunosuke\PHPUnit\Actual isNull()
 * @method \ryunosuke\PHPUnit\Actual isNotNull()
 *
 * @see \PHPUnit\Framework\Constraint\IsReadable
 * @method \ryunosuke\PHPUnit\Actual allIsReadable()
 * @method \ryunosuke\PHPUnit\Actual isReadable()
 * @method \ryunosuke\PHPUnit\Actual isNotReadable()
 *
 * @see \PHPUnit\Framework\Constraint\IsTrue
 * @method \ryunosuke\PHPUnit\Actual allIsTrue()
 * @method \ryunosuke\PHPUnit\Actual isTrue()
 * @method \ryunosuke\PHPUnit\Actual isNotTrue()
 *
 * @see \PHPUnit\Framework\Constraint\IsType
 * @method \ryunosuke\PHPUnit\Actual allIsType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isNotType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isTypeAll(array $types)
 *
 * @see \PHPUnit\Framework\Constraint\IsWritable
 * @method \ryunosuke\PHPUnit\Actual allIsWritable()
 * @method \ryunosuke\PHPUnit\Actual isWritable()
 * @method \ryunosuke\PHPUnit\Actual isNotWritable()
 *
 * @see \PHPUnit\Framework\Constraint\JsonMatches
 * @method \ryunosuke\PHPUnit\Actual allJsonMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual jsonMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual notJsonMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\LessThan
 * @method \ryunosuke\PHPUnit\Actual allLessThan($value)
 * @method \ryunosuke\PHPUnit\Actual lessThan($value)
 * @method \ryunosuke\PHPUnit\Actual notLessThan($value)
 * @method \ryunosuke\PHPUnit\Actual lessThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual lessThanAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\ObjectHasAttribute
 * @method \ryunosuke\PHPUnit\Actual allObjectHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual notObjectHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\RegularExpression
 * @method \ryunosuke\PHPUnit\Actual allRegularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual regularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual notRegularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual regularExpressionAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual regularExpressionAll(array $patterns)
 *
 * @see \PHPUnit\Framework\Constraint\SameSize
 * @method \ryunosuke\PHPUnit\Actual allSameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual sameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual notSameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual sameSizeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual sameSizeAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\StringContains
 * @method \ryunosuke\PHPUnit\Actual allStringContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notStringContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContainsAny(array $strings, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContainsAll(array $strings, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\StringEndsWith
 * @method \ryunosuke\PHPUnit\Actual allStringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual notStringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWithAny(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWithAll(array $suffixs)
 *
 * @see \PHPUnit\Framework\Constraint\StringMatchesFormatDescription
 * @method \ryunosuke\PHPUnit\Actual allStringMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual notStringMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescriptionAny(array $strings)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescriptionAll(array $strings)
 *
 * @see \PHPUnit\Framework\Constraint\StringStartsWith
 * @method \ryunosuke\PHPUnit\Actual allStringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual notStringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWithAny(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWithAll(array $prefixs)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContains
 * @method \ryunosuke\PHPUnit\Actual allTraversableContains($value, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
 * @method \ryunosuke\PHPUnit\Actual traversableContains($value, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
 * @method \ryunosuke\PHPUnit\Actual notTraversableContains($value, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsAny(array $values, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsAll(array $values, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsEqual
 * @method \ryunosuke\PHPUnit\Actual allTraversableContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual notTraversableContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqualAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqualAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsIdentical
 * @method \ryunosuke\PHPUnit\Actual allTraversableContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual notTraversableContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdenticalAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsOnly
 * @method \ryunosuke\PHPUnit\Actual allTraversableContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual notTraversableContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnlyAny(array $types, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnlyAll(array $types, bool $isNativeType = true)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct()
 * @method \ryunosuke\PHPUnit\Actual allIs($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual is($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNot($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isAny(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isAll(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsIdentical::__construct()
 * @method \ryunosuke\PHPUnit\Actual allIsSame($value)
 * @method \ryunosuke\PHPUnit\Actual isSame($value)
 * @method \ryunosuke\PHPUnit\Actual isNotSame($value)
 * @method \ryunosuke\PHPUnit\Actual isSameAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isSameAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct() {"canonicalize":true}
 * @method \ryunosuke\PHPUnit\Actual allEqualsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizingAny(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizingAll(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct() {"4":true}
 * @method \ryunosuke\PHPUnit\Actual allEqualsIgnoreCase($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCase($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreCase($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCaseAny(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCaseAll(array $values, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = true)
 *
 * @see \PHPUnit\Framework\Constraint\RegularExpression::__construct()
 * @method \ryunosuke\PHPUnit\Actual allMatches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual matches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual notMatches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual matchesAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual matchesAll(array $patterns)
 *
 * @see \PHPUnit\Framework\Constraint\GreaterThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual allGt($value)
 * @method \ryunosuke\PHPUnit\Actual gt($value)
 * @method \ryunosuke\PHPUnit\Actual notGt($value)
 * @method \ryunosuke\PHPUnit\Actual gtAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual gtAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\LessThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual allLt($value)
 * @method \ryunosuke\PHPUnit\Actual lt($value)
 * @method \ryunosuke\PHPUnit\Actual notLt($value)
 * @method \ryunosuke\PHPUnit\Actual ltAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual ltAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct(),\PHPUnit\Framework\Constraint\GreaterThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual allGte($value)
 * @method \ryunosuke\PHPUnit\Actual gte($value)
 * @method \ryunosuke\PHPUnit\Actual notGte($value)
 * @method \ryunosuke\PHPUnit\Actual gteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual gteAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct(),\PHPUnit\Framework\Constraint\LessThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual allLte($value)
 * @method \ryunosuke\PHPUnit\Actual lte($value)
 * @method \ryunosuke\PHPUnit\Actual notLte($value)
 * @method \ryunosuke\PHPUnit\Actual lteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual lteAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsNull::__construct(),\PHPUnit\Framework\Constraint\IsType::__construct() ["string"]
 * @method \ryunosuke\PHPUnit\Actual allIsNullOrString()
 * @method \ryunosuke\PHPUnit\Actual isNullOrString()
 * @method \ryunosuke\PHPUnit\Actual isNotNullOrString()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["array"]
 * @method \ryunosuke\PHPUnit\Actual allIsArray()
 * @method \ryunosuke\PHPUnit\Actual isArray()
 * @method \ryunosuke\PHPUnit\Actual isNotArray()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["bool"]
 * @method \ryunosuke\PHPUnit\Actual allIsBool()
 * @method \ryunosuke\PHPUnit\Actual isBool()
 * @method \ryunosuke\PHPUnit\Actual isNotBool()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["float"]
 * @method \ryunosuke\PHPUnit\Actual allIsFloat()
 * @method \ryunosuke\PHPUnit\Actual isFloat()
 * @method \ryunosuke\PHPUnit\Actual isNotFloat()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["int"]
 * @method \ryunosuke\PHPUnit\Actual allIsInt()
 * @method \ryunosuke\PHPUnit\Actual isInt()
 * @method \ryunosuke\PHPUnit\Actual isNotInt()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["numeric"]
 * @method \ryunosuke\PHPUnit\Actual allIsNumeric()
 * @method \ryunosuke\PHPUnit\Actual isNumeric()
 * @method \ryunosuke\PHPUnit\Actual isNotNumeric()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["object"]
 * @method \ryunosuke\PHPUnit\Actual allIsObject()
 * @method \ryunosuke\PHPUnit\Actual isObject()
 * @method \ryunosuke\PHPUnit\Actual isNotObject()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["resource"]
 * @method \ryunosuke\PHPUnit\Actual allIsResource()
 * @method \ryunosuke\PHPUnit\Actual isResource()
 * @method \ryunosuke\PHPUnit\Actual isNotResource()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["string"]
 * @method \ryunosuke\PHPUnit\Actual allIsString()
 * @method \ryunosuke\PHPUnit\Actual isString()
 * @method \ryunosuke\PHPUnit\Actual isNotString()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["scalar"]
 * @method \ryunosuke\PHPUnit\Actual allIsScalar()
 * @method \ryunosuke\PHPUnit\Actual isScalar()
 * @method \ryunosuke\PHPUnit\Actual isNotScalar()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["callable"]
 * @method \ryunosuke\PHPUnit\Actual allIsCallable()
 * @method \ryunosuke\PHPUnit\Actual isCallable()
 * @method \ryunosuke\PHPUnit\Actual isNotCallable()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["iterable"]
 * @method \ryunosuke\PHPUnit\Actual allIsIterable()
 * @method \ryunosuke\PHPUnit\Actual isIterable()
 * @method \ryunosuke\PHPUnit\Actual isNotIterable()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["alnum"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeAlnum()
 * @method \ryunosuke\PHPUnit\Actual isCtypeAlnum()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeAlnum()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["alpha"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeAlpha()
 * @method \ryunosuke\PHPUnit\Actual isCtypeAlpha()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeAlpha()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["cntrl"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeCntrl()
 * @method \ryunosuke\PHPUnit\Actual isCtypeCntrl()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeCntrl()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["digit"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeDigit()
 * @method \ryunosuke\PHPUnit\Actual isCtypeDigit()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeDigit()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["graph"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeGraph()
 * @method \ryunosuke\PHPUnit\Actual isCtypeGraph()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeGraph()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["lower"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeLower()
 * @method \ryunosuke\PHPUnit\Actual isCtypeLower()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeLower()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["print"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypePrint()
 * @method \ryunosuke\PHPUnit\Actual isCtypePrint()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypePrint()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["punct"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypePunct()
 * @method \ryunosuke\PHPUnit\Actual isCtypePunct()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypePunct()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["space"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeSpace()
 * @method \ryunosuke\PHPUnit\Actual isCtypeSpace()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeSpace()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["upper"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeUpper()
 * @method \ryunosuke\PHPUnit\Actual isCtypeUpper()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeUpper()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["xdigit"]
 * @method \ryunosuke\PHPUnit\Actual allIsCtypeXdigit()
 * @method \ryunosuke\PHPUnit\Actual isCtypeXdigit()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeXdigit()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["email"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidEmail($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidEmail($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidEmail($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ip"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidIp($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIp($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIp($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ipv4"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidIpv4($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIpv4($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIpv4($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ipv6"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidIpv6($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIpv6($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIpv6($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["mac"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidMac($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidMac($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidMac($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["url"]
 * @method \ryunosuke\PHPUnit\Actual allIsValidUrl($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidUrl($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidUrl($flags = 0)
 *
 * @see bootstrap.php#8-28
 * @method \ryunosuke\PHPUnit\Actual allLineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual notLineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCountAny(array $lineCounts, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCountAll(array $lineCounts, string $delimiter = '\\R')
 *
 */
trait Annotation
{
    function isHoge()
    {
        return $this->eval(new \PHPUnit\Framework\Constraint\IsEqual('hoge'));
    }
}
