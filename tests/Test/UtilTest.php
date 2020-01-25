<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\SelfDescribing;
use ryunosuke\PHPUnit\Actual;
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
            private $privateProperty = 'this is private';
            public  $publicProperty  = 'this is public';

            public function __get($name)
            {
                assert(strlen($this->privateProperty));
                return "this is magic";
            }
        };
        /** @noinspection PhpUndefinedFieldInspection */
        $object->dynamicProperty = 'this is dynamic';

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
            protected function privateMethod() { return __FUNCTION__; }

            public function publicMethod() { return __FUNCTION__; }

            public function __call($name, $arguments) { return __FUNCTION__; }
        };
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

    function test_stringMatch()
    {
        $this->assertSame([], Util::stringMatch('HelloWorld', '#unmatch#'));
        $this->assertSame([
            'letter' => 'H',
            0        => 'ello'
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#u'));
        $this->assertSame([
            'letter' => ['H', 0],
            0        => ['ello', 1],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#u', PREG_OFFSET_CAPTURE));
        $this->assertSame([
            'letter' => ['W', 5],
            0        => ['orld', 6],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#u', PREG_OFFSET_CAPTURE, 5));

        $this->assertSame([], Util::stringMatch('HelloWorld', '#unmatch#g', PREG_PATTERN_ORDER));
        $this->assertSame([], Util::stringMatch('HelloWorld', '#unmatch#g', PREG_SET_ORDER));
        $this->assertSame([
            'letter' => ['H', 'W'],
            0        => ['ello', 'orld'],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#ug', PREG_PATTERN_ORDER));
        $this->assertSame([
            [
                'letter' => 'H',
                0        => 'ello',
            ],
            [
                'letter' => 'W',
                0        => 'orld',
            ],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#ug', PREG_SET_ORDER));
        $this->assertSame([
            'letter' => [['H', 0], ['W', 5]],
            0        => [['ello', 1], ['orld', 6]],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#ug', PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE));
        $this->assertSame([
            [
                'letter' => ['H', 0],
                0        => ['ello', 1],
            ],
            [
                'letter' => ['W', 5],
                0        => ['orld', 6],
            ],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#ug', PREG_SET_ORDER | PREG_OFFSET_CAPTURE));
        $this->assertSame([
            [
                'letter' => ['W', 5],
                0        => ['orld', 6],
            ],
        ], Util::stringMatch('HelloWorld', '#(?<letter>[A-Z])([a-z]+)#ug', PREG_SET_ORDER | PREG_OFFSET_CAPTURE, 5));

        $this->assertSame([
            'letter' => ['H', 'T', 'W'],
            0        => ['e', 'e', 'o',],
            'second' => ['l', 's', 'r',],
            1        => ['l', 't', 'l',],
            'rest'   => ['o', 'ing', 'd',],
        ], Util::stringMatch('HelloUnitTestingWorld', '#(?<letter>[A-Z])([a-z])(?<second>[a-z])([a-z])(?<rest>[a-z]+)#ug', PREG_PATTERN_ORDER));
        $this->assertSame([
            [
                'letter' => 'H',
                0        => 'e',
                'second' => 'l',
                1        => 'l',
                'rest'   => 'o',
            ],
            [
                'letter' => 'T',
                0        => 'e',
                'second' => 's',
                1        => 't',
                'rest'   => 'ing',
            ],
            [
                'letter' => 'W',
                0        => 'o',
                'second' => 'r',
                1        => 'l',
                'rest'   => 'd',
            ],
        ], Util::stringMatch('HelloUnitTestingWorld', '#(?<letter>[A-Z])([a-z])(?<second>[a-z])([a-z])(?<rest>[a-z]+)#ug', PREG_SET_ORDER));
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

    function test_isStringy()
    {
        $this->assertTrue(Util::isStringy('hoge'));
        $this->assertTrue(Util::isStringy(new \Exception('hoge')));

        $this->assertFalse(Util::isStringy(new \stdClass()));
        $this->assertFalse(Util::isStringy(STDOUT));
    }

    function test_exportVar()
    {
        $value = [
            'null'   => null,
            'list'   => ['x', 'y', 'z'],
            'arrays' => [['x'], ['y'], ['z']],
            'hash'   => [
                'b' => [
                    'k1' => 'v1',
                    'k2' => 'v2',
                ],
            ],
            'object' => (new Actual('value'))->as('message'),
        ];
        $this->assertEquals(<<<CODE
[
    'null'   => null,
    'list'   => ['x', 'y', 'z'],
    'arrays' => [
        ['x'],
        ['y'],
        ['z'],
    ],
    'hash'   => [
        'b' => [
            'k1' => 'v1',
            'k2' => 'v2',
        ],
    ],
    'object' => ryunosuke\PHPUnit\Actual::__set_state([
        'actual'   => 'value',
        'parent'   => '*RECURSION*',
        'autoback' => false,
        'afters'   => [],
        'message'  => 'message',
    ]),
]

CODE
            , Util::exportVar($value));
    }
}
