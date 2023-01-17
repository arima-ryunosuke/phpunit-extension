<?php

namespace ryunosuke\PHPUnit\Constraint;

class MatchesCountEquals extends AbstractConstraint
{
    private $patternCounts;
    private $results;

    public function __construct(array $patternCounts)
    {
        $this->patternCounts = $patternCounts;
    }

    protected function failureDescription($other): string
    {
        $results = [];
        foreach ($this->results as $pattern => $actual) {
            $expected = $this->patternCounts[$pattern] ?? '>0';
            $results[] = "$pattern => expected:$expected, actual:$actual";
        }
        return parent::failureDescription($other) . " \n" . implode("\n", $results);
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $array = $this->toArray($other);

        $this->results = [];

        foreach ($this->patternCounts as $pattern => $count) {
            $matchedCount = count(preg_grep($pattern, $array));

            if ($count === null) {
                if ($matchedCount === 0) {
                    $this->results[$pattern] = $matchedCount;
                }
            }
            else {
                if ($matchedCount !== (int) $count) {
                    $this->results[$pattern] = $matchedCount;
                }
            }
        }

        if ($returnResult) {
            return !$this->results;
        }

        if ($this->results) {
            $this->fail($other, $description);
        }
        return null;
    }
}
