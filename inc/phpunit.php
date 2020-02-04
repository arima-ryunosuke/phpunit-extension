<?php

namespace PHPUnit;

function importFunction()
{
    if ((require_once __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php') !== true) {
        // do something appendix
    }
}
