<?php

namespace ryunosuke\PHPUnit\Printer;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\IncompleteTest;
use PHPUnit\Framework\RiskyTestError;
use PHPUnit\Framework\SkippedTestError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Runner\PhptTestCase;
use PHPUnit\TextUI\DefaultResultPrinter;
use Throwable;
use function ryunosuke\PHPUnit\var_export2;

class AbstractPrinter extends DefaultResultPrinter
{
    protected const FAILURE_MAP = [
        SkippedTestError::class     => ['fg-cyan, bold', 'Skip'],
        RiskyTestError::class       => ['fg-yellow, bold', 'Risky'],
        IncompleteTest::class       => ['fg-yellow, bold', 'Incomplete'],
        Warning::class              => ['fg-yellow, bold', 'Warning'],
        AssertionFailedError::class => ['bg-red, fg-white', 'Failure'],
        Throwable::class            => ['fg-red, bold', 'Error'],
    ];

    protected int $numberOfColumns;

    protected ?TestSuite $suite;

    protected ?Test $test;

    protected ?Throwable $failureCause = null;

    public function __construct($out = null, bool $verbose = false, $colors = self::COLOR_DEFAULT, bool $debug = false, $numberOfColumns = 80, bool $reverse = false)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);

        $this->numberOfColumns = (fn() => $this->numberOfColumns)->bindTo($this, parent::class)();
    }

    public function startTestSuite(TestSuite $suite): void
    {
        $this->suite = $suite;

        if ($this->numTests == -1) {
            $this->maxColumn = $this->numberOfColumns - strlen(' CCC tests (NNN/MMMM)  XXX%');
        }

        parent::startTestSuite($suite);

        // testcase class only (filter directory, dataprovider etc)
        if (class_exists($suite->getName())) {
            $this->_onStartTestSuite($suite);
        }
    }

    public function endTestSuite(TestSuite $suite): void
    {
        // testcase class only (filter directory, dataprovider etc)
        if (class_exists($suite->getName())) {
            $this->_onEndTestSuite($suite);
        }

        parent::endTestSuite($suite);

        $this->suite = null;
    }

    public function startTest(Test $test): void
    {
        $this->test = $test;

        if ($test instanceof TestCase) {
            $this->_onStartTestCase($test);
        }
    }

    public function endTest(Test $test, float $time): void
    {
        $this->numTestsRun++;

        $actualOutput = $test instanceof TestCase && !$test->hasExpectationOnOutput() ? trim($test->getActualOutput()) : '';

        if ($this->failureCause || strlen($actualOutput)) {
            if ($this->failureCause) {
                $this->_onFailTestCase($this->failureCause);
            }

            if (strlen($actualOutput)) {
                $this->_onOutputTestCase($actualOutput);
            }
        }
        else {
            $this->_onPassTestCase($time);
        }

        if ($test instanceof TestCase) {
            $this->_onEndTestCase($test, $this->failureCause, $actualOutput);
        }

        $this->failureCause = null;

        if ($test instanceof TestCase) {
            $this->numAssertions += $test->getNumAssertions();
        }
        elseif ($test instanceof PhptTestCase) {
            $this->numAssertions++;
        }

        $this->test = null;
    }

    protected function _onStartTestSuite(TestSuite $testSuite)
    {
    }

    protected function _onStartTestCase(TestCase $testCase)
    {
    }

    protected function _onPassTestCase(float $time)
    {
    }

    protected function _onFailTestCase(Throwable $failureCause)
    {
        foreach (self::FAILURE_MAP as $type => $args) {
            if (is_a($failureCause, $type, true)) {
                $args[] = false;
                $this->writeWithColor(...$args);
                break;
            }
        }
        $message = $failureCause->getMessage();
        if (strlen($message)) {
            if ($failureCause instanceof ExpectationFailedException) {
                $message = explode("\n", $message, 2)[0];
            }
            $this->write(' (' . $message . ')');
        }
    }

    protected function _onOutputTestCase(string $actualOutput)
    {
        if (strpos($actualOutput, "\n") === false) {
            $this->write($actualOutput);
        }
        else {
            $this->write("```");
            $this->writeNewLine();
            $this->write($actualOutput);
            $this->writeNewLine();
            $this->write("```");
        }
    }

    protected function _onEndTestCase(TestCase $testCase, ?Throwable $failureCause, string $actualOutput)
    {
    }

    protected function _onEndTestSuite(TestSuite $testSuite)
    {
    }

    protected function writeWithSpace(string $buffer, int $pad_type = STR_PAD_RIGHT): void
    {
        $width = $this->maxColumn - mb_strwidth($buffer);
        $padlength = max(0, $width);
        switch ($pad_type) {
            case STR_PAD_LEFT:
                $this->write(str_repeat(" ", $padlength) . $buffer);
                break;
            case STR_PAD_RIGHT:
                $this->write($buffer . str_repeat(" ", $padlength));
                break;
            case STR_PAD_BOTH:
                $this->write(str_repeat(" ", ceil($padlength / 2)) . $buffer . str_repeat(" ", floor($padlength / 2)));
                break;
        }
    }

    protected function printDefectTrace(TestFailure $defect): void
    {
        $e = $defect->thrownException();
        if ($e instanceof ExpectationFailedException && $e->getComparisonFailure()) {
            $actual = $e->getComparisonFailure()->getActual();
            if (is_array($actual)) {
                $actual = var_export2($actual, true);
            }
            if (is_string($actual) && strpos($actual, "\n") !== false) {
                $this->write("<<<'ACTUAL'\n");
                $this->write("$actual\n");
                $this->write("ACTUAL\n\n");
            }
        }

        parent::printDefectTrace($defect);
    }

    public function addError(Test $test, Throwable $t, float $time): void
    {
        $this->failureCause = $t;
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        $this->failureCause = $e;
    }

    public function addWarning(Test $test, Warning $e, float $time): void
    {
        $this->failureCause = $e;
    }

    public function addIncompleteTest(Test $test, Throwable $t, float $time): void
    {
        $this->failureCause = $t;
    }

    public function addRiskyTest(Test $test, Throwable $t, float $time): void
    {
        $this->failureCause = $t;
    }

    public function addSkippedTest(Test $test, Throwable $t, float $time): void
    {
        $this->failureCause = $t;
    }
}
