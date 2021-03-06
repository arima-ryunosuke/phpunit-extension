# PHPUnit Fluent Assertions

## Description

This package adds phpunit Fluent interface. 

- e.g. `that('xxx')->isEqual('xxx')`
- e.g. `that(1)->isInt()->isBetween(1, 9)`
- e.g. `that('qwe asd zxc')->stringStartsWith('qwe')->stringEndsWith('zxc')`

## Install

```json
{
    "require-dev": {
        "ryunosuke/phpunit-extension": "dev-master"
    }
}
```

## Usage

### Actual class

Simplified chart:

| method             | description                         | return type
| :---               | :---                                | :---
| __call             | call original method no thrown      | actual of method's return or expcetion
| __invoke           | call original __invoke              | actual of __invoke's return
| __get              | get original property               | actual of property
| offsetGet          | get ArrayAccess by key              | actual of key's value
| var                | get property                        | original property
| use                | get original method's callable      | original method
| callable           | get original method's callable      | actual of method's callable
| do                 | call original method                | actual of method's return
| try                | call original method no thrown      | actual of method's return or expcetion
| function           | apply global function               | actual of applied value
| foreach            | apply global function each element  | actual of applied value
| return             | return original                     | original
| eval               | assert constraint directly          | $this
| as                 | set assertion message               | $this
| and                | return latest asserted actual       | actual of latest asserted

