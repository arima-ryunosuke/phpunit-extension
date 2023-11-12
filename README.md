# PHPUnit Extension

## Description

This package adds Fluent interface. and provides Custom assertion.

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
| __call(...[])      | get original method's callable      | actual of method's callable (with bindings)
| __invoke           | call original __invoke no thrown    | actual of __invoke's return or expcetion
| __invoke(...[])    | get original __invoke's callable    | actual of __invoke's callable (with bindings)
| __get              | get original property no thrown     | actual of property or expcetion
| __set              | set original property publicly      | void
| offsetGet          | get ArrayAccess by key              | actual of key's value
| var                | get property                        | original property
| use                | get original method's callable      | original method as Closure
| callable           | get original method's callable      | actual of method's callable
| fn                 | get original invoke's callable      | actual of invoke's callable
| do                 | call original method                | actual of method's return
| try                | call original method no thrown      | actual of method's return or expcetion
| new                | call class construct no thrown      | actual of object or expcetion
| insteadof          | apply callable                      | actual of applied value
| function           | apply global function               | actual of applied value
| foreach            | apply global function each element  | actual of applied value
| list               | return reference argument           | actual of reference argument
| return             | return original                     | original
| echo               | dump original                       | $this
| eval               | assert constraint directly          | $this
| as                 | set assertion message               | $this
| break              | mark breakable test                 | $this
| and                | return latest asserted actual       | actual of latest asserted
| final              | return assertion statistic          | actual of assertion statistic
| declare            | rewrite source by actual value      | $this
| wasOutputed        | assert stdout or echo               | $this
| wasErrored         | assert stderr or throw              | $this
| inElapsedTime      | assert elapsed time                 | $this
 
```PHP
# e.g. bootstrap.php

/**
 * @template T
 * @param T $actual
 * @return \ryunosuke\PHPUnit\Actual|T
 */
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
        # array access returns array's value and actual
        $array = ['x' => ['y' => ['z' => [1, 2, 3]]]];
        // means: assertThat($array['x']['y']['z'], equalTo([1, 2, 3]));
        that($array)['x']['y']['z']->isEqual([1, 2, 3]);
    }

    function test_propertyAccess()
    {
        # property access returns property and actual (non-public access is possible)
        $object = (object) ['x' => 'X'];
        // means: assertThat($object->x, equalTo('X'));
        that($object)->x->isEqual('X');
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
        that($object)->callable('setIteratorClass', \stdClass::class)->throws('to be a class name derived from ArrayIterator');

        # "do" invokes original method and actual
        that($object)->do('count')->isEqual(3);

        # "__invoke" returns original::__invoke and actual
        $object = function ($a, $b) { return $a + $b; };
        // means: assertThat($object(1, 2), equalTo(3));
        that($object)(1, 2)->isEqual(3);
    }

    function test_methodCallWithBinding()
    {
        # method call by (...[]) returns method's callable of original object with binding (non-public access is possible)
        $closure = function ($arg) { echo $arg; };
        that($closure)->callable('__invoke', 'hoge')->outputEquals('hoge');
        that($closure)(...['hoge'])->outputEquals('hoge');
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

    function test_list()
    {
        # "list" returns reference argument and actual
        // means: (fn (&$ref) => $ref = 123)($dummy); assertThat($dummy, equalTo(123));
        $dummy = null;
        that(fn (&$ref) => $ref = 123)($dummy)->list(0)->isEqual(123);
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

    function test_break()
    {
        # "break" mark breakable test (converting Failure to Warning)
        that('x')->break()->isEqual('notX');
        // ...continued this case
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

    function test_declare()
    {
        # declare is replaced below at runtime
        // that(['x', 'y', 'z'])->declare();
        that(['x', 'y', 'z'])->is(['x', 'y', 'z']);
    }
}
```

A return value or argument of Actual can transparently use the original method, as shown below.

