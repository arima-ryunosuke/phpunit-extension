<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\SkippedTestError;
use ryunosuke\PHPUnit\TestCaseTrait;

class TestCaseTraitTest extends \ryunosuke\Test\AbstractTestCase
{
    use TestCaseTrait;

    function test_rewriteProperty()
    {
        $object = new class {
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
