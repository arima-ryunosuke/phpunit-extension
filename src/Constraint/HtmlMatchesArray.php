<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\ExpectationFailedException;

class HtmlMatchesArray extends AbstractConstraint
{
    private $nodes;

    public function __construct($nodes)
    {
        $this->nodes = $nodes;
    }

    protected function failureDescription($other): string
    {
        return $other;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($other);
        libxml_clear_errors();

        try {
            $this->match($this->nodes, $document->documentElement, []);
        }
        catch (ExpectationFailedException $e) {
            if ($returnResult) {
                return false;
            }
            $this->fail($e->getMessage(), '');
        }

        if ($returnResult) {
            return true;
        }
        return null;
    }

    private function match(array $nodes, \DOMElement $parent, array $pathes)
    {
        foreach ($nodes as $path => $attrs) {
            $fullpath = [...$pathes, $path];
            $cpath = $path;
            if (ctype_alpha($path[0])) {
                $cpath = ($pathes ? './' : '//') . $path;
            }

            $xpath = new \DOMXPath($parent->ownerDocument);
            $nodelist = $xpath->query($cpath, $parent);
            if ($nodelist === false || count($nodelist) !== 1) {
                throw new ExpectationFailedException(sprintf('%s should be single element. found %s elements', implode('/', $fullpath), $nodelist ? count($nodelist) : 'false'));
            }
            $this->compare($nodelist[0], $attrs, $fullpath);
        }
    }

    private function compare(\DOMElement $element, array $expected, array $fullpath)
    {
        foreach ($expected as $attr => $value) {
            if (is_array($value)) {
                $this->match([$attr => $value], $element, $fullpath);
            }
            elseif (is_int($attr)) {
                if (strpos($element->textContent, $value) === false) {
                    throw new ExpectationFailedException(sprintf('%s textContent contains "%s"', implode('/', $fullpath), $value));
                }
            }
            elseif (is_bool($value)) {
                if (!$element->hasAttribute($attr) && $value === true) {
                    throw new ExpectationFailedException(sprintf('%s[%s] should exist', implode('/', $fullpath), $attr));
                }
                if ($element->hasAttribute($attr) && $value === false) {
                    throw new ExpectationFailedException(sprintf('%s[%s] should not exist', implode('/', $fullpath), $attr));
                }
            }
            else {
                if ($element->getAttribute($attr) !== (string) $value) {
                    throw new ExpectationFailedException(sprintf('%s[%s] should be "%s"', implode('/', $fullpath), $attr, $value));
                }
            }
        }
    }
}
