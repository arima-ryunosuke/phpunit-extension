<?php

namespace ryunosuke\PHPUnit\Printer;

use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\IncompleteTest;
use PHPUnit\Framework\RiskyTestError;
use PHPUnit\Framework\SkippedTestError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Runner\PhptTestCase;
use PHPUnit\TextUI\DefaultResultPrinter;
use Throwable;

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

    private static array $reports = [];

    protected int $numberOfColumns;

    protected ?TestSuite $suite;

    protected ?Test $test;

    protected ?Throwable $failureCause = null;

    protected array $testThrowns = [];

    public function __construct($out = null, bool $verbose = false, $colors = self::COLOR_DEFAULT, bool $debug = false, $numberOfColumns = 80, bool $reverse = false)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);

        $this->numberOfColumns = $numberOfColumns;

        $printResultOnInterrupt = function () {
            foreach ($this->testThrowns as $type => $throwns) {
                $this->writeNewLine();
                $this->writeNewLine();
                $this->printDefects($throwns, $type);
            }
            $this->flush();
            exit;
        };
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, $printResultOnInterrupt);
        }
        elseif (function_exists('sapi_windows_set_ctrl_handler')) {
            sapi_windows_set_ctrl_handler($printResultOnInterrupt);
        }
    }

    public function startTestSuite(TestSuite $suite): void
    {
        $this->suite = $suite;

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
                $export = function_exists('\\ryunosuke\\PHPUnit\\var_export2') ? '\\ryunosuke\\PHPUnit\\var_export2' : 'var_export';
                $actual = $export($actual, true);
            }
            if (is_string($actual) && strpos($actual, "\n") !== false) {
                $this->write("<<<'ACTUAL'\n");
                $this->write("$actual\n");
                $this->write("ACTUAL\n\n");
            }
        }

        parent::printDefectTrace($defect);
    }

    protected function printFooter(TestResult $result): void
    {
        $print = function_exists('\\ryunosuke\\PHPUnit\\var_pretty')
            ? fn($v) => \ryunosuke\PHPUnit\var_pretty($v, ['return' => true, 'context' => $this->colors ? 'cli' : 'plain'])
            : fn($v) => print_r($v, true);

        $defects = [];

        foreach (self::$reports as ['testCase' => $testCase, 'messages' => $messages]) {
            foreach ($messages as $n => $message) {
                if (!(is_string($message) || (is_object($message) && method_exists($message, '__toString')))) {
                    $message = $print($message);
                }
                $messages[$n] = trim($message, "\n");
            }
            $defects[] = new TestFailure($testCase, new class(implode("\n", $messages) . "\n") extends Exception {
                public function __toString()
                {
                    return $this->getMessage();
                }
            });
        }

        $this->printDefects($defects, 'report');

        parent::printFooter($result);
    }

    public static function report(TestCase $testCase, $message)
    {
        $id = spl_object_id($testCase);
        self::$reports[$id] ??= [
            'testCase' => $testCase,
            'messages' => [],
        ];
        self::$reports[$id]['messages'][] = $message;
    }

    public function addError(Test $test, Throwable $t, float $time): void
    {
        $this->testThrowns['errors'][] = new TestFailure($test, $t);
        $this->failureCause = $t;
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        $this->testThrowns['failures'][] = new TestFailure($test, $e);
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
        if ($this->verbose) {
            $this->failureCause = $t;
        }
    }
}
