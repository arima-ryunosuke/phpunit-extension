<?php

namespace ryunosuke\Test;

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