```php
// e.g. bootstrap.php
\ryunosuke\PHPUnit\Actual::$compatibleVersion = 2; // see below
function that($actual)
{
    return new \ryunosuke\PHPUnit\Actual($actual);
}

// example TestCase
class ActualTest extends \PHPUnit\Framework\TestCase
{
    function test_fluent()
    {
        # fluent interface
        // means: assertThat(5, logicalAnd(isType('int'), greaterThanOrEqual(1), lessThanOrEqual(9)));
        that(5)->isInt()->isBetween(1, 9);
    }

    function test_prefixEach()
    {
        # "each*" asserts per values (assert AND all values)
        // means: assertThat(1, greaterThan(0)); assertThat(2, greaterThan(0)); assertThat(3, greaterThan(0));
        that([1, 2, 3])->eachGreaterThan(0);
    }

    function test_suffixAnyAll()
    {
        # "*Any" asserts multiple arguments (assert OR all arguments)
        // means: assertThat('hello world', logicalOr(stringContains('hello'), stringContains('noexists')));
        that('hello world')->stringContainsAny(['hello', 'noexists']);
        // ignore case (other arguments are normal)
        that('hello world')->stringContainsAny(['HELLO', 'noexists'], true);

        # "*All" asserts multiple arguments (assert AND all arguments)
        // means: assertThat('hello world', logicalAnd(stringContains('hello'), stringContains('world')));
        that('hello world')->stringContainsAll(['hello', 'world']);
    }

    function test_var_use()
    {
        # "var" returns property of original object (non-public access is possible)
        $object = new \ArrayObject(['x' => 'X', 'y' => 'Y'], \ArrayObject::ARRAY_AS_PROPS);
        $property = that($object)->var('x');
        assertThat($property, equalTo('X'));

        # "use" returns method's closure of original object (non-public access is possible)
        $object = new \ArrayObject(['x' => 'X', 'y' => 'Y'], \ArrayObject::ARRAY_AS_PROPS);
        $method = that($object)->use('getArrayCopy');
        assertThat($method(), equalTo(['x' => 'X', 'y' => 'Y']));
    }

    function test_arrayAccess()
    {
        # array access returns array's value by JMESPath and actual
        $array = ['x' => ['y' => ['z' => [1, 2, 3]]]];
        // means: assertThat($array['x']['y']['z'], equalTo([1, 2, 3]));
        that($array)['x']['y']['z']->isEqual([1, 2, 3]); // simple access
        that($array)['x.y.z']->isEqual([1, 2, 3]);       // JMESPath access

        # if value is string then argument behaves RegularExpression
        # no return 0 (full pattern matches) and unset numeric key of named pattern and sequential array
        $string = 'Hello World';
        that($string)['#(?<first>[A-Z])([a-z]+)#']->is(['first' => 'H', 'ello']);

        # if pattern contains g flag then pattern behaves preg_match_all (like a javascript) 
        $string = 'Hello World';
        that($string)['#(?<first>[A-Z])([a-z]+)#g']->is([
            ['first' => 'H', 'ello'],
            ['first' => 'W', 'orld'],
        ]);

        # if value is SimpleXmlElement or like a XmlString then argument behaves xpath(prefix is "/") or css selector(prefix is not "/")
        $xml = '<a><b><c>C</c></b></a>';
        that($xml)['/a/b/c'][0]->is('C'); // case xpath
        that($xml)['a b c'][0]->is('C');  // case css selector
    }

    function test_propertyAccess()
    {
        # property access returns property and actual (non-public access is possible)
        $object = (object) ['x' => 'X'];
        // means: assertThat($object->x, equalTo('X'));
        that($object)->x->isEqual('X');

        # if prefix is "$" then argument behaves JSONPath
        $object = (object) ['x' => (object) ['y' => (object) ['z' => [1, 2, 3]]]];
        // means: assertThat($object->x->y->z, equalTo([1, 2, 3]));
        that($object)->{'$.x.y.z.*'}->is([1, 2, 3]);
    }

    function test_methodCall()
    {
        # method call returns original result and actual (non-public access is possible)
        $object = new \ArrayObject([1, 2, 3]);
        // means: assertThat($object->getArrayCopy(), equalTo([1, 2, 3]));
        that($object)->getArrayCopy()->isEqual([1, 2, 3]);

        # actual's method prefers to original method
        $object = new \ArrayObject([1, 2, 3]);
        // means: assertThat($object, countOf(3)); not: $object->count();
        that($object)->count(3);

        # "callable" returns original method's callable and actual
        that($object)->callable('count')->isCallable();
        // "callable"'s arguments mean method arguments
        that($object)->callable('setIteratorClass', \stdClass::class)->throws('to be a class name derived from Iterator');

        # "do" invokes original method and actual
        that($object)->do('count')->isEqual(3);

        # "__invoke" returns original::__invoke and actual
        $object = function ($a, $b) { return $a + $b; };
        // means: assertThat($object(1, 2), equalTo(3));
        that($object)(1, 2)->isEqual(3);
    }

    function test_try()
    {
        # "try" is not thrown method call and actual
        $object = new \ReflectionObject((object) ['x' => 'X']);
        // returns original result and actual if not thrown
        that($object)->try('getProperty', 'x')->isInstanceOf(\ReflectionProperty::class);
        // returns thrown exception and actual if thrown
        that($object)->try('getProperty', 'y')->isInstanceOf(\ReflectionException::class);
    }

    function test_function()
    {
        # "function" applys function and actual
        // means: assertThat(strtoupper('hello'), equalTo('HELLO'));
        that('hello')->function('strtoupper')->isEqual('HELLO');
        // if function name suffix is numeric, applys argument the number (zero base)
        // means: assertThat(str_replace('l', 'L', 'hello'), equalTo('heLLo'));
        that('hello')->function('str_replace2', 'l', 'L')->isEqual('heLLo');
    }

    function test_foreach()
    {
        # "foreach" is similar to "function" method. the differences are below:
        // applys each element
        that(['x', 'y', 'z'])->foreach('strtoupper')->isEqual(['X', 'Y', 'Z']);
        // suffix effect is same as "function"
        that(['hoge', 'fuga', 'piyo'])->foreach('str_replace2', ['o', 'g'], ['O', 'G'])->isEqual(['hOGe', 'fuGa', 'piyO']);
        // invokes object's method (if prefix is "::", "->")
        that([new \Exception('foo'), new \Exception('bar')])->foreach('::getMessage')->isEqual(['foo', 'bar']);
    }

    function test_return()
    {
        # "return" returns original value
        $object = new \stdClass();
        assertSame($object, that($object)->return());
    }

    function test_eval()
    {
        # "eval" asserts directly constraint (variadic arguments OR all arguments)
        // means: assertThat('x', equalTo('x'));
        that('x')->eval(equalTo('x'));
        // means: assertThat('x', logicalOr(equalTo('x'), equalTo('y'), equalTo('z')));
        that('x')->eval(equalTo('x'), equalTo('y'), equalTo('z'));
    }

    function test_as()
    {
        # "as" describes failure text
        // means: assertThat('x', equalTo('notX'), 'this is failure message');
        that('x')->as('this is failure message')->isEqual('notX');
    }

    function test_and_exit()
    {
        # "and" returns latest actual
        $object = new \ArrayObject(['x' => 'abcX', 'y' => 'abcY'], \ArrayObject::ARRAY_AS_PROPS);
        // "and" can call as property also as below
        that($object)
            ->x->stringStartsWith('abc')->and->stringLengthEquals(4)->exit()
            ->y->stringStartsWith('abc')->and->stringLengthEquals(4)->exit()
            ->getArrayCopy()->count(2)->and->hasKey('x');

        # but no need to use them as below
        $that = that($object);
        $that->getArrayCopy()->count(2)->hasKey('x')->hasKey('y');
        $that->x->stringStartsWith('abc')->stringLengthEquals(4);
        $that->y->stringStartsWith('abc')->stringLengthEquals(4);
    }
}
```

