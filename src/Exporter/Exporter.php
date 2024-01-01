<?php declare(strict_types=1);
/*
 * Copyright (c) 2002-2022, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 *
 *  * Neither the name of Sebastian Bergmann nor the names of his
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
namespace ryunosuke\PHPUnit\Exporter;

use Iterator;
use LogicException;
use NoRewindIterator;
use SebastianBergmann\RecursionContext\Context;
use SplObjectStorage;
use function ryunosuke\PHPUnit\mb_ellipsis;

// @formatter:off

/**
 * fixes:
 * - shortenedExport: Extended maximum character width for strings
 * - recursiveExport: Changed binary string to quoted string
 * - recursiveExport: Changed to not insert tagged newline characters
 * - recursiveExport: Changed object identifier from hash to id
 */
class Exporter
{
    /**
     * @codeCoverageIgnore
     */
    public static function insteadOf($original = \SebastianBergmann\Exporter\Exporter::class)
    {
        static $registered = false;
        if ($registered) {
            return;
        }
        $registered = true;

        if (class_exists($original, false)) {
            throw new LogicException("$original is already declared");
        }

        spl_autoload_register(function ($class) use ($original) {
            if ($class === $original) {
                class_alias(self::class, $original);
            }
        }, true, true);
    }

    /**
     * Exports a value as a string.
     *
     * The output of this method is similar to the output of print_r(), but
     * improved in various aspects:
     *
     *  - NULL is rendered as "null" (instead of "")
     *  - TRUE is rendered as "true" (instead of "1")
     *  - FALSE is rendered as "false" (instead of "")
     *  - Strings are always quoted with single quotes
     *  - Carriage returns and newlines are normalized to \n
     *  - Recursion and repeated rendering is treated properly
     *
     * @param int $indentation The indentation level of the 2nd+ line
     *
     * @return string
     */
    public function export($value, $indentation = 0)
    {
        return $this->recursiveExport($value, $indentation);
    }

    /**
     * @param array<mixed> $data
     * @param ?Context     $context
     *
     * @return string
     */
    public function shortenedRecursiveExport(&$data, Context $context = null)
    {
        $result   = [];
        $exporter = new self();

        if (!$context) {
            $context = new Context;
        }

        $array = $data;
        $context->add($data);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($context->contains($data[$key]) !== false) {
                    $result[] = '*RECURSION*';
                } else {
                    $result[] = sprintf(
                        'array(%s)',
                        $this->shortenedRecursiveExport($data[$key], $context)
                    );
                }
            } else {
                $result[] = $exporter->shortenedExport($value);
            }
        }

        return implode(', ', $result);
    }

    /**
     * Exports a value into a single-line string.
     *
     * The output of this method is similar to the output of
     * SebastianBergmann\Exporter\Exporter::export().
     *
     * Newlines are replaced by the visible string '\n'.
     * Contents of arrays and objects (if any) are replaced by '...'.
     *
     * @return string
     *
     * @see    \SebastianBergmann\Exporter\Exporter::export
     */
    public function shortenedExport($value)
    {
        if (is_string($value)) {
            $string = str_replace("\n", '', $this->export($value));
            return mb_ellipsis($string, 80, '...');
        }

        if (is_object($value)) {
            return sprintf(
                '%s Object (%s)',
                get_class($value),
                count($this->toArray($value)) > 0 ? '...' : ''
            );
        }

        if (is_array($value)) {
            return sprintf(
                'Array (%s)',
                count($value) > 0 ? '...' : ''
            );
        }

        return $this->export($value);
    }

    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @return array
     */
    public function toArray($value)
    {
        if (!is_object($value)) {
            return (array) $value;
        }

        $array = [];

        if ($value instanceof Iterator && $value->valid() && !$value instanceof SplObjectStorage) {
            foreach (new NoRewindIterator($value) as $key => $val) {
                if (is_scalar($key) || is_resource($key)) {
                    $array["$key"] = $val;
                } else {
                    $array[json_encode($key, JSON_UNESCAPED_UNICODE)] = $val;
                }
            }

            return $array;
        }

        foreach ((array) $value as $key => $val) {
            // Exception traces commonly reference hundreds to thousands of
            // objects currently loaded in memory. Including them in the result
            // has a severe negative performance impact.
            if ("\0Error\0trace" === $key || "\0Exception\0trace" === $key) {
                continue;
            }

            // properties are transformed to keys in the following way:
            // private   $property => "\0Classname\0property"
            // protected $property => "\0*\0property"
            // public    $property => "property"
            if (preg_match('/^\0.+\0(.+)$/', (string) $key, $matches)) {
                $key = $matches[1];
            }

            // See https://github.com/php/php-src/commit/5721132
            if ($key === "\0gcdata") {
                continue; // @codeCoverageIgnore
            }

            $array[$key] = $val;
        }

        // Some internal classes like SplObjectStorage don't work with the
        // above (fast) mechanism nor with reflection in Zend.
        // Format the output similarly to print_r() in this case
        if ($value instanceof SplObjectStorage) {
            foreach ($value as $key => $val) {
                $array[spl_object_hash($val)] = [
                    'obj' => $val,
                    'inf' => $value->getInfo(),
                ];
            }
        }

        return $array;
    }

    /**
     * Recursive implementation of export.
     *
     * @param mixed                                       $value       The value to export
     * @param int                                         $indentation The indentation level of the 2nd+ line
     * @param \SebastianBergmann\RecursionContext\Context $processed   Previously processed objects
     *
     * @return string
     *
     * @see    \SebastianBergmann\Exporter\Exporter::export
     */
    protected function recursiveExport(&$value, $indentation, $processed = null)
    {
        if ($value === null) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_float($value) && (float) ((int) $value) === $value) {
            return "{$value}.0";
        }

        if (gettype($value) === 'resource (closed)') {
            return 'resource (closed)';
        }

        if (is_resource($value)) {
            return sprintf(
                'resource(%d) of type (%s)',
                $value,
                get_resource_type($value)
            );
        }

        if (is_string($value)) {
            // Match for most non printable chars somewhat taking multibyte chars into account
            if (preg_match('/[^\x09-\x0d\x1b\x20-\xff]/', $value)) {
                return 'Quoted String: "' . addcslashes($value, "\0..\11!@\14..\37!@\177..\377") . '"';
            }

            return "'$value'";
        }

        $whitespace = str_repeat(' ', 4 * $indentation);

        if (!$processed) {
            $processed = new Context;
        }

        if (is_array($value)) {
            if (($key = $processed->contains($value)) !== false) {
                return 'Array &' . $key;
            }

            $array  = $value;
            $key    = $processed->add($value);
            $values = '';

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        $this->recursiveExport($k, $indentation),
                        $this->recursiveExport($value[$k], $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('Array &%s (%s)', $key, $values);
        }

        if (is_object($value)) {
            $class = get_class($value);
            $hash  = spl_object_id($value);

            if ($processed->contains($value)) {
                return sprintf('%s Object &%s', $class, $hash);
            }

            $processed->add($value);
            $values = '';
            $array  = $this->toArray($value);

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        $this->recursiveExport($k, $indentation),
                        $this->recursiveExport($v, $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('%s Object &%s (%s)', $class, $hash, $values);
        }

        return var_export($value, true);
    }
}
