<?php

namespace ryunosuke\PHPUnit\Printer;

require_once __DIR__ . '/AbstractPrinter.php';

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Filter;
use ReflectionProperty;
use Throwable;

class ProgressPrinter extends AbstractPrinter
{
    private int $caseCount;
    private int $testCount;

    protected function _onStartTestSuite(TestSuite $testSuite)
    {
        // testcase class only (filter directory, dataprovider etc)
        if (!class_exists($testSuite->getName())) {
            return;
        }

        $this->maxColumn = $this->numberOfColumns - strlen("# XXX% ({$this->numTests}/{$this->numTests}) ");

        $digit = strlen((string) $this->numTests);

        $this->write('#');
        $this->writeWithColor('fg-cyan', sprintf("%4d%% ", $this->numTestsRun / $this->numTests * 100), false);
        $this->writeWithColor('fg-green', sprintf("(%{$digit}d/%{$digit}d)", $this->numTestsRun, $this->numTests), false);

        $this->write(' ' . $testSuite->getName());
        $this->writeNewLine();

        $this->caseCount = 0;
        $this->testCount = $testSuite->count();
    }

    protected function _onStartTestCase(TestCase $testCase)
    {
        $digit = strlen((string) $this->testCount);

        $this->write('    -');
        $this->writeWithColor('fg-cyan', sprintf("%4d%% ", $this->caseCount / $this->testCount * 100), false);
        $this->writeWithColor('fg-green', sprintf("(%{$digit}d/%{$digit}d)", $this->caseCount, $this->testCount), false);
        $this->write(' ' . $testCase->getName(true));

        $this->caseCount++;
    }

    protected function _onFailTestCase(Throwable $failureCause)
    {
        $traces = [];

        // traces for break
        if ($failureCause instanceof Warning) {
            $messages = array_filter(explode("\n", $failureCause->getMessage()), 'strlen');
            foreach ($messages as &$message) {
                if (preg_match('#^(Failed asserting that .+?) in (.+?):(\d+)#u', $message, $m)) {
                    $traces[] = "{$m[2]}:{$m[3]}";
                    $message = $m[1];
                }
            }
            if ($traces) {
                $ref = new ReflectionProperty(Exception::class, 'message');
                $ref->setAccessible(true);
                $ref->setValue($failureCause, implode("\n", $messages));
            }
        }
        else {
            $traces = array_filter(explode("\n", Filter::getFilteredStacktrace($failureCause)), 'strlen');
        }

        $this->write(': ');
        parent::_onFailTestCase($failureCause);

        foreach ($traces as $trace) {
            $this->write("\n        - $trace");
        }
    }

    protected function _onOutputTestCase(string $actualOutput)
    {
        $this->writeNewLine();
        $this->write('    - stdout: ');
        parent::_onOutputTestCase($actualOutput);
    }

    protected function _onPassTestCase(float $time)
    {
        $this->write("\r" . str_repeat(" ", $this->numberOfColumns) . "\r");
    }

    protected function _onEndTestCase(TestCase $testCase, ?Throwable $failureCause, string $actualOutput)
    {
        if ($failureCause || strlen($actualOutput)) {
            $this->writeNewLine();
        }
    }

    protected function printHeader(TestResult $result): void
    {
        $digit = strlen((string) $this->numTests);

        $this->write('#');
        $this->writeWithColor('fg-cyan', sprintf("%4d%% ", $this->numTestsRun / $this->numTests * 100), false);
        $this->writeWithColor('fg-green', sprintf("(%{$digit}d/%{$digit}d)", $this->numTestsRun, $this->numTests), false);

        parent::printHeader($result);
    }
}
