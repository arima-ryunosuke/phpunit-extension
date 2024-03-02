<?php

use ryunosuke\PHPUnit\Replacer;

require_once __DIR__ . '/../vendor/autoload.php';

$root = realpath(__DIR__ . '/../');
$relative = fn($path) => preg_replace("#^" . preg_quote($root) . "[/\\\\]#", '', $path);

$loader = new ReflectionClass(Replacer::class);

$commands = [
    'patch' => function () use ($root, $relative, $loader) {
        foreach (Replacer::gather() as $classname => $aliasfile) {
            $classfile = (new ReflectionClass($classname))->getFileName();
            $patchfile = "$root/patch/" . strtr($classname, ['\\' => '/']) . '.patch';

            fwrite(STDOUT, "patch {$relative($classfile)} {$relative($patchfile)} > {$relative($aliasfile)}\n");

            $contents = file_get_contents($classfile);
            if (file_exists($patchfile)) {
                $contents = \ryunosuke\PHPUnit\str_patch($contents, file_get_contents($patchfile));
            }
            \ryunosuke\PHPUnit\file_set_contents($aliasfile, $contents);
        }
    },
    'diff'  => function () use ($root, $relative, $loader) {
        foreach (Replacer::gather() as $classname => $aliasfile) {
            $classfile = (new ReflectionClass($classname))->getFileName();
            $patchfile = "$root/patch/" . strtr($classname, ['\\' => '/']) . '.patch';

            fwrite(STDOUT, "diff {$relative($classfile)} {$relative($aliasfile)} > {$relative($patchfile)}\n");

            $diff = \ryunosuke\PHPUnit\str_diff(file_get_contents($classfile), file_get_contents($aliasfile), ['stringify' => 'unified=3']);
            \ryunosuke\PHPUnit\file_set_contents($patchfile, $diff);
        }
    },
];

$commands[$argv[1] ?? 'undefined']();
