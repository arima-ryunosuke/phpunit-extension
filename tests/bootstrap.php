<?php

error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/annotation.php';


file_put_contents(__DIR__ . '/annotation.php', "<?php

namespace ryunosuke\\PHPUnit;

" . \ryunosuke\PHPUnit\Actual::generateAnnotation() . "
trait Annotation
{
    function isHoge()
    {
        \$this->assert(new \\PHPUnit\\Framework\\Constraint\\IsEqual('hoge'));
        return \$this;
    }
}
");
