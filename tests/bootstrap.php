<?php

error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../inc/annotation.php';

file_put_contents(__DIR__ . "/../inc/ryunosuke.php", \ryunosuke\Functions\Transporter::exportNamespace('\\ryunosuke\\PHPUnit'));

\ryunosuke\PHPUnit\Actual::$compatibleVersion = 2;
\ryunosuke\PHPUnit\Actual::$constraintVariations['lineCount'] = new class(0, "") extends \PHPUnit\Framework\Constraint\Constraint {
    private $lineCount;
    private $delimiter;

    public function __construct(int $lineCount, string $delimiter = "\\R")
    {
        $this->lineCount = $lineCount;
        $this->delimiter = $delimiter;
    }

    protected function matches($other): bool
    {
        return $this->lineCount === (preg_match_all("#{$this->delimiter}#", $other) + 1);
    }

    public function toString(): string
    {
        return 'is ' . $this->lineCount . ' lines';
    }
};

file_put_contents(__DIR__ . '/../inc/annotation.php', "<?php

namespace ryunosuke\\PHPUnit;

" . \ryunosuke\PHPUnit\Actual::generateAnnotation() . "
trait Annotation
{
    function isHoge()
    {
        return \$this->eval(new \\PHPUnit\\Framework\\Constraint\\IsEqual('hoge'));
    }
}
");
