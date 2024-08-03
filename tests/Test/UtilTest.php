<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\SelfDescribing;
use PrivateMember;
use ProtectedMember;
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

    function test_reflectProperty()
    {
        $object = new class() extends \ProtectedMember {
            protected $x = 'this is anonymouse';
        };

        $refproperties = Util::reflectProperty($object, 'x');
        $this->assertEquals([get_class($object), ProtectedMember::class, PrivateMember::class], array_keys($refproperties));
        $this->assertEquals([get_class($object), ProtectedMember::class, PrivateMember::class], array_values(array_map(fn($r) => $r->class, $refproperties)));

        try {
            Util::propertyToValue('NeverUndefinedClass', 'undefined');
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString('does not exist', $t->getMessage());
        }
    }

    function test_propertyToValue()
    {
        $object = new #[\AllowDynamicProperties] class('testname') extends \ryunosuke\Test\AbstractTestCase {
            public static $staticProperty  = 'this is static';
            private       $privateProperty = 'this is private';
            public        $publicProperty  = 'this is public';

            public function __get($name)
            {
                assert(strlen($this->privateProperty));
                return "this is magic";
            }
        };
        $object->dynamicProperty = 'this is dynamic';

        $this->assertEquals("this is static", Util::propertyToValue(get_class($object), 'staticProperty'));
        $this->assertEquals("this is static", Util::propertyToValue($object, 'staticProperty'));
        $this->assertEquals("this is private", Util::propertyToValue($object, 'privateProperty'));
        $this->assertEquals("this is public", Util::propertyToValue($object, 'publicProperty'));
        $this->assertEquals("this is magic", Util::propertyToValue($object, 'magicProperty'));
        $this->assertEquals("this is dynamic", Util::propertyToValue($object, 'dynamicProperty'));
        $this->assertEquals("testname", Util::propertyToValue($object, 'name'));


        try {
            Util::propertyToValue(new \stdClass(), 'undefined');
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString('is not defined', $t->getMessage());
        }

        try {
            Util::propertyToValue('NeverUndefinedClass', 'undefined');
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString('does not exist', $t->getMessage());
        }
    }

    function test_methodToCallable()
    {
        $method = Util::methodToCallable($this, 'setUp');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('ryunosuke\\Test\\UtilTest::setUp', $method->toString());

        $class = new class {
            public static function staticMethod() { return __FUNCTION__; }

            /** @noinspection PhpUnusedPrivateMethodInspection */
            private static function privateStaticMethod() { return __FUNCTION__; }

            protected function privateMethod() { return __FUNCTION__; }

            public function publicMethod() { return __FUNCTION__; }

            public function __call($name, $arguments) { return __FUNCTION__; }
        };

        $staticMethod = Util::methodToCallable(get_class($class), 'staticMethod');
        $this->assertEquals('staticMethod', $staticMethod());

        $staticMethod = Util::methodToCallable(get_class($class), 'privateStaticMethod');
        $this->assertEquals('privateStaticMethod', $staticMethod());

        $staticMethod = Util::methodToCallable($class, 'staticMethod');
        $this->assertEquals('staticMethod', $staticMethod());

        $privateMethod = Util::methodToCallable($class, 'privateMethod');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals("AnonymousClass@" . Util::reflectFile(new \ReflectionClass($class)) . "::privateMethod", $privateMethod->toString());
        $this->assertEquals('privateMethod', $privateMethod());

        $publicMethod = Util::methodToCallable($class, 'publicMethod');
        $this->assertEquals('publicMethod', $publicMethod());

        $magicMethod = Util::methodToCallable($class, 'magicMethod');
        $this->assertEquals('__call', $magicMethod());

        $constructor = Util::methodToCallable(\NoneConstructor::class, '__construct');
        $this->assertInstanceOf(\NoneConstructor::class, $constructor());

        $constructor = Util::methodToCallable(\PublicConstructor::class, '__construct');
        $this->assertInstanceOf(\PublicConstructor::class, $constructor(1));

        $constructor = Util::methodToCallable(\PrivateConstructor::class, '__construct');
        $this->assertInstanceOf(\PrivateConstructor::class, $constructor(1));

        try {
            Util::methodToCallable(new \stdClass(), 'undefined');
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString('is not defined', $t->getMessage());
        }

        try {
            Util::methodToCallable('NeverUndefinedClass', 'undefined');
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString('does not exist', $t->getMessage());
        }
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
                return 'CustomString';
            }
        };

        $this->assertEquals('CustomString', Util::callableToString($object));
    }
}
