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

    function test_propertyToValue()
    {
        $object = new class('testname') extends \ryunosuke\Test\AbstractTestCase {
            public static $staticProperty  = 'this is static';
            private       $privateProperty = 'this is private';
            public        $publicProperty  = 'this is public';

            public function __get($name)
            {
                assert(strlen($this->privateProperty));
                return "this is magic";
            }
        };
        /** @noinspection PhpUndefinedFieldInspection */
        $object->dynamicProperty = 'this is dynamic';

        $this->assertEquals("this is static", Util::propertyToValue(get_class($object), 'staticProperty'));
        $this->assertEquals("this is static", Util::propertyToValue($object, 'staticProperty'));
        $this->assertEquals("this is private", Util::propertyToValue($object, 'privateProperty'));
        $this->assertEquals("this is public", Util::propertyToValue($object, 'publicProperty'));
        $this->assertEquals("this is magic", Util::propertyToValue($object, 'magicProperty'));
        $this->assertEquals("this is dynamic", Util::propertyToValue($object, 'dynamicProperty'));
        $this->assertEquals("testname", Util::propertyToValue($object, 'name'));

        $this->expectExceptionMessage('is not defined');
        Util::propertyToValue(new \stdClass(), 'undefined');
    }

    function test_methodToCallable()
    {
        $class = new class {
            public static function staticMethod() { return __FUNCTION__; }

            protected function privateMethod() { return __FUNCTION__; }

            public function publicMethod() { return __FUNCTION__; }

            public function __call($name, $arguments) { return __FUNCTION__; }
        };

        $staticMethod = Util::methodToCallable(get_class($class), 'staticMethod');
        $this->assertEquals('staticMethod', $staticMethod());

        $staticMethod = Util::methodToCallable($class, 'staticMethod');
        $this->assertEquals('staticMethod', $staticMethod());

        $privateMethod = Util::methodToCallable($class, 'privateMethod');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('AnonymousClass::privateMethod', $privateMethod->toString());
        $this->assertEquals('privateMethod', $privateMethod());

        $publicMethod = Util::methodToCallable($class, 'publicMethod');
        $this->assertEquals('publicMethod', $publicMethod());

        $magicMethod = Util::methodToCallable($class, 'magicMethod');
        $this->assertEquals('__call', $magicMethod());

        $this->expectExceptionMessage('is not defined');
        Util::methodToCallable(new \stdClass(), 'undefined');
    }

    function test_callableToString()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertEquals("strlen", Util::callableToString("strlen"));

        $this->assertEquals("ryunosuke\\Test\\UtilTest::setUpBeforeClass", Util::callableToString([__CLASS__, 'setUpBeforeClass']));
        $this->assertEquals("ryunosuke\\Test\\UtilTest::setUpBeforeClass", Util::callableToString(__CLASS__ . '::setUpBeforeClass'));

        $actual = Util::callableToString(function () { });
        $this->assertStringStartsWith("Closure@tests{$DS}Test{$DS}UtilTest.php#", $actual);

        $object = new class {
            public function __invoke() { }

            public function method() { }
        };

        $actual = Util::callableToString([$object, 'method']);
        $this->assertStringStartsWith("AnonymousClass@tests{$DS}Test{$DS}UtilTest.php#", $actual);
        $this->assertStringEndsWith("::method", $actual);

        $actual = Util::callableToString($object);
        $this->assertStringStartsWith("AnonymousClass@tests{$DS}Test{$DS}UtilTest.php#", $actual);
        $this->assertStringEndsWith("::__invoke", $actual);

        $object = new class implements SelfDescribing {
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

    function test_stringToStructure()
    {
        $this->assertSame($stdclass = new \stdClass(), Util::stringToStructure($stdclass));
        $this->assertSame('', Util::stringToStructure(''));
        $this->assertSame('hoge', Util::stringToStructure('hoge'));
        $this->assertSame(null, Util::stringToStructure('null'));
        $this->assertSame(true, Util::stringToStructure('true'));
        $this->assertSame(false, Util::stringToStructure('false'));
        $this->assertSame(file_get_contents(__FILE__), Util::stringToStructure(__FILE__));
        $this->assertInstanceOf(\SimpleXMLElement::class, Util::stringToStructure(__DIR__ . '/../phpunit.xml.dist'));
        $this->assertInstanceOf(\SimpleXMLElement::class, Util::stringToStructure('<a></a>'));
        $this->assertSame([], Util::stringToStructure('{}'));
        $this->assertSame(['a' => 'A'], Util::stringToStructure('{"a": "A"}'));
    }
}