### Annotester class

Run test by DocComment.

enable use 2 annotations and run codeblock.

- @that: simply call the method/function
- @test: exec code block that dependency context
- ```php ~ ```: exec code block that dependency context

See \ryunosuke\PHPUnit\Annotester and \ryunosuke\Test\AnnotesterTest for details.

```php
/**
 * blockstart
 * that($foo . $bar)->is('foobar');
 * $this->add(1)->is(11);
 * $this->append($foo)->is('10foo');
 * blockend
 */
class Document
{
    private $x;

    public function __construct($x)
    {
        $this->x = $x;
    }

    /**
     * @that(1)->is(11)
     */
    public function add($y)
    {
        return $this->x + $y;
    }

    /**
     * @test {
     *     $that($foo)->is('10foo');
     * }
     */
    public function append($y)
    {
        return $this->x . $y;
    }
}

class DocumentTest extends \PHPUnit\Framework\TestCase
{
    private static $annotester; 

    public static function setUpBeforeClass(): void
    {
        self::$annotester = new \ryunosuke\PHPUnit\Annotester([
            Document::class => [10],
            '$foo'          => 'foo',
            '$bar'          => 'bar',
        ], [
            'doccode' => "#^blockstart(?<phpcode>.*?)^blockend#ums",
        ]);
    }

    function test_all()
    {
        self::$annotester->test(Document::class);
    }
}
```

### Custom constraints

Internals:

| constraint         | description
| :---               | :---
| Contains           | assert string/iterable/file contains substring/element/content
| EqualsFile         | assert string equals file
| EqualsIgnoreWS     | assert string equals ignoring whitespace
| FileContains       | assert file contains string
| FileEquals         | assert file equals string
| FileSizeIs         | assert file size
| HasKey             | assert array/object has key/property
| InTime             | assert processing in time
| IsBetween          | assert range of number
| IsBlank            | assert blank string
| IsCType            | assert value by ctype_xxx
| IsFalsy            | assert value like a false
| IsThrowable        | assert value is Throwable
| IsTruthy           | assert value like a true
| IsValid            | assert value by filter_var
| LengthEquals       | assert string/iterable/file length/count/size
| OutputMatches      | assert output of STDOUT
| StringLengthEquals | assert length of string
| Throws             | assert callable should throw exception

Alias:

`\ryunosuke\PHPUnit\Actual::$constraintVariations` is searching for variation from other constraint.

```php
// Alias. This ables to use: $actual->isSame('other')
\ryunosuke\PHPUnit\Actual::$constraintVariations['isSame'] = IsIdentical::class;
// Construct. This ables to use: $actual->isArray()
\ryunosuke\PHPUnit\Actual::$constraintVariations['isArray'] = [IsType::class => [IsType::TYPE_ARRAY]];
// Mix. This ables to use: $actual->isNullOrString()
\ryunosuke\PHPUnit\Actual::$constraintVariations['isNullOrString'] = [IsNull::class, IsType::class => [IsType::TYPE_STRING]];
// Instance. This ables to use: $actual->lineCount(5)
\ryunosuke\PHPUnit\Actual::$constraintVariations['lineCount'] = new class(/* argument is used as default */0) extends \PHPUnit\Framework\Constraint\Constraint {
    private $lineCount;

    public function __construct(int $lineCount)
    {
        $this->lineCount = $lineCount;
    }

    protected function matches($other): bool
    {
        return $this->lineCount === (preg_match_all("#\\R#", $other) + 1);
    }

    public function toString(): string
    {
        return 'is ' . $this->lineCount . ' lines';
    }
};
// Shorthand instance by closure. This is the same as above
\ryunosuke\PHPUnit\Actual::$constraintVariations['lineCount2'] = function ($other, int $lineCount, string $delimiter = "\\R") {
    return $lineCount === (preg_match_all("#$delimiter#", $other) + 1);
};
```

