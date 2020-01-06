<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\SelfDescribing;
use ryunosuke\PHPUnit\Util;

class UtilTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_relativizeFile()
    {
        $DS = DIRECTORY_SEPARATOR;

        $path = __FILE__;
        $this->assertEquals("tests{$DS}Test{$DS}UtilTest.php", Util::relativizeFile($path, 'vendor'));
        $this->assertEquals("UtilTest.php", Util::relativizeFile($path, 'notfound'));

        $path = __DIR__ . '/../../vendor/phpunit/phpunit/src/Exception.php';
        $this->assertEquals("vendor{$DS}phpunit{$DS}phpunit{$DS}src{$DS}Exception.php", Util::relativizeFile($path, '.gitignore'));
        $this->assertEquals("Exception.php", Util::relativizeFile($path, 'notfound'));
    }

    function test_reflectFile()
    {
        $DS = DIRECTORY_SEPARATOR;

        $ref = new \ReflectionClass($this);
        $this->assertEquals("tests{$DS}Test{$DS}UtilTest.php#" . $ref->getStartLine() . '-' . $ref->getEndLine(), Util::reflectFile($ref));
        $this->assertEquals("tests{$DS}Test{$DS}UtilTest.php", Util::reflectFile($ref, '%s'));
    }

    function test_callableToString()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertEquals("strlen", Util::callableToString("strlen"));

        $this->assertEquals("ryunosuke\\Test\\UtilTest::setUpBeforeClass", Util::callableToString([__CLASS__, 'setUpBeforeClass']));
        $this->assertEquals("ryunosuke\\Test\\UtilTest::setUpBeforeClass", Util::callableToString(__CLASS__ . '::setUpBeforeClass'));

        $actual = Util::callableToString(function () { });
        $this->assertStringStartsWith("Closure@tests{$DS}Test{$DS}UtilTest.php#", $actual);

        $object = new class
        {
            public function __invoke() { }

            public function method() { }
        };

        $actual = Util::callableToString([$object, 'method']);
        $this->assertStringStartsWith("AnonymousClass@tests{$DS}Test{$DS}UtilTest.php#", $actual);
        $this->assertStringEndsWith("::method", $actual);

        $actual = Util::callableToString($object);
        $this->assertStringStartsWith("AnonymousClass@tests{$DS}Test{$DS}UtilTest.php#", $actual);
        $this->assertStringEndsWith("::__invoke", $actual);

        $object = new class implements SelfDescribing
        {
            public function __invoke() { }

            public function toString(): string
            {
                return 'Hoge::fuga';
            }
        };

        $actual = Util::callableToString($object);
        $this->assertStringStartsWith("Hoge@tests{$DS}Test{$DS}UtilTest.php#", $actual);
        $this->assertStringEndsWith("::fuga", $actual);
    }
}
