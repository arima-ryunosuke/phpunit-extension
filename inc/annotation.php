<?php

namespace ryunosuke\PHPUnit;

/**
 * @see \ryunosuke\PHPUnit\Constraint\Contains
 * @method \ryunosuke\PHPUnit\Actual eachContains($needle, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual contains($needle, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual notContains($needle, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual containsAny(array $needles, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual containsAll(array $needles, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual notContainsAny(array $needles, ?bool $strict = NULL)
 * @method \ryunosuke\PHPUnit\Actual notContainsAll(array $needles, ?bool $strict = NULL)
 *
 * @see \ryunosuke\PHPUnit\Constraint\EqualsFile
 * @method \ryunosuke\PHPUnit\Actual eachEqualsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsFile($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFileAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsFileAll(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsFileAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsFileAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\EqualsIgnoreWS
 * @method \ryunosuke\PHPUnit\Actual eachEqualsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreWS($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWSAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreWSAll(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreWSAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreWSAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\FileContains
 * @method \ryunosuke\PHPUnit\Actual eachFileContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotContains($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContainsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileContainsAll(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotContainsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotContainsAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\FileEquals
 * @method \ryunosuke\PHPUnit\Actual eachFileEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotEquals($value, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEqualsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileEqualsAll(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotEqualsAny(array $values, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual fileNotEqualsAll(array $values, bool $ignoreCase = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\FileSizeIs
 * @method \ryunosuke\PHPUnit\Actual eachFileSizeIs(int $size)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIs(int $size)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIsNot(int $size)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIsAny(array $sizes)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIsAll(array $sizes)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIsNotAny(array $sizes)
 * @method \ryunosuke\PHPUnit\Actual fileSizeIsNotAll(array $sizes)
 *
 * @see \ryunosuke\PHPUnit\Constraint\HasKey
 * @method \ryunosuke\PHPUnit\Actual eachHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual hasKey($key)
 * @method \ryunosuke\PHPUnit\Actual notHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual hasKeyAny(array $keys)
 * @method \ryunosuke\PHPUnit\Actual hasKeyAll(array $keys)
 * @method \ryunosuke\PHPUnit\Actual notHasKeyAny(array $keys)
 * @method \ryunosuke\PHPUnit\Actual notHasKeyAll(array $keys)
 *
 * @see \ryunosuke\PHPUnit\Constraint\HtmlMatchesArray
 * @method \ryunosuke\PHPUnit\Actual eachHtmlMatchesArray($nodes)
 * @method \ryunosuke\PHPUnit\Actual htmlMatchesArray($nodes)
 * @method \ryunosuke\PHPUnit\Actual htmlNotMatchesArray($nodes)
 * @method \ryunosuke\PHPUnit\Actual htmlMatchesArrayAny(array $nodess)
 * @method \ryunosuke\PHPUnit\Actual htmlMatchesArrayAll(array $nodess)
 * @method \ryunosuke\PHPUnit\Actual htmlNotMatchesArrayAny(array $nodess)
 * @method \ryunosuke\PHPUnit\Actual htmlNotMatchesArrayAll(array $nodess)
 *
 * @see \ryunosuke\PHPUnit\Constraint\InTime
 * @method \ryunosuke\PHPUnit\Actual eachInTime(float $time)
 * @method \ryunosuke\PHPUnit\Actual inTime(float $time)
 * @method \ryunosuke\PHPUnit\Actual notInTime(float $time)
 * @method \ryunosuke\PHPUnit\Actual inTimeAny(array $times)
 * @method \ryunosuke\PHPUnit\Actual inTimeAll(array $times)
 * @method \ryunosuke\PHPUnit\Actual notInTimeAny(array $times)
 * @method \ryunosuke\PHPUnit\Actual notInTimeAll(array $times)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsBetween
 * @method \ryunosuke\PHPUnit\Actual eachIsBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isNotBetween($min, $max)
 * @method \ryunosuke\PHPUnit\Actual isBetweenAny(array $minmaxs)
 * @method \ryunosuke\PHPUnit\Actual isBetweenAll(array $minmaxs)
 * @method \ryunosuke\PHPUnit\Actual isNotBetweenAny(array $minmaxs)
 * @method \ryunosuke\PHPUnit\Actual isNotBetweenAll(array $minmaxs)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsBlank
 * @method \ryunosuke\PHPUnit\Actual eachIsBlank(bool $trim = true)
 * @method \ryunosuke\PHPUnit\Actual isBlank(bool $trim = true)
 * @method \ryunosuke\PHPUnit\Actual isNotBlank(bool $trim = true)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType
 * @method \ryunosuke\PHPUnit\Actual eachIsCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isNotCType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isCTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isCTypeAll(array $types)
 * @method \ryunosuke\PHPUnit\Actual isNotCTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isNotCTypeAll(array $types)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsFalsy
 * @method \ryunosuke\PHPUnit\Actual eachIsFalsy()
 * @method \ryunosuke\PHPUnit\Actual isFalsy()
 * @method \ryunosuke\PHPUnit\Actual isNotFalsy()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsThrowable
 * @method \ryunosuke\PHPUnit\Actual eachIsThrowable($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual isThrowable($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual isNotThrowable($expected = NULL)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsTruthy
 * @method \ryunosuke\PHPUnit\Actual eachIsTruthy()
 * @method \ryunosuke\PHPUnit\Actual isTruthy()
 * @method \ryunosuke\PHPUnit\Actual isNotTruthy()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid
 * @method \ryunosuke\PHPUnit\Actual eachIsValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValid(string $type, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidAny(array $types, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidAll(array $types, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidAny(array $types, $flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidAll(array $types, $flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\JsonMatchesArray
 * @method \ryunosuke\PHPUnit\Actual eachJsonMatchesArray(array $expected, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesArray(array $expected, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatchesArray(array $expected, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesArrayAny(array $expecteds, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesArrayAll(array $expecteds, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatchesArrayAny(array $expecteds, bool $subset = false)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatchesArrayAll(array $expecteds, bool $subset = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\LengthEquals
 * @method \ryunosuke\PHPUnit\Actual eachLengthEquals(int $length)
 * @method \ryunosuke\PHPUnit\Actual lengthEquals(int $length)
 * @method \ryunosuke\PHPUnit\Actual lengthNotEquals(int $length)
 * @method \ryunosuke\PHPUnit\Actual lengthEqualsAny(array $lengths)
 * @method \ryunosuke\PHPUnit\Actual lengthEqualsAll(array $lengths)
 * @method \ryunosuke\PHPUnit\Actual lengthNotEqualsAny(array $lengths)
 * @method \ryunosuke\PHPUnit\Actual lengthNotEqualsAll(array $lengths)
 *
 * @see \ryunosuke\PHPUnit\Constraint\OutputMatches
 * @method \ryunosuke\PHPUnit\Actual eachOutputMatches($value, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputMatches($value, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputNotMatches($value, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputMatchesAny(array $values, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputMatchesAll(array $values, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputNotMatchesAny(array $values, $raw = false)
 * @method \ryunosuke\PHPUnit\Actual outputNotMatchesAll(array $values, $raw = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\StringLengthEquals
 * @method \ryunosuke\PHPUnit\Actual eachStringLengthEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthNotEquals(int $length, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEqualsAny(array $lengths, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthEqualsAll(array $lengths, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthNotEqualsAny(array $lengths, bool $multibyte = false)
 * @method \ryunosuke\PHPUnit\Actual stringLengthNotEqualsAll(array $lengths, bool $multibyte = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\SubsetEquals
 * @method \ryunosuke\PHPUnit\Actual eachSubsetEquals($subset, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetEquals($subset, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetNotEquals($subset, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetEqualsAny(array $subsets, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetEqualsAll(array $subsets, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetNotEqualsAny(array $subsets, bool $canonicalize = false)
 * @method \ryunosuke\PHPUnit\Actual subsetNotEqualsAll(array $subsets, bool $canonicalize = false)
 *
 * @see \ryunosuke\PHPUnit\Constraint\SubsetMatches
 * @method \ryunosuke\PHPUnit\Actual eachSubsetMatches(array $subpatterns)
 * @method \ryunosuke\PHPUnit\Actual subsetMatches(array $subpatterns)
 * @method \ryunosuke\PHPUnit\Actual subsetNotMatches(array $subpatterns)
 * @method \ryunosuke\PHPUnit\Actual subsetMatchesAny(array $subpatternss)
 * @method \ryunosuke\PHPUnit\Actual subsetMatchesAll(array $subpatternss)
 * @method \ryunosuke\PHPUnit\Actual subsetNotMatchesAny(array $subpatternss)
 * @method \ryunosuke\PHPUnit\Actual subsetNotMatchesAll(array $subpatternss)
 *
 * @see \ryunosuke\PHPUnit\Constraint\Throws
 * @method \ryunosuke\PHPUnit\Actual eachThrows($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual throws($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual notThrows($expected = NULL)
 *
 * @see \PHPUnit\Framework\Constraint\IsFalse
 * @method \ryunosuke\PHPUnit\Actual eachIsFalse()
 * @method \ryunosuke\PHPUnit\Actual isFalse()
 * @method \ryunosuke\PHPUnit\Actual isNotFalse()
 *
 * @see \PHPUnit\Framework\Constraint\IsTrue
 * @method \ryunosuke\PHPUnit\Actual eachIsTrue()
 * @method \ryunosuke\PHPUnit\Actual isTrue()
 * @method \ryunosuke\PHPUnit\Actual isNotTrue()
 *
 * @see \PHPUnit\Framework\Constraint\Callback
 * @method \ryunosuke\PHPUnit\Actual eachCallback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual callback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual notCallback(callable $callback)
 * @method \ryunosuke\PHPUnit\Actual callbackAny(array $callbacks)
 * @method \ryunosuke\PHPUnit\Actual callbackAll(array $callbacks)
 * @method \ryunosuke\PHPUnit\Actual notCallbackAny(array $callbacks)
 * @method \ryunosuke\PHPUnit\Actual notCallbackAll(array $callbacks)
 *
 * @see \PHPUnit\Framework\Constraint\Count
 * @method \ryunosuke\PHPUnit\Actual eachCount(int $expected)
 * @method \ryunosuke\PHPUnit\Actual count(int $expected)
 * @method \ryunosuke\PHPUnit\Actual notCount(int $expected)
 * @method \ryunosuke\PHPUnit\Actual countAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual countAll(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notCountAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notCountAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\GreaterThan
 * @method \ryunosuke\PHPUnit\Actual eachGreaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual greaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual notGreaterThan($value)
 * @method \ryunosuke\PHPUnit\Actual greaterThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual greaterThanAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGreaterThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGreaterThanAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEmpty
 * @method \ryunosuke\PHPUnit\Actual eachIsEmpty()
 * @method \ryunosuke\PHPUnit\Actual isEmpty()
 * @method \ryunosuke\PHPUnit\Actual isNotEmpty()
 *
 * @see \PHPUnit\Framework\Constraint\LessThan
 * @method \ryunosuke\PHPUnit\Actual eachLessThan($value)
 * @method \ryunosuke\PHPUnit\Actual lessThan($value)
 * @method \ryunosuke\PHPUnit\Actual notLessThan($value)
 * @method \ryunosuke\PHPUnit\Actual lessThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual lessThanAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLessThanAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLessThanAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\SameSize
 * @method \ryunosuke\PHPUnit\Actual eachSameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual sameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual notSameSize(iterable $expected)
 * @method \ryunosuke\PHPUnit\Actual sameSizeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual sameSizeAll(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notSameSizeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notSameSizeAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual
 * @method \ryunosuke\PHPUnit\Actual eachIsEqual($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqual($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotEqual($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqualAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isEqualAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqualCanonicalizing
 * @method \ryunosuke\PHPUnit\Actual eachIsEqualCanonicalizing($value)
 * @method \ryunosuke\PHPUnit\Actual isEqualCanonicalizing($value)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualCanonicalizing($value)
 * @method \ryunosuke\PHPUnit\Actual isEqualCanonicalizingAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isEqualCanonicalizingAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualCanonicalizingAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualCanonicalizingAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqualIgnoringCase
 * @method \ryunosuke\PHPUnit\Actual eachIsEqualIgnoringCase($value)
 * @method \ryunosuke\PHPUnit\Actual isEqualIgnoringCase($value)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualIgnoringCase($value)
 * @method \ryunosuke\PHPUnit\Actual isEqualIgnoringCaseAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isEqualIgnoringCaseAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualIgnoringCaseAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualIgnoringCaseAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqualWithDelta
 * @method \ryunosuke\PHPUnit\Actual eachIsEqualWithDelta($value, float $delta)
 * @method \ryunosuke\PHPUnit\Actual isEqualWithDelta($value, float $delta)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualWithDelta($value, float $delta)
 * @method \ryunosuke\PHPUnit\Actual isEqualWithDeltaAny(array $valuedeltas)
 * @method \ryunosuke\PHPUnit\Actual isEqualWithDeltaAll(array $valuedeltas)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualWithDeltaAny(array $valuedeltas)
 * @method \ryunosuke\PHPUnit\Actual isNotEqualWithDeltaAll(array $valuedeltas)
 *
 * @see \PHPUnit\Framework\Constraint\Exception
 * @method \ryunosuke\PHPUnit\Actual eachException(string $className)
 * @method \ryunosuke\PHPUnit\Actual exception(string $className)
 * @method \ryunosuke\PHPUnit\Actual notException(string $className)
 * @method \ryunosuke\PHPUnit\Actual exceptionAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual exceptionAll(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual notExceptionAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual notExceptionAll(array $classNames)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionCode
 * @method \ryunosuke\PHPUnit\Actual eachExceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionCode($expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionCodeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionCodeAll(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionCodeAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionCodeAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionMessage
 * @method \ryunosuke\PHPUnit\Actual eachExceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessage(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageAll(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression
 * @method \ryunosuke\PHPUnit\Actual eachExceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageRegularExpression(string $expected)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpressionAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual exceptionMessageRegularExpressionAll(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageRegularExpressionAny(array $expecteds)
 * @method \ryunosuke\PHPUnit\Actual notExceptionMessageRegularExpressionAll(array $expecteds)
 *
 * @see \PHPUnit\Framework\Constraint\DirectoryExists
 * @method \ryunosuke\PHPUnit\Actual eachDirectoryExists()
 * @method \ryunosuke\PHPUnit\Actual directoryExists()
 * @method \ryunosuke\PHPUnit\Actual directoryNotExists()
 *
 * @see \PHPUnit\Framework\Constraint\FileExists
 * @method \ryunosuke\PHPUnit\Actual eachFileExists()
 * @method \ryunosuke\PHPUnit\Actual fileExists()
 * @method \ryunosuke\PHPUnit\Actual fileNotExists()
 *
 * @see \PHPUnit\Framework\Constraint\IsReadable
 * @method \ryunosuke\PHPUnit\Actual eachIsReadable()
 * @method \ryunosuke\PHPUnit\Actual isReadable()
 * @method \ryunosuke\PHPUnit\Actual isNotReadable()
 *
 * @see \PHPUnit\Framework\Constraint\IsWritable
 * @method \ryunosuke\PHPUnit\Actual eachIsWritable()
 * @method \ryunosuke\PHPUnit\Actual isWritable()
 * @method \ryunosuke\PHPUnit\Actual isNotWritable()
 *
 * @see \PHPUnit\Framework\Constraint\IsAnything
 * @method \ryunosuke\PHPUnit\Actual eachIsAnything()
 * @method \ryunosuke\PHPUnit\Actual isAnything()
 * @method \ryunosuke\PHPUnit\Actual isNotAnything()
 *
 * @see \PHPUnit\Framework\Constraint\IsIdentical
 * @method \ryunosuke\PHPUnit\Actual eachIsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isNotIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual isIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isIdenticalAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotIdenticalAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\JsonMatches
 * @method \ryunosuke\PHPUnit\Actual eachJsonMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual jsonMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatches(string $value)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual jsonMatchesAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatchesAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual jsonNotMatchesAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsFinite
 * @method \ryunosuke\PHPUnit\Actual eachIsFinite()
 * @method \ryunosuke\PHPUnit\Actual isFinite()
 * @method \ryunosuke\PHPUnit\Actual isNotFinite()
 *
 * @see \PHPUnit\Framework\Constraint\IsInfinite
 * @method \ryunosuke\PHPUnit\Actual eachIsInfinite()
 * @method \ryunosuke\PHPUnit\Actual isInfinite()
 * @method \ryunosuke\PHPUnit\Actual isNotInfinite()
 *
 * @see \PHPUnit\Framework\Constraint\IsNan
 * @method \ryunosuke\PHPUnit\Actual eachIsNan()
 * @method \ryunosuke\PHPUnit\Actual isNan()
 * @method \ryunosuke\PHPUnit\Actual isNotNan()
 *
 * @see \PHPUnit\Framework\Constraint\ClassHasAttribute
 * @method \ryunosuke\PHPUnit\Actual eachClassHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classNotHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classHasAttributeAll(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classNotHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classNotHasAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\ClassHasStaticAttribute
 * @method \ryunosuke\PHPUnit\Actual eachClassHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classNotHasStaticAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classHasStaticAttributeAll(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classNotHasStaticAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual classNotHasStaticAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\ObjectEquals
 * @method \ryunosuke\PHPUnit\Actual eachObjectEquals(object $object, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectEquals(object $object, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectNotEquals(object $object, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectEqualsAny(array $objects, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectEqualsAll(array $objects, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectNotEqualsAny(array $objects, string $method = 'equals')
 * @method \ryunosuke\PHPUnit\Actual objectNotEqualsAll(array $objects, string $method = 'equals')
 *
 * @see \PHPUnit\Framework\Constraint\ObjectHasAttribute
 * @method \ryunosuke\PHPUnit\Actual eachObjectHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual objectNotHasAttribute(string $attributeName)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual objectHasAttributeAll(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual objectNotHasAttributeAny(array $attributeNames)
 * @method \ryunosuke\PHPUnit\Actual objectNotHasAttributeAll(array $attributeNames)
 *
 * @see \PHPUnit\Framework\Constraint\IsJson
 * @method \ryunosuke\PHPUnit\Actual eachIsJson()
 * @method \ryunosuke\PHPUnit\Actual isJson()
 * @method \ryunosuke\PHPUnit\Actual isNotJson()
 *
 * @see \PHPUnit\Framework\Constraint\RegularExpression
 * @method \ryunosuke\PHPUnit\Actual eachRegularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual regularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual notRegularExpression(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual regularExpressionAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual regularExpressionAll(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual notRegularExpressionAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual notRegularExpressionAll(array $patterns)
 *
 * @see \PHPUnit\Framework\Constraint\StringContains
 * @method \ryunosuke\PHPUnit\Actual eachStringContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringNotContains(string $string, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContainsAny(array $strings, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringContainsAll(array $strings, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringNotContainsAny(array $strings, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual stringNotContainsAll(array $strings, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\StringEndsWith
 * @method \ryunosuke\PHPUnit\Actual eachStringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual notStringEndsWith(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWithAny(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual stringEndsWithAll(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual notStringEndsWithAny(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual notStringEndsWithAll(array $suffixs)
 *
 * @see \PHPUnit\Framework\Constraint\StringMatchesFormatDescription
 * @method \ryunosuke\PHPUnit\Actual eachStringMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual stringNotMatchesFormatDescription(string $string)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescriptionAny(array $strings)
 * @method \ryunosuke\PHPUnit\Actual stringMatchesFormatDescriptionAll(array $strings)
 * @method \ryunosuke\PHPUnit\Actual stringNotMatchesFormatDescriptionAny(array $strings)
 * @method \ryunosuke\PHPUnit\Actual stringNotMatchesFormatDescriptionAll(array $strings)
 *
 * @see \PHPUnit\Framework\Constraint\StringStartsWith
 * @method \ryunosuke\PHPUnit\Actual eachStringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual notStringStartsWith(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWithAny(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual stringStartsWithAll(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual notStringStartsWithAny(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual notStringStartsWithAll(array $prefixs)
 *
 * @see \PHPUnit\Framework\Constraint\ArrayHasKey
 * @method \ryunosuke\PHPUnit\Actual eachArrayHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual arrayNotHasKey($key)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKeyAny(array $keys)
 * @method \ryunosuke\PHPUnit\Actual arrayHasKeyAll(array $keys)
 * @method \ryunosuke\PHPUnit\Actual arrayNotHasKeyAny(array $keys)
 * @method \ryunosuke\PHPUnit\Actual arrayNotHasKeyAll(array $keys)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsEqual
 * @method \ryunosuke\PHPUnit\Actual eachTraversableContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsEqual($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqualAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsEqualAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsEqualAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsEqualAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsIdentical
 * @method \ryunosuke\PHPUnit\Actual eachTraversableContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsIdentical($value)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsIdenticalAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsIdenticalAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsIdenticalAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\TraversableContainsOnly
 * @method \ryunosuke\PHPUnit\Actual eachTraversableContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsOnly(string $type, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnlyAny(array $types, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableContainsOnlyAll(array $types, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsOnlyAny(array $types, bool $isNativeType = true)
 * @method \ryunosuke\PHPUnit\Actual traversableNotContainsOnlyAll(array $types, bool $isNativeType = true)
 *
 * @see \PHPUnit\Framework\Constraint\IsInstanceOf
 * @method \ryunosuke\PHPUnit\Actual eachIsInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isNotInstanceOf(string $className)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOfAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual isInstanceOfAll(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual isNotInstanceOfAny(array $classNames)
 * @method \ryunosuke\PHPUnit\Actual isNotInstanceOfAll(array $classNames)
 *
 * @see \PHPUnit\Framework\Constraint\IsNull
 * @method \ryunosuke\PHPUnit\Actual eachIsNull()
 * @method \ryunosuke\PHPUnit\Actual isNull()
 * @method \ryunosuke\PHPUnit\Actual isNotNull()
 *
 * @see \PHPUnit\Framework\Constraint\IsType
 * @method \ryunosuke\PHPUnit\Actual eachIsType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isNotType(string $type)
 * @method \ryunosuke\PHPUnit\Actual isTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isTypeAll(array $types)
 * @method \ryunosuke\PHPUnit\Actual isNotTypeAny(array $types)
 * @method \ryunosuke\PHPUnit\Actual isNotTypeAll(array $types)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachIs($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual is($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNot($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual isNotAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsIdentical::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachIsSame($value)
 * @method \ryunosuke\PHPUnit\Actual isSame($value)
 * @method \ryunosuke\PHPUnit\Actual isNotSame($value)
 * @method \ryunosuke\PHPUnit\Actual isSameAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isSameAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotSameAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual isNotSameAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\StringStartsWith::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachPrefixIs(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual prefixIs(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual prefixIsNot(string $prefix)
 * @method \ryunosuke\PHPUnit\Actual prefixIsAny(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual prefixIsAll(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual prefixIsNotAny(array $prefixs)
 * @method \ryunosuke\PHPUnit\Actual prefixIsNotAll(array $prefixs)
 *
 * @see \PHPUnit\Framework\Constraint\StringEndsWith::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachSuffixIs(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual suffixIs(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual suffixIsNot(string $suffix)
 * @method \ryunosuke\PHPUnit\Actual suffixIsAny(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual suffixIsAll(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual suffixIsNotAny(array $suffixs)
 * @method \ryunosuke\PHPUnit\Actual suffixIsNotAll(array $suffixs)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct() {"canonicalize":true}
 * @method \ryunosuke\PHPUnit\Actual eachEqualsCanonicalizing($value, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizing($value, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsCanonicalizing($value, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizingAny(array $values, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual equalsCanonicalizingAll(array $values, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsCanonicalizingAny(array $values, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 * @method \ryunosuke\PHPUnit\Actual notEqualsCanonicalizingAll(array $values, float $delta = 0.0, bool $canonicalize = true, bool $ignoreCase = false)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct() {"ignoreCase":true}
 * @method \ryunosuke\PHPUnit\Actual eachEqualsIgnoreCase($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCase($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreCase($value, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCaseAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual equalsIgnoreCaseAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreCaseAny(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 * @method \ryunosuke\PHPUnit\Actual notEqualsIgnoreCaseAll(array $values, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = true)
 *
 * @see \PHPUnit\Framework\Constraint\RegularExpression::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachMatches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual matches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual notMatches(string $pattern)
 * @method \ryunosuke\PHPUnit\Actual matchesAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual matchesAll(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual notMatchesAny(array $patterns)
 * @method \ryunosuke\PHPUnit\Actual notMatchesAll(array $patterns)
 *
 * @see \PHPUnit\Framework\Constraint\GreaterThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachGt($value)
 * @method \ryunosuke\PHPUnit\Actual gt($value)
 * @method \ryunosuke\PHPUnit\Actual notGt($value)
 * @method \ryunosuke\PHPUnit\Actual gtAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual gtAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGtAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGtAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\LessThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachLt($value)
 * @method \ryunosuke\PHPUnit\Actual lt($value)
 * @method \ryunosuke\PHPUnit\Actual notLt($value)
 * @method \ryunosuke\PHPUnit\Actual ltAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual ltAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLtAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLtAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct(),\PHPUnit\Framework\Constraint\GreaterThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachGte($value)
 * @method \ryunosuke\PHPUnit\Actual gte($value)
 * @method \ryunosuke\PHPUnit\Actual notGte($value)
 * @method \ryunosuke\PHPUnit\Actual gteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual gteAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notGteAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsEqual::__construct(),\PHPUnit\Framework\Constraint\LessThan::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachLte($value)
 * @method \ryunosuke\PHPUnit\Actual lte($value)
 * @method \ryunosuke\PHPUnit\Actual notLte($value)
 * @method \ryunosuke\PHPUnit\Actual lteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual lteAll(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLteAny(array $values)
 * @method \ryunosuke\PHPUnit\Actual notLteAll(array $values)
 *
 * @see \PHPUnit\Framework\Constraint\IsNull::__construct(),\PHPUnit\Framework\Constraint\IsType::__construct() ["string"]
 * @method \ryunosuke\PHPUnit\Actual eachIsNullOrString()
 * @method \ryunosuke\PHPUnit\Actual isNullOrString()
 * @method \ryunosuke\PHPUnit\Actual isNotNullOrString()
 *
 * @see \ryunosuke\PHPUnit\Constraint\OutputMatches::__construct() {"raw":true}
 * @method \ryunosuke\PHPUnit\Actual eachOutputContains($value, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputContains($value, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputNotContains($value, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputContainsAny(array $values, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputContainsAll(array $values, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputNotContainsAny(array $values, $raw = true)
 * @method \ryunosuke\PHPUnit\Actual outputNotContainsAll(array $values, $raw = true)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsThrowable::__construct()
 * @method \ryunosuke\PHPUnit\Actual eachWasThrown($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual wasThrown($expected = NULL)
 * @method \ryunosuke\PHPUnit\Actual notWasThrown($expected = NULL)
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["array"]
 * @method \ryunosuke\PHPUnit\Actual eachIsArray()
 * @method \ryunosuke\PHPUnit\Actual isArray()
 * @method \ryunosuke\PHPUnit\Actual isNotArray()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["bool"]
 * @method \ryunosuke\PHPUnit\Actual eachIsBool()
 * @method \ryunosuke\PHPUnit\Actual isBool()
 * @method \ryunosuke\PHPUnit\Actual isNotBool()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["float"]
 * @method \ryunosuke\PHPUnit\Actual eachIsFloat()
 * @method \ryunosuke\PHPUnit\Actual isFloat()
 * @method \ryunosuke\PHPUnit\Actual isNotFloat()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["int"]
 * @method \ryunosuke\PHPUnit\Actual eachIsInt()
 * @method \ryunosuke\PHPUnit\Actual isInt()
 * @method \ryunosuke\PHPUnit\Actual isNotInt()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["numeric"]
 * @method \ryunosuke\PHPUnit\Actual eachIsNumeric()
 * @method \ryunosuke\PHPUnit\Actual isNumeric()
 * @method \ryunosuke\PHPUnit\Actual isNotNumeric()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["object"]
 * @method \ryunosuke\PHPUnit\Actual eachIsObject()
 * @method \ryunosuke\PHPUnit\Actual isObject()
 * @method \ryunosuke\PHPUnit\Actual isNotObject()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["resource"]
 * @method \ryunosuke\PHPUnit\Actual eachIsResource()
 * @method \ryunosuke\PHPUnit\Actual isResource()
 * @method \ryunosuke\PHPUnit\Actual isNotResource()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["string"]
 * @method \ryunosuke\PHPUnit\Actual eachIsString()
 * @method \ryunosuke\PHPUnit\Actual isString()
 * @method \ryunosuke\PHPUnit\Actual isNotString()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["scalar"]
 * @method \ryunosuke\PHPUnit\Actual eachIsScalar()
 * @method \ryunosuke\PHPUnit\Actual isScalar()
 * @method \ryunosuke\PHPUnit\Actual isNotScalar()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["callable"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCallable()
 * @method \ryunosuke\PHPUnit\Actual isCallable()
 * @method \ryunosuke\PHPUnit\Actual isNotCallable()
 *
 * @see \PHPUnit\Framework\Constraint\IsType::__construct() ["iterable"]
 * @method \ryunosuke\PHPUnit\Actual eachIsIterable()
 * @method \ryunosuke\PHPUnit\Actual isIterable()
 * @method \ryunosuke\PHPUnit\Actual isNotIterable()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["alnum"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeAlnum()
 * @method \ryunosuke\PHPUnit\Actual isCtypeAlnum()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeAlnum()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["alpha"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeAlpha()
 * @method \ryunosuke\PHPUnit\Actual isCtypeAlpha()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeAlpha()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["cntrl"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeCntrl()
 * @method \ryunosuke\PHPUnit\Actual isCtypeCntrl()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeCntrl()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["digit"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeDigit()
 * @method \ryunosuke\PHPUnit\Actual isCtypeDigit()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeDigit()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["graph"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeGraph()
 * @method \ryunosuke\PHPUnit\Actual isCtypeGraph()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeGraph()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["lower"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeLower()
 * @method \ryunosuke\PHPUnit\Actual isCtypeLower()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeLower()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["print"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypePrint()
 * @method \ryunosuke\PHPUnit\Actual isCtypePrint()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypePrint()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["punct"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypePunct()
 * @method \ryunosuke\PHPUnit\Actual isCtypePunct()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypePunct()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["space"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeSpace()
 * @method \ryunosuke\PHPUnit\Actual isCtypeSpace()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeSpace()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["upper"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeUpper()
 * @method \ryunosuke\PHPUnit\Actual isCtypeUpper()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeUpper()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsCType::__construct() ["xdigit"]
 * @method \ryunosuke\PHPUnit\Actual eachIsCtypeXdigit()
 * @method \ryunosuke\PHPUnit\Actual isCtypeXdigit()
 * @method \ryunosuke\PHPUnit\Actual isNotCtypeXdigit()
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["int"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidInt($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidInt($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidInt($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["float"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidFloat($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidFloat($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidFloat($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["email"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidEmail($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidEmail($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidEmail($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ip"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidIp($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIp($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIp($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ipv4"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidIpv4($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIpv4($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIpv4($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["ipv6"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidIpv6($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidIpv6($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidIpv6($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["mac"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidMac($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidMac($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidMac($flags = 0)
 *
 * @see \ryunosuke\PHPUnit\Constraint\IsValid::__construct() ["url"]
 * @method \ryunosuke\PHPUnit\Actual eachIsValidUrl($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isValidUrl($flags = 0)
 * @method \ryunosuke\PHPUnit\Actual isNotValidUrl($flags = 0)
 *
 * @see tests/bootstrap.php#21-23
 * @method \ryunosuke\PHPUnit\Actual eachLineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual notLineCount(int $lineCount, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCountAny(array $lineCounts, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual lineCountAll(array $lineCounts, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual notLineCountAny(array $lineCounts, string $delimiter = '\\R')
 * @method \ryunosuke\PHPUnit\Actual notLineCountAll(array $lineCounts, string $delimiter = '\\R')
 *
 */
trait Annotation
{
    function isHoge()
    {
        return $this->eval(new \PHPUnit\Framework\Constraint\IsEqual('hoge'));
    }
}
