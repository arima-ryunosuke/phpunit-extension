<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\Constraint\IsEqual;
use ryunosuke\PHPUnit\Actual;

class ActualTest extends \ryunosuke\Test\AbstractTestCase
{
    function actual($actual, bool $autoback = false)
    {
        return new Actual($actual, $autoback);
    }

    function test_generateAnnotation()
    {
        $annotations = Actual::generateAnnotation(false);
        $this->assertIsString($annotations);
        $this->assertStringContainsString('@method', $annotations);
        $this->assertStringContainsString('@see', $annotations);
        $this->assertStringContainsString('isFalse', $annotations);
        $this->assertStringContainsString('isNotFalse', $annotations);
        $this->assertStringContainsString('stringLengthEquals', $annotations);
        $this->assertStringContainsString('notStringLengthEquals', $annotations);
        $this->assertStringContainsString(' all', $annotations);
        $this->assertStringContainsString('Any(', $annotations);
        $this->assertStringContainsString('All(', $annotations);
        $this->assertStringContainsString('isNullOrString()', $annotations);
        $this->assertStringContainsString('gte($value)', $annotations);
        $this->assertStringContainsString('equalsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)', $annotations);

        $this->assertIsArray(Actual::generateAnnotation(true));
    }

    function test_ok()
    {
        $this->actual('qweN N Nzxc')
            ->isNullOrString()
            ->matches('#^[a-z\s]+$#i')
            ->stringContains(' N ')
            ->stringStartsWith('qwe')
            ->stringEndsWith('zxc')
            ->notStringStartsWith('zxc')
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

        $this->actual([true, 1, '1', new \stdClass()])->allIsTruthy();
        $this->actual(['1', '2', '3'])->allIsString();
    }