User defined:

`\ryunosuke\PHPUnit\Actual::$constraintNamespaces` is searching for constraint namespace.

```php
// This ables to use: $actual->yourConstraint()
\ryunosuke\PHPUnit\Actual::$constraintNamespaces['your\\namespace'] = 'your/constraint/directory';
```

### Code completion

Actual class is using `\ryunosuke\PHPUnit\Annotation` trait.
If declare this class in your project space, then custom method and code completion are enabled.

```php
// e.g. bootstrap.php
namespace ryunosuke\PHPUnit {
    /**
     * @method \ryunosuke\PHPUnit\Actual isHoge()
     */
    trait Annotation
    {
        function isFuga(): \ryunosuke\PHPUnit\Actual {
        {
            return $this->eval(new \PHPUnit\Framework\Constraint\IsEqual('fuga'));
        }
    }
}
```

That ables to use `$actual->isH(oge)` completion and `$actual->isF(uga)` method.

Or call `\ryunosuke\PHPUnit\Actual::generateAnnotation`.
This method returns annotation via `$constraintVariations` and `$constraintNamespaces`.

### IDE helper

`bin/phpunit-current` is IDE helper. See [bin/phpunit-current](bin/phpunit-current).

- Run test project context (php verion, phpunit version, configuration etc)
- Run test editing class/method
- Run latest test
- Switching coverage

But this is very legacy. Better to use phpstorm Test Runner.

## Release

Versioning is Semantic Versioning.
BC breaking is controled $compatibleVersion static field somewhat.

- e.g. 1 is compatible 1.0.0
  - e.g. 1.1 is compatible 1.1.0
- e.g. 2 is compatible 2.0.0
- e.g. 999 is latest

### 2.0.0

- [*change] see log

### 1.2.0

- [feature] add Annotester class
- [feature] add shorthand closure alias
- [feature] add int, float ValidType
- [feature] add constraint alias mangle argument
- [feature] add "and" property/method
- [fixbug] supports static property/method
- [fixbug] supports minor/patch version of $compatibleVersion

### 1.1.2

- [feature] add "InTime" constraint
- [feature] add "callable" method
- [change] deprecated "catch" and "print" method

### 1.1.1

- [fixbug] get/offsetGet implementation leak
  - __get: use stringToStructure
  - offsetGet: access to original offset

### 1.1.0

- [feature] add version control property
- [feature] add "prefixIs", "suffixIs" alias
- [feature] support Regex and JSONPath and JMESPath at get/offsetGet
- [feature] implement "__toString" method
- [feature] add depended on other constraint
- [feature] add "FileSizeIs" constraint
- [change] change "Not" position (e.g. NotFileExists -> FileNotExists)
  - "notFileExists" can still be used, but will be deleted in the future
- [change] rename "all*" -> "each*"
  - "all*" can still be used, but will be deleted in the future
- [fixbug] normalize directory separator

### 1.0.0

- release 1.0.0
- [change] drastic change
- [feature] add "function" method
- [feature] add "foreach" method
- [feature] support "Throws" multiple arguments

### 0.2.0

- [feature] add "var" method
- [feature] add "use" method
- [feature] add "print" method
- [feature] add "return" method
- [feature] add "OutputMatches" constraint
- [change] delete "autoback" method
- [change] rename class/method

### 0.1.0

- [feature] add "*All" method
- [feature] add "try" method
- [feature] add "message" method
- [feature] add "__invoke" method
- [feature] add "file*" constraint
- [feature] replace with original "logical*" constraint
- [feature] variation adds "is" alias
- [feature] variation supports anonymouse class
- [fixbug] variation ignores arguments
- [change] __get/__call can access no-public member

### 0.0.0

- publish

## License

MIT
