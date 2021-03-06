<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\Constraint\IsEqual;
use ryunosuke\PHPUnit\Actual;

class ActualTest extends \ryunosuke\Test\AbstractTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Actual::$constraintVariations['isFoo'] = new IsEqual('foo', 9.9, 99, true);
        Actual::$constraintVariations['isBar'] = function ($other, $expected = '') { return $other == $expected; };
    }

    function actual($actual)
    {
        return new Actual($actual);
    }

    function test_generateAnnotation()
    {
        $annotations = Actual::generateAnnotation();
        $this->assertIsString($annotations);
        $this->assertStringContainsString('@method', $annotations);
        $this->assertStringContainsString('@see', $annotations);
        $this->assertStringContainsString('isFoo($value = \'foo\'', $annotations);
        $this->assertStringContainsString('isFooAny(array $values', $annotations);
        $this->assertStringContainsString('isBar($expected = \'\')', $annotations);
        $this->assertStringContainsString('isFalse', $annotations);
        $this->assertStringContainsString('isNotFalse', $annotations);
        $this->assertStringContainsString('stringLengthEquals', $annotations);
        $this->assertStringContainsString('stringLengthNotEquals', $annotations);
        $this->assertStringContainsString(' each', $annotations);
        $this->assertStringContainsString('Any(', $annotations);
        $this->assertStringContainsString('All(', $annotations);
        $this->assertStringContainsString('isNullOrString()', $annotations);
        $this->assertStringContainsString('gte($value)', $annotations);
        $this->assertStringContainsString('equalsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)', $annotations);
    }

    function test_ok()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->actual('foo')->isFoo()->isBar('foo');

        $this->actual('qweN N Nzxc')
            ->isNullOrString()
            ->matches('#^[a-z\s]+$#i')
            ->stringContains(' N ')
            ->stringStartsWith('qwe')
            ->stringEndsWith('zxc')
            ->prefixIsNot('zxc')
            ->equalsIgnoreWS('
qweN
N
Nzxc ');

        $this->actual(2)
            ->isInt()
            ->isEqual(2)
            ->gt(1)
            ->gte(2)
            ->lt(3)
            ->lte(2)
            ->isBetween(1, 3);

        $ex = new \Exception();
        $this->actual($ex)
            ->isSame($ex)
            ->isNotSame(new \Exception())
            ->isInstanceOf(\Exception::class)
            ->isInstanceOf(\Throwable::class)
            ->isNotInstanceOf(\ArrayObject::class);

        $this->actual(function () { throw new \Exception('hogera', 22); })->throws('hogera');
        $this->actual(function () { throw new \Exception('hogera', 22); })->throws(new \Exception('hogera', 22));

        $this->actual('qwe')
            ->stringLengthEqualsAny([1, 2, 3])
            ->isEqualAny(['qwe', 'asd'])
            ->isEqualAny(['QWE', 'asd'], 0, 10, false, true);

        $this->actual(['a' => 'A', 'b' => 'B', 'c' => 'C'])
            ->arrayHasKeyAll(['a', 'b', 'c']);

        $this->actual([true, 1, '1', new \stdClass()])->eachIsTruthy();
        $this->actual(['1', '2', '3'])->eachIsString();
    }

    function test_ng()
    {
        $this->ng(function () {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual('foo')->isFoo('xxx');
        }, 'two strings are equal');

        $this->ng(function () {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual('foo')->isBar('xxx');
        }, '{ return $other == $expected; }');

        $this->ng(function () {
            $this->actual('qweN N Nzxc')->stringStartsWith('hoge');
        }, 'starts with "hoge".');

        $this->ng(function () {
            $this->actual(9)->lessThan(5);
        }, '9 is less than 5.');

        $this->ng(function () {
            $this->actual(new \Exception())->isInstanceOf(\ArrayObject::class);
        }, 'is an instance of class "ArrayObject"');

        $this->ng(function () {
            $this->actual(function () { })->throws('dummy');
        }, "'dummy', 0");

        $this->ng(function () {
            $this->actual(function () { throw new \Exception('hogera', 22); })->throws('dummy');
        }, "'dummy', 0");

        $this->ng(function () {
            $this->actual(function () { throw new \Exception('hogera', 22); })->throws(new \Exception('hogera', 99));
        }, "'hogera', 99");

        $this->ng(function () {
            $this->actual('qwe')->isEqualAny(['QWE', 'asd', 'zxc']);
        }, "'qwe' is equal to 'QWE' or is equal to 'asd' or is equal to 'zxc'");

        $this->ng(function () {
            $this->actual(['a' => 'A', 'b' => 'B', 'c' => 'C'])->arrayHasKeyAll(['a', 'b', 'c', 'x']);
        }, "has the key 'a' and has the key 'b' and has the key 'c' and has the key 'x'");

        $this->ng(function () {
            $this->actual([true, 1, 0, new \stdClass()])->eachIsTruthy();
        }, "0 is truthy");

        $this->ng(function () {
            $this->actual(['1', 2, '3'])->eachIsString();
        }, '2 is of type "string"');
    }

    function test_callStatic()
    {
        $actual = $this->actual(new class {
            static function staticMethod()
            {
                return func_get_args();
            }
        });
        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual::staticMethod(1, 2, 3)->is([1, 2, 3]);
            ("$actual")::staticMethod(1, 2, 3)->is([1, 2, 3]);
        }
    }

    function test_toString()
    {
        $this->assertEquals("null\n", (string) $this->actual(null));
        $this->assertEquals("[1, 2, 3]\n", (string) $this->actual([1, 2, 3]));
    }

    function test_accessor()
    {
        $actual = $this->actual(new class('testname') extends \ryunosuke\Test\AbstractTestCase {
            private $privateProperty = 'this is private';

            public function __get($name)
            {
                assert(strlen($this->privateProperty));
                return "$name is __get property";
            }
        });
        $actual->privateProperty->isEqual('this is private');
        $actual->getter->isEqual('getter is __get property');
        $actual->name->isEqual('testname');

        $actual = $this->actual((object) [
            'x' => 'X',
        ]);
        $actual->x->isEqual('X');

        $actual = $this->actual(new \ArrayObject([
            'x' => 'X',
            'y' => 'Y',
            'z' => [
                ['k' => 'v1'],
                ['k' => 'v2'],
                ['k' => 'v3'],
            ],
        ], \ArrayObject::ARRAY_AS_PROPS));

        $actual['x']->isEqual('X');
        $actual->y->isEqual('Y');

        $actual->{"$.z.*.k"}->is(['v1', 'v2', 'v3']);
        $actual['z[].k']->is(['v1', 'v2', 'v3']);

        $actual['z[0].k']->is('v1');
        $actual->z[0]->k->is('v1');

        $this->ng(function () use ($actual) {
            $actual->undefined->isInt();
        }, '$undefined is not defined');

        $this->ng(function () {
            $this->actual(123)['aaaa'];
        }, 'must be structure value');
    }

    function test_accessor_xml()
    {
        $actual = $this->actual('<a id="foo">A<b id="bar">B<c attr="ATTR1">C1</c><c attr="ATTR2">C2</c></b></a>');

        $actual['id']->is('foo');
        $actual->b['id']->is('bar');
        $actual->b->c[0]['attr']->is('ATTR1');

        $actual['/a/b/c']->count(2)[1]->isEqual('C2');
        $actual['/a/b/c/@attr']->count(2)[1]->isEqual('ATTR2');

        $actual['#foo']->count(1)[0]->isEqual('A');
        $actual['c[attr]']->count(2)[0]->isEqual('C1');
    }

    function test_accessor_string()
    {
        $actual = $this->actual('hoge fuga piyo');
        $actual['#(hoge).*(fuga)#']->count(2)->isEqual(['hoge', 'fuga']);
        $actual['#(?<aaa>fuga)#']->count(1)->isEqual(['aaa' => 'fuga']);
        $actual['#(?<aaa>hoge) (?<bbb>.*)#']->count(2)->isEqual(['aaa' => 'hoge', 'bbb' => 'fuga piyo']);

        $actual = $this->actual('hoge fuga piyo hoge fuga piyo');
        $actual['#(hoge).*(fuga)#g']->count(1)[0]->isEqual(['hoge', 'fuga']);
        $actual['#(?<aaa>fuga)#g']->count(2)[0]->isEqual(['aaa' => 'fuga']);
        $actual['#(?<aaa>hoge) (?<bbb>.*)#g']->count(1)[0]->isEqual(['aaa' => 'hoge', 'bbb' => 'fuga piyo hoge fuga piyo']);
    }

    function test_mutator()
    {
        $actual = $this->actual(new \ArrayObject(['a' => null, 'x' => null], \ArrayObject::ARRAY_AS_PROPS));

        $actual['a']->isNull();
        $actual->x->isNull();

        $actual['a'] = 'a';
        $actual->x = 'x';

        $actual->hasKeyAll(['a', 'x']);
        $actual['a']->is('a');
        $actual->x->is('x');

        unset($actual['a']);
        unset($actual->x);

        $actual->notHasKeyAll(['a', 'x']);
    }

    function test_var()
    {
        $object = new class('testname') extends \ryunosuke\Test\AbstractTestCase {
            public static $staticProperty  = 'this is static';
            private       $privateProperty = 'this is private';
            public        $publicProperty  = 'this is public';

            public function __get($name)
            {
                assert(strlen($this->privateProperty));
                return "$name is __get property";
            }
        };
        $object->dynamicProperty = 'this is dynamic';
        $actual = $this->actual($object);

        $this->assertEquals('this is static', $this->actual(get_class($object))->var('staticProperty'));
        $this->assertEquals('this is static', $actual->var('staticProperty'));
        $this->assertEquals('this is private', $actual->var('privateProperty'));
        $this->assertEquals('this is public', $actual->var('publicProperty'));
        $this->assertEquals('getter is __get property', $actual->var('getter'));
        $this->assertEquals('testname', $actual->var('name'));

        $actual = $this->actual(new \stdClass());
        $this->ng(function () use ($actual) {
            $actual->var('undefinedProperty');
        }, '::$undefinedProperty is not defined');
    }

    function test_use()
    {
        $actual = $this->actual(new class {
            /** @noinspection PhpUnusedPrivateMethodInspection */
            private function privateMethod($x)
            {
                return $x * 10;
            }

            public function publicMethod($x)
            {
                return $x * 20;
            }
        });

        $this->assertEquals(10, $actual->use('privateMethod')(1));
        $this->assertEquals(20, $actual->use('publicMethod')(1));

        $actual = $this->actual(new \stdClass());
        $this->ng(function () use ($actual) {
            $actual->use('undefinedMethod');
        }, '::undefinedMethod() is not defined');
    }

    function test_callable()
    {
        $actual = $this->actual(new class {
            private function privateMethod($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                echo $x * 10;
            }

            public function publicMethod($x)
            {
                echo $this->privateMethod($x) * 2;
            }
        });

        $actual->callable('privateMethod')->isCallable();
        $actual->callable('publicMethod')->isCallable();

        $actual->callable('privateMethod', null)->throws('this is message');
        $actual->callable('publicMethod', null)->throws('this is message');

        $actual->callable('privateMethod', 1)->outputMatches('#10#');
        $actual->callable('publicMethod', 2)->outputMatches('#20#');
    }

    function test_invoke()
    {
        $thrower = new class() {
            function __invoke($x) { return $x; }
        };

        $actual = $this->actual($thrower);
        $actual(1)->is(1);

        $thrower = function ($x) {
            return $x;
        };

        $actual = $this->actual($thrower);
        $actual(1)->is(1);
    }

    function test_call()
    {
        $object = new class() {
            public function method($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                return $x * 10;
            }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $this->actual($object)->method(2)->is(20);
            $this->actual($object)->method(null)->getMessage()->is('this is message.');
            $this->actual($object)->method(null)->wasThrown('this is message');
        }

        $array = [1, 2, 3];
        $this->actual($array)->maximum()->is(3);
        $this->actual($array)->implode1(',')->is('1,2,3');
    }

    function test_do()
    {
        $object = new class {
            public static function staticMethod($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                return $x * 10;
            }

            private function privateMethod($x)
            {
                return $this->staticMethod($x);
            }

            public function publicMethod($x)
            {
                return $this->privateMethod($x);
            }

            public function __invoke($x)
            {
                return $this->privateMethod($x);
            }

            public function __call($name, $arguments)
            {
                return $name . $arguments[0] * 10;
            }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual = $this->actual(get_class($object));

            $actual->staticMethod(3)->isEqual(30);
            $actual->do('staticMethod', 5)->isEqual(50);

            $actual = $this->actual($object);

            $actual->staticMethod(3)->isEqual(30);
            $actual->do('staticMethod', 5)->isEqual(50);
            $actual->isType('object');

            $actual->privateMethod(3)->isEqual(30);
            $actual->do('privateMethod', 5)->isEqual(50);
            $actual->isType('object');

            $actual->publicMethod(3)->isEqual(30);
            $actual->do('publicMethod', 5)->isEqual(50);
            $actual->isType('object');

            $actual->hoge(3)->isEqual('hoge30');
            $actual->fuga(4)->isEqual('fuga40');
        }

        $callee = function ($x) {
            return $x * 10;
        };

        $this->actual($callee)->do(2)->is(20);
    }

    function test_try()
    {
        $thrower = new class() {
            function divide($x, $n) { return $x / $n; }

            function __invoke($x, $n) { return $x / $n; }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $this->actual($thrower)->try('divide', 10, 2)->is(5);
            $this->actual($thrower)->try('divide', 10, 0)->isInstanceOf(\Exception::class)->getMessage()->is('Division by zero');
            $this->actual($thrower)->try(null, 10, 2)->is(5);
            $this->actual($thrower)->try(null, 10, 0)->getMessage()->is('Division by zero');
            $this->actual($thrower)->try(null, 10, 0)->wasThrown('Division by zero');
        }

        $thrower = function ($x, $n) {
            return $x / $n;
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $this->actual($thrower)->try(10, 2)->is(5);
            $this->actual($thrower)->try(10, 0)->isInstanceOf(\Exception::class)->getMessage()->is('Division by zero');
            $this->actual($thrower)->try(10, 2)->is(5);
            $this->actual($thrower)->try(10, 0)->getMessage()->is('Division by zero');
            $this->actual($thrower)->try(10, 0)->wasThrown('Division by zero');
        }
    }

    function test_list()
    {
        $actual = $this->actual(function ($x, &$y, $z) {
            $x = 1;
            $y = 2;
            $z = 3;
            return $x + $y + $z;
        });
        $actual(0, 0, 0)->list(0)->is(0)->exit()->list(1)->is(2)->exit()->list(2)->is(0);
        $actual(0, 0, 0)->list()->is([0, 2, 0]);
    }

    function test_function()
    {
        $this->actual(' qwe ')->function('trim')->isEqual('qwe');
        $this->actual('XqweX')->function('trim', 'X')->isEqual('qwe');
        $this->actual('XqweX')->function('str_replace2', 'X', 'Z')->isEqual('ZqweZ');

        $this->ng(function () {
            $this->actual('qwe')->function('undefined');
        }, "undefined is not defined");
    }

    function test_foreach()
    {
        $this->actual(['a', 'b', 'c'])->foreach('strtoupper')->isEqual(['A', 'B', 'C']);
        $this->actual(['XaX', 'XbX', 'XcX'])->foreach('trim', 'X')->isEqual(['a', 'b', 'c']);
    }

    function test_function_foreach()
    {
        $user = new class() {
            public $code, $name;

            function new($code, $name)
            {
                $that = new self();
                $that->code = $code;
                $that->name = $name;
                return $that;
            }

            private function privateCodeName()
            {
                return "$this->code:$this->name";
            }

            public function publicCodeName()
            {
                return $this->privateCodeName();
            }
        };

        $users = [
            1 => $user->new(1, 'hoge'),
            2 => $user->new(2, 'fuga'),
            3 => $user->new(3, 'piyo'),
        ];

        $this->actual($users)
            ->function('array_column', 'code')->isEqual([1, 2, 3])->exit()
            ->function('array_column', 'name')->foreach('strtoupper')->isEqual(['HOGE', 'FUGA', 'PIYO'])->exit()->exit()
            ->foreach('->privateCodeName')->isEqual([1 => '1:hoge', 2 => '2:fuga', 3 => '3:piyo'])->exit()
            ->foreach('::publicCodeName')->isEqual([1 => '1:hoge', 2 => '2:fuga', 3 => '3:piyo']);
    }

    function test_return()
    {
        $object = new \stdClass();
        $object->member = new \stdClass();
        $actual = $this->actual($object);

        $this->assertSame($actual->return(), $object);
        $this->assertSame($actual->member->return(), $object->member);
    }

    function test_eval()
    {
        $this->actual('qwe')->eval(new IsEqual('qwe'));
        $this->actual('qwe')->eval(new IsEqual('asd'), new IsEqual('qwe'));

        $this->ng(function () {
            $this->actual('qwe')->eval(new IsEqual('asd'), new IsEqual('zxc'));
        }, "'qwe' is equal to 'asd' or is equal to 'zxc'");
    }

    function test_as()
    {
        $this->ng(function () {
            $this->actual(1)->as('this is fail message')->isFalse();
        }, "this is fail message");
    }

    function test_and_exit()
    {
        $object = new \ArrayObject([
            'x' => 'X',
            'y' => 'Y',
            'z' => new \ArrayObject([
                'a' => 'A',
                'b' => 'B',
            ], \ArrayObject::ARRAY_AS_PROPS),
        ], \ArrayObject::ARRAY_AS_PROPS);

        // use exit
        $actual = $this->actual($object);
        $actual['z']->count(2)
            ->a->isEqual('A')->exit()
            ->b->isEqual('B')->exit(2)
            ->x->isEqual('X')->exit()
            ->y->isEqual('Y')->exit(99)
            ->do('count')->isEqual(3)->exit()
            ->isInstanceOf(\ArrayObject::class);

        // use autoexit and
        $actual = $this->actual($object);
        $actual['z']->count(2)->and->isInstanceOf(\ArrayObject::class)
            ->a->isEqual('A')->and->lengthEquals(1)->exit()
            ->b->isEqual('B')->and->lengthEquals(1)->exit()->exit()
            ->x->isEqual('X')->and->lengthEquals(1)->exit()
            ->y->isEqual('Y')->and->lengthEquals(1)->exit()->exit()
            ->do('count')->isEqual(3)->exit()
            ->isInstanceOf(\ArrayObject::class);

        // standard
        $actual = $this->actual($object);
        $actual->isInstanceOf(\ArrayObject::class)->count(3);
        $actual->x->is('X');
        $actual->y->is('Y');
        $z = $actual->z->isInstanceOf(\ArrayObject::class)->count(2);
        $z->a->is('A');
        $z->b->is('B');
    }

    function test_variation()
    {
        $this->actual('hoge')->isHoge();

        $this->actual('hoge')->is('hoge');
        $this->actual('hoge')->isSame('hoge');
        $this->actual('abcxyz')->prefixIs('abc');
        $this->actual('abcxyz')->suffixIs('xyz');
        $this->actual(['a', 'b'])->equalsCanonicalizing(['b', 'a']);
        $this->actual('hoge')->equalsIgnoreCase('HOGE');
        $this->actual('hoge')->matches('#hoge#');
        $this->actual(5)->gt(4)->gte(5);
        $this->actual(5)->lt(6)->lte(5);
        $this->actual(null)->isNullOrString();

        $this->actual([12])->isArray();
        $this->actual(true)->isBool();
        $this->actual(1.23)->isFloat();
        $this->actual(1234)->isInt();
        $this->actual(null)->isNull();
        $this->actual("12")->isNumeric();
        $this->actual($this)->isObject();
        $this->actual(STDIN)->isResource();
        $this->actual("str")->isString();
        $this->actual(12345)->isScalar();
        $this->actual(function () { })->isCallable();
        $this->actual(new \ArrayObject())->isIterable();

        $this->actual("Az")->isCtypeAlnum();
        $this->actual("Az")->isCtypeAlpha();
        $this->actual("\n")->isCtypeCntrl();
        $this->actual("12")->isCtypeDigit();
        $this->actual("Az")->isCtypeGraph();
        $this->actual("az")->isCtypeLower();
        $this->actual("az")->isCtypePrint();
        $this->actual("()")->isCtypePunct();
        $this->actual("  ")->isCtypeSpace();
        $this->actual("AZ")->isCtypeUpper();
        $this->actual("Ff")->isCtypeXdigit();

        $this->actual("123")->isValidInt();
        $this->actual("1.23")->isValidFloat();
        $this->actual("hoge@example.com")->isValidEmail();
        $this->actual("127.0.0.1")->isValidIp();
        $this->actual("127.0.0.1")->isValidIpv4();
        $this->actual("::1")->isValidIpv6();
        $this->actual("00-00-5E-00-53-00")->isValidMac();
        $this->actual("http://example.com")->isValidUrl();

        $this->actual("1\n2\n3\n")->lineCount(4);
        $this->actual("1\r2\r3\r4\r")->lineCount(1, "\n");
        $this->actual("1\r2\r3\r4\r")->lineCount(5, "\r");
    }
}
