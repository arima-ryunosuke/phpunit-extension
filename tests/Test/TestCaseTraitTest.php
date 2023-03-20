<?php

namespace ryunosuke\Test;

use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\SkippedTestError;
use RuntimeException;
use ryunosuke\PHPUnit\TestCaseTrait;
use UnexpectedValueException;

class TestCaseTraitTest extends \ryunosuke\Test\AbstractTestCase
{
    use TestCaseTrait;

    function test_trapThrowable_string_and_do_nothing()
    {
        $this->trapThrowable(RuntimeException::class);

        $this->assertEquals('dummy', 'dummy');
        throw new RuntimeException();
    }

    function test_trapThrowable_string_and_skip()
    {
        $this->trapThrowable(RuntimeException::class, SkippedTestError::class);

        $this->assertEquals('dummy', 'dummy');
        throw new RuntimeException();
    }

    function test_trapThrowable_string_nomatch()
    {
        $this->trapThrowable(RuntimeException::class);

        $this->expectException(UnexpectedValueException::class);
        throw new UnexpectedValueException();
    }

    function test_trapThrowable_notype()
    {
        $this->trapThrowable(function ($t) {
            $this->assertEquals(RuntimeException::class, get_class($t));
        });

        throw new RuntimeException();
    }

    function test_trapThrowable_type_match()
    {
        $this->trapThrowable(function (RuntimeException $t) {
            $this->assertEquals(UnexpectedValueException::class, get_class($t));
        });

        throw new UnexpectedValueException();
    }

    function test_trapThrowable_type_nomatch()
    {
        $this->trapThrowable(function (DomainException $t) {
            $this->fail();
        });

        $this->expectException(UnexpectedValueException::class);
        throw new UnexpectedValueException();
    }

    function test_trapThrowable_canceled()
    {
        $this->expectException(InvalidArgumentException::class);
        throw new InvalidArgumentException();
    }

    /**
     * @backupGlobals enabled
     */
    function test_restorer()
    {
        $currents = [ini_get('memory_limit'), mb_internal_encoding()];

        // change something
        $restorer1 = $this->restorer(fn($v) => ini_set('memory_limit', $v), ['99G']);
        $restorer2 = $this->restorer('mb_internal_encoding', ['SJIS'], [mb_internal_encoding()]);

        // enable changed value (see also test_restorer_php)
        $this->assertEquals('99G', ini_get('memory_limit'));
        $this->assertEquals('SJIS', mb_internal_encoding());

        // recovery explicit
        $restorer1();
        $this->assertEquals($currents[0], ini_get('memory_limit'));

        // recovery implicit (backupGlobals=on, so not recovery)
        unset($restorer2);
        $this->assertEquals('SJIS', mb_internal_encoding());

        return $currents[1];
    }

    /**
     * @depends test_restorer
     */
    function test_restorer_recovery($mb_internal_encoding)
    {
        // auto restore changes of test_restorer
        $this->assertEquals($mb_internal_encoding, mb_internal_encoding());
    }

    function test_rewriteProperty()
    {
        $object = new class() {
            private static $staticProperty  = 'this is static';
            private        $privateProperty = 'this is private';

            public function describe()
            {
                return [
                    'static'  => self::$staticProperty,
                    'private' => $this->privateProperty,
                ];
            }
        };

        $this->assertEquals([
            "static"  => "this is static",
            "private" => "this is private",
        ], $object->describe());

        $static = $this->rewriteProperty($object, 'staticProperty', fn($property) => $property . ' Ex');
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private",
        ], $object->describe());

        $private = $this->rewriteProperty($object, 'privateProperty', fn($property) => $property . ' Ex');
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private Ex",
        ], $object->describe());

        unset($private);
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private",
        ], $object->describe());

        unset($static);
        $this->assertEquals([
            "static"  => "this is static",
            "private" => "this is private",
        ], $object->describe());
    }

    function test_getEnvOrSkip()
    {
        $this->assertEquals('value', $this->getEnvOrSkip('THIS_IS_ENV'));
        $this->expectException(SkippedTestError::class);
        $this->getEnvOrSkip('THIS_IS_ENV_EMPTY');
    }

    function test_getConstantOrSkip()
    {
        $this->assertEquals('value', $this->getConstOrSkip('THIS_IS_CONST'));
        $this->expectException(SkippedTestError::class);
        $this->getConstOrSkip('UNDEFINED_CONST');
    }

    function test_emptyDirectory()
    {
        $directory = $this->emptyDirectory();
        $this->assertFileExists($directory);

        @mkdir("$directory/dummy");
        touch($dummy = "$directory/dummy/dummy.txt");
        $this->assertFileExists($dummy);

        $directory = $this->emptyDirectory();
        $this->assertFileExists($directory);

        $this->assertFileDoesNotExist($dummy);
    }

    function test_backgroundTask()
    {
        $dir = $this->emptyDirectory();
        $task = $this->backgroundTask(function () use ($dir) {
            while (true) {
                sleep(1);
                file_put_contents("$dir/log.txt", "added\n", FILE_APPEND);
            }
        });

        that("$dir/log.txt")->fileNotExists();

        usleep(1000 * 1500);
        that("$dir/log.txt")->fileEquals("added\n");

        $task->terminate();
    }
}
