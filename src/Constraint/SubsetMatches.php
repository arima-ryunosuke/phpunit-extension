<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\ExpectationFailedException;
use function ryunosuke\PHPUnit\is_stringable;

class SubsetMatches extends AbstractConstraint
{
    private $subpatterns;

    public function __construct(array $subpatterns)
    {
        $this->subpatterns = $subpatterns;
    }

    protected function failureDescription($other): string
    {
        return $other;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        try {
            $array = $this->toArray($other);
            $index_array = array_filter($array, 'is_int', ARRAY_FILTER_USE_KEY);
            $assoc_array = array_filter($array, 'is_string', ARRAY_FILTER_USE_KEY);

            $index_patterns = array_filter($this->subpatterns, 'is_int', ARRAY_FILTER_USE_KEY);
            $assoc_patterns = array_filter($this->subpatterns, 'is_string', ARRAY_FILTER_USE_KEY);

            foreach ($index_patterns as $subpattern) {
                if (!preg_grep($subpattern, $index_array)) {
                    throw new ExpectationFailedException("actual containes $subpattern");
                }
            }

            foreach ($assoc_patterns as $key => $subpattern) {
                if (!array_key_exists($key, $assoc_array)) {
                    throw new ExpectationFailedException("actual[$key] exists");
                }
                if (!is_stringable($assoc_array[$key])) {
                    throw new ExpectationFailedException("actual[$key] must be stringable");
                }
                if (!preg_match($subpattern, $assoc_array[$key])) {
                    throw new ExpectationFailedException("actual[$key] matches $subpattern");
                }
            }
        }
        catch (ExpectationFailedException $e) {
            if ($returnResult) {
                return false;
            }
            $this->fail($e->getMessage(), '');
        }

        if ($returnResult) {
            return true;
        }
        return null;
    }
}
