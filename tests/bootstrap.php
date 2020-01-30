<?php

error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../inc/annotation.php';

file_put_contents(__DIR__ . "/../inc/ryunosuke.php", \ryunosuke\Functions\Transporter::exportNamespace('\\ryunosuke\\PHPUnit'));

\ryunosuke\PHPUnit\Actual::$compatibleVersion = 2;
\ryunosuke\PHPUnit\Actual::$constraintVariations['lineCount'] = function ($other, int $lineCount, string $delimiter = "\\R") {
    return $lineCount === (preg_match_all("#$delimiter#", $other) + 1);
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
