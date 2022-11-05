<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\SkippedTestError;
use ryunosuke\PHPUnit\TestCaseTrait;

class TestCaseTraitTest extends \ryunosuke\Test\AbstractTestCase
{
    use TestCaseTrait;

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
}
