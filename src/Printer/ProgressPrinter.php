<?php

namespace ryunosuke\PHPUnit\Printer;

require_once __DIR__ . '/AbstractPrinter.php';

use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use ReflectionProperty;
use Throwable;

class ProgressPrinter extends AbstractPrinter
{
    private int $caseCount;
    private int $testCount;

    protected function _onStartTestSuite(TestSuite $testSuite)
    {
        $digit = strlen((string) $this->numTests);

        $this->maxColumn = $this->numberOfColumns - strlen('C tests (L/M/N) XXX%') - (4 * ($digit - 1));

        $this->writeWithSpace('# ' . $testSuite->getName());

        $numTestsRun = $this->numTestsRun + $testSuite->count();
        $this->writeWithColor('fg-cyan', sprintf("%4d%% ", $numTestsRun / $this->numTests * 100), false);
        $this->writeWithColor('fg-green', sprintf("(%{$digit}d/%{$digit}d/%{$digit}d)", $testSuite->count(), $numTestsRun, $this->numTests), false);
        $this->writeNewLine();

        $this->caseCount = 0;
        $this->testCount = $testSuite->count();
    }

    protected function _onStartTestCase(TestCase $testCase)
    {
        $this->caseCount++;
        $digit = strlen((string) $this->testCount);

        $this->write('- ');
        $this->writeWithColor('fg-cyan', sprintf("%4d%% ", $this->caseCount / $this->testCount * 100), false);
        $this->writeWithColor('fg-green', sprintf("(%{$digit}d/%{$digit}d)", $this->caseCount, $this->testCount), false);
        $this->write(' ' . $testCase->getName(true));
    }

    protected function _onFailTestCase(Throwable $failureCause)
    {
        if ($failureCause instanceof AssertionFailedError) {
            foreach ($failureCause->getTrace() as $trace) {
                if (isset($trace['file'], $trace['line']) && $trace['file'] === (new \ReflectionClass($this->test))->getFileName()) {
                    $this->write(sprintf(': %s:%d', $trace['file'], $trace['line']));
                    break;
                }
            }
        }
        elseif ($failureCause instanceof Warning) {
            $messages = explode("\n", $failureCause->getMessage());
            $filelines = [];
            foreach ($messages as &$message) {
                if (preg_match('#^(Failed asserting that .+?) in (.+?):(\d+)#u', $message, $m)) {
                    $filelines[$m[2]][] = $m[3];
                    $message = $m[1];
                }
            }
            if ($filelines) {
                $this->write(': ');
                foreach ($filelines as $file => $lines) {
                    $this->write("$file:" . implode(',', $lines));
                }
                $ref = new ReflectionProperty(Exception::class, 'message');
                $ref->setAccessible(true);
                $ref->setValue($failureCause, implode("\n", $messages));
            }
        }
        else {
            $this->write(sprintf(': %s:%d', $failureCause->getFile(), $failureCause->getLine()));
        }

        $this->writeNewLine();
        $this->write('    - status: ');
        parent::_onFailTestCase($failureCause);
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
}
