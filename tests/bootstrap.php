<?php

error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../inc/annotation.php';

$tmpdir = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';
@mkdir($tmpdir, 0777, true);
if (DIRECTORY_SEPARATOR === '\\') {
    putenv("TMP=$tmpdir");
}
else {
    putenv("TMPDIR=$tmpdir");
}

ryunosuke\PHPUnit\Exporter\Exporter::insteadOf();

/**
 * @template T
 * @param T $actual
 * @return \ryunosuke\PHPUnit\Actual|T
 */
function that($actual)
{
    return new \ryunosuke\PHPUnit\Actual($actual);
}

file_put_contents(__DIR__ . "/../inc/ryunosuke.stub", \ryunosuke\Functions\Transporter::exportNamespace('\\ryunosuke\\PHPUnit'));
file_put_contents(__DIR__ . "/../tests/ryunosuke.php", \ryunosuke\Functions\Transporter::exportNamespace('\\ryunosuke\\PHPUnit'));

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
