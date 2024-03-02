<?php

namespace ryunosuke\PHPUnit;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @codeCoverageIgnore
 */
class Replacer
{
    public static function insteadOf()
    {
        $classes = self::gather();
        spl_autoload_register(function ($class) use ($classes) {
            if (isset($classes[$class])) {
                require_once $classes[$class];
            }
        }, true, true);
    }

    public static function gather(): array
    {
        $result = [];

        $rdi = new RecursiveDirectoryIterator(__DIR__ . '/replace', RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::CURRENT_AS_SELF);
        $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::LEAVES_ONLY);

        /** @var RecursiveDirectoryIterator $file */
        foreach ($rii as $file) {
            $classname = strtr(preg_replace('#\.php$#', '', $file->getSubPathname()), ['/' => '\\']);

            $result[$classname] = $file->getRealPath();
        }

        return $result;
    }
}