```php
class Example
{
    private int $privateField = 0;

    public function getPrivate()
    {
        return $this->privateField;
    }

    public function setPrivate(int $field)
    {
        $this->privateField = $field;
    }
}

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    function test()
    {
        // test object
        $example = that(new Example());

        // directry private access
        $example->privateField = 3;
        $example->privateField->is(3);

        // $field is actual
        $field = $example->getPrivate();
        $field->is(3);

        // but, $field can use to arguments
        $example->setPrivate($field);
    }
}
```

### Custom constraints

Internals:

| constraint         | description
| :---               | :---
| ClosesTo           | assert float by auto approximation
| Contains           | assert string/iterable/file contains substring/element/content
| DatetimeEquals     | assert Datetimable equals
| EqualsFile         | assert string equals file
| EqualsIgnoreWS     | assert string equals ignoring whitespace
| EqualsPath         | assert path equals other path (compatible posix)
| FileContains       | assert file contains string
| FileEquals         | assert file equals string
| FileSizeIs         | assert file size
| HasKey             | assert array/object has key/property
| HtmlMatchesArray   | assert html string by array
| InTime             | assert processing in time
| Is                 | assert value with loosely
| IsBetween          | assert range of number
| IsBlank            | assert blank string
| IsCType            | assert value by ctype_xxx
| IsFalsy            | assert value like a false
| IsThrowable        | assert value is Throwable
| IsTruthy           | assert value like a true
| IsValid            | assert value by filter_var
| JsonMatchesArray   | assert json string based on array
| LengthEquals       | assert string/iterable/file length/count/size
| MatchesCountEquals | assert matched count element per array
| OutputMatches      | assert output of STDOUT
| StringLengthEquals | assert length of string
| SubsetEquals       | assert array by subarray
| SubsetMatches      | assert array at preg_match
| Throws             | assert callable should throw exception

Alias:

`\ryunosuke\PHPUnit\Actual::$constraintVariations` is searching for variation from other constraint.

```php
// Disable. Built-in constraints are not called
\ryunosuke\PHPUnit\Actual::$constraintVariations['isSame'] = false;
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

```php
// Disable. chain case function call
\ryunosuke\PHPUnit\Actual::$functionNamespaces = [];
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

### TestCaseTrait

This Trait provides testing utility.

- trapThrowable
    - If specified exception is thrown then skip the test.
- restorer
    - Reset function base's value. When unset return value recovery prev value.
- rewriteProperty
    - Rewrite private/protected property. When unset return value recovery prev value.
- getEnvOrSkip
    - Return getenv(). If novalue then skip the test.
- getConstOrSkip
    - Return constant(). If undefined then skip the test.
- getClassMap
    - Return all class => file array based on composer
- getClassByDirectory
    - Return class names by directory
- getClassByNamespace
    - Return class names by namespace
- emptyDirectory
    - Ready temporary directory and clean contents.
- backgroundTask
    - Run closure asynchronously.
- report
    - Report message to test result footer.

### Custom printer

This package provides Progress Printer.
This printer outputs only in case of failure. It will not output on success.

```xml
<phpunit printerClass="\ryunosuke\PHPUnit\Printer\ProgressPrinter">
</phpunit>
```

### Custom exporter

This package provides Custom Exporter.
This Exporter changes on the following.

- Extended maximum character width for strings
- Changed binary string to quoted string
- Changed to not insert tagged newline characters
- Changed object identifier from hash to id

```php
# e.g. bootstrap.php
ryunosuke\PHPUnit\Exporter\Exporter::insteadOf();
```

## Release

Versioning is Semantic Versioning.

### 3.17.0

- [feature] added getClassMap/getClassByDirectory/getClassByNamespace
- [feature] added IsTypeOf constraint

### 3.16.0

- [feature] added insteadof
- [change] obsolete clear global states

### 3.15.0

