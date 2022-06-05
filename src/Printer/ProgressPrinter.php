<?php

namespace ryunosuke\PHPUnit\Printer;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use Throwable;

class ProgressPrinter extends AbstractPrinter
{
    protected function _onStartTestSuite(TestSuite $testSuite)
    {
        $this->writeWithSpace('# ' . $testSuite->getName());

        $numTestsRun = $this->numTestsRun + $testSuite->count();
        $this->writeWithColor('fg-cyan', sprintf("%3d tests", $testSuite->count()), false);
        $this->writeWithColor('fg-green', sprintf(" (%d/%d)%4d%%", $numTestsRun, $this->numTests, $numTestsRun / $this->numTests * 100), false);
        $this->writeNewLine();
    }

    protected function _onStartTestCase(TestCase $testCase)
    {
        $this->writeWithSpace('- ' . $testCase->getName(true));
    }

    protected function _onFailTestCase(Throwable $failureCause)
    {
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