    function test_ng()
    {
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
            $this->actual([true, 1, 0, new \stdClass()])->allIsTruthy();
        }, "0 is truthy");

        $this->ng(function () {
            $this->actual(['1', 2, '3'])->allIsString();
        }, '2 is of type "string"');
    }

    function test_accessor()
    {
        $actual = $this->actual(new class('testname') extends \ryunosuke\Test\AbstractTestCase
        {
            private /** @noinspection PhpUnusedPrivateFieldInspection */ $privateProperty = 'this is private';

            public function __get($name)
            {
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
        ], \ArrayObject::ARRAY_AS_PROPS));

        $actual['x']->isEqual('X');
        $actual->y->isEqual('Y');

        $this->ng(function () use ($actual) {
            $actual['undefined']->isInt();
        }, "has the key 'undefined'");

        $this->ng(function () use ($actual) {
            $actual->undefined->isInt();
        }, 'has attribute "undefined"');
    }

    function test_do()
    {
        $actual = $this->actual(new class
        {
            /** @noinspection PhpUnusedPrivateMethodInspection */
            private function privateMethod($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                return $x * 10;
            }

            public function publicMethod($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                return $x * 10;
            }
        });

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual->privateMethod(3)->isEqual(30);
            $actual->do('privateMethod', 5)->isEqual(50);
            $actual->catch(new \Exception('this is message.', 123))->privateMethod(null);
            $actual->catch(new \Exception('this is message.', 123))->do('privateMethod', null);
            $actual->isType('object');

            $actual->publicMethod(3)->isEqual(30);
            $actual->do('publicMethod', 5)->isEqual(50);
            $actual->catch(new \Exception('this is message.', 123))->publicMethod(null);
            $actual->catch(new \Exception('this is message.', 123))->do('publicMethod', null);
            $actual->isType('object');
        }

        $actual = $this->actual(
        /**
         * @method hoge()
         * @method int fuga()
         */
            new class()
            {
                public function __call($name, $arguments)
                {
                    return $name . $arguments[0] * 10;
                }
            });
        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual->hoge(3)->isEqual('hoge30');
            $actual->fuga(4)->isEqual('fuga40');
            $this->ng(function () use ($actual) {
                /** @noinspection PhpUndefinedMethodInspection */
                $actual->piyo();
            }, 'piyo');
        }

        $actual = $this->actual(new \stdClass());
        $this->ng(function () use ($actual) {
            /** @noinspection PhpUndefinedMethodInspection */
            $actual->undefinedMethod();
        }, 'undefinedMethod');

        $this->ng(function () {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual(null)->undefinedMethod();
        }, "undefinedMethod");
    }

    function test_invoke()
    {
        $actual = $this->actual(new class
        {
            public function __invoke($a, $b, $c)
            {
                return "$a,$b,$c";
            }
        });
        $actual(1, 2, 3)->isEqual('1,2,3');

        $actual = $this->actual(function ($a, $b, $c) {
            return "$a,$b,$c";
        });
        $actual(1, 2, 3)->isEqual('1,2,3');

        $actual = $this->actual(function () {
            throw new \Exception('message');
        });
        $actual->catch('message')();
        $this->ng(function () use ($actual) {
            $actual->catch('notmessage')();
        }, "should throw \Exception('notmessage', 0)");
    }

    function test_exit()
    {
        $actual = $this->actual(new \ArrayObject([
            'x' => 'X',
            'y' => 'Y',
            'z' => new \ArrayObject([
                'a' => 'A',
                'b' => 'B',
            ], \ArrayObject::ARRAY_AS_PROPS),
        ], \ArrayObject::ARRAY_AS_PROPS));

        $actual['z']->count(2)
            ->a->isEqual('A')->exit()
            ->b->isEqual('B')->exit(2)
            ->x->isEqual('X')->exit()
            ->y->isEqual('Y')->exit(99)
            ->do('count')->isEqual(3)->exit()
            ->isInstanceOf(\ArrayObject::class);
    }

    function test_autoback()
    {
        $actual = $this->actual(new class
        {
            function getA() { return 'A'; }

            function getB() { return 'B'; }

            function getC() { return 'C'; }
        }, true);

        /** @noinspection PhpUndefinedMethodInspection */
        $actual
            ->getA()->isEqual('A')
            ->getB()->isEqual('B')
            ->getC()->isEqual('C');
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

    function test_function()
    {
        $this->actual(' qwe ')->function('trim')->isEqual('qwe');
        $this->actual('XqweX')->function('trim', 'X')->isEqual('qwe');
        $this->actual('XqweX')->function('str_replace2', 'X', 'Z')->isEqual('ZqweZ');

        $this->ng(function () {
            $this->actual('qwe')->function('undefined');
        }, "undefined is not callable");
    }

    function test_foreach()
    {
        $this->actual(['a', 'b', 'c'])->foreach('strtoupper')->isEqual(['A', 'B', 'C']);
        $this->actual(['XaX', 'XbX', 'XcX'])->foreach('trim', 'X')->isEqual(['a', 'b', 'c']);
    }

    function test_function_foreach()
    {
        $user = new class()
        {
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

        $this->actual($users, true)
            ->function('array_column', 'code')->isEqual([1, 2, 3])
            ->function('array_column', 'name')->foreach('strtoupper')->isEqual(['HOGE', 'FUGA', 'PIYO'])->exit()
            ->foreach(function ($user) { return $user->code; })->isEqual([1 => 1, 2 => 2, 3 => 3])
            ->foreach('->privateCodeName')->isEqual([1 => '1:hoge', 2 => '2:fuga', 3 => '3:piyo'])
            ->foreach('::publicCodeName')->isEqual([1 => '1:hoge', 2 => '2:fuga', 3 => '3:piyo']);
    }

    function test_var()
    {
        $object = new class('testname') extends \ryunosuke\Test\AbstractTestCase
        {
            private /** @noinspection PhpUnusedPrivateFieldInspection */ $privateProperty = 'this is private';
            public                                                       $publicProperty  = 'this is public';

            public function __get($name)
            {
                return "$name is __get property";
            }
        };
        /** @noinspection PhpUndefinedFieldInspection */
        $object->dynamicProperty = 'this is dynamic';
        $actual = $this->actual($object);

        $this->assertEquals('this is private', $actual->var('privateProperty'));
        $this->assertEquals('this is public', $actual->var('publicProperty'));
        $this->assertEquals('getter is __get property', $actual->var('getter'));
        $this->assertEquals('testname', $actual->var('name'));
    }

    function test_use()
    {
        $actual = $this->actual(new class
        {
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
    }

    function test_try()
    {
        $thrower = new class()
        {
            function divide($x, $n) { return $x / $n; }

            function __invoke($x, $n) { return $x / $n; }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $this->actual($thrower)->try('divide', 10, 2)->is(5);
            $this->actual($thrower)->try('divide', 10, 0)->isInstanceOf(\Exception::class)->getMessage()->is('Division by zero');
            $this->actual($thrower)->try(null, 10, 2)->is(5);
            $this->actual($thrower)->try(null, 10, 0)->getMessage()->is('Division by zero');
        }
    }

    function test_catch()
    {
        $thrower = new class()
        {
            function throw() { throw new \Exception('actual message', 123); }

            function nothrow() { }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $this->actual($thrower)->catch('actual message')->throw();
            $this->actual($thrower)->catch('actual message', 1)->throw();
            $this->actual($thrower)->catch(123, 'invalid')->throw();
            $this->actual($thrower)->catch(\RuntimeException::class, \DomainException::class, \Exception::class)->throw();
        }
        $this->ng(function () use ($thrower) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual($thrower)->catch('actual message')->nothrow();
        }, "should throw");
    }

    function test_print()
    {
        $printer = new class()
        {
            function echo() { echo 'hello world'; }

            function __invoke() { echo 'hello world'; }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        $this->actual($printer)->print('#hello#')->echo();
        $this->actual($printer)->outputMatches('#hello#');
    }

    function test_return()
    {
        $object = new \stdClass();
        $object->child = new \stdClass();
        $actual = $this->actual($object);

        $this->assertSame($actual->return(), $object);
        $this->assertSame($actual->child->return(), $object->child);
    }

    function test_variation()
    {
        $this->actual('hoge')->isHoge();

        $this->actual('hoge')->is('hoge');
        $this->actual('hoge')->isSame('hoge');
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