- [refactor] code format and fix inspection
- [feature] added clear state to that
- [fixbug] fixed Constraints and method calls is mixed
- [fixbug] changed getXXXOrSkip to static

### 3.14.0

- [feature] added TraversableComparator
- [fixbug] fixed self/static type
- [fixbug] fixed multiple markfile

### 3.13.1

- [fixbug] fixed sub-processes did not terminate when test failed.
- [fixbug] fixed single backquote noticed on Windows

### 3.13.0

- [feature] add after report
- [feature] generateStub supports glob pattern

### 3.12.0

- [change] suppressed warning at warning test
- [feature] added backgroundTask
- [fixbug] fixed mixin doesn't append no generated stub

### 3.11.0

- [change] changed ProgressPrinter format and support breakable test
- [feature] added trapThrowable
- [feature] added breakable
- [change] deprecated function caller
- [refactor] fixed wrong namespace

### 3.10.1

- [change] changed stub class is hierarchized
- [fixbug] fixed __set does not set ancestor private field
- [fixbug] fixed generateStub losts original type
- [fixbug] fixed generateStub ignores public member

### 3.10.0

- [feature] implove generateStub
- [feature] added MatchesCountEquals constaint
- [feature] added unwrapping original value if Actual argument
- [feature] added disable function option
- [change] deprecated static calls with __toString of object
- [fixbug] fixed caused exceptions to be implicitly through
- [fixbug] fixed filesystem function denies null string
- [fixbug] fixed __set private field
- [fixbug] fixed "debug" method returns null always

### 3.9.0

- [change] fixed printer oddities
  - improved portability
  - prefer specified columns
  - enable verbosity
  - print result on interrupt

### 3.8.1

- [feature] mark risky not asserting anything
- [feature] added wasOutputed/wasErrored/inElapsedTime method

### 3.8.0

- [feature] added restorer
- [feature] added get(Env|Const)OrSkip
- [change] fixed ExpectationFailedException message is too large
- [fixbug] fixed output is swallowed up

### 3.7.1

- [fixbug] fixed broken dependency

### 3.7.0

- [fixbug] fixed duplicated annotation
- [feature] added Is constaint (looser than IsEqual)
- [feature] added ClosesTo constaint
- [feature] added DatetimeEquals constaint
- [feature] supported SplFileInfo at file system
- [change] changed as method to variable arguments

### 3.6.0

- [refactor] changed private field name to be incompatible with stub generation
- [feature] implemented to disable built-in constraints
- [feature] added TestCaseTrait trait
- [feature] added declare method
- [feature] added new method
- [feature] added isUndefined variation
- [feature] added EqualsPath constaint
- [fixbug] fixed no $ in stub generation
- [fixbug] fixed strictly enforced due to frequent unintended function calls
- [fixbug] fixed in __callStatic where original method was not called

### 3.5.0

- [feature] added htmlMatchesArray supports style attribute
- [fixbug] fixed "try" catches necessary exceptions
- [change] implemented __callStatic omission

### 3.4.0

- [refactor] fixed annotation
- [feature] added ...[] syntax
- [feature] added stdout to results property
- [feature] added htmlMatchesArray supports class and closure
- [feature] added OutputMatches variation
- [fixbug] fixed the file location was on the test code when an error on the test target
- [fixbug] fixed progress disorder

### 3.3.0

- [feature] ProgressPrinter to show file location on failure
- [feature] htmlMatchesArray made it easier to understand when A fails

### 3.2.0

- [feature] add bootstrap.php for boilerplates
- [feature] print Actual value

### 3.1.0

- [feature] add final method for assertion statistic
- [feature] add raw flag to OutputMatches constraint
- [feature] add fn method for no-method callable
- [refactor] Establish self describing class

### 3.0.1

- [fixbug] vendor directories have difference during development and release
- [fixbug] callable that not closure/object throws exception

### 3.0.0

- [*change] see log

### 2.0.1

- [feature] support php8

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
