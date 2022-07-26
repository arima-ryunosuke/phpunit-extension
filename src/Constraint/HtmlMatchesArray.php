<?php

namespace ryunosuke\PHPUnit\Constraint;

use Closure;
use SebastianBergmann\Comparator\ComparisonFailure;
use function ryunosuke\PHPUnit\var_export2;

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
        $document->loadHTML($other ?: '<html></html>');
        libxml_clear_errors();

        try {
            $this->match($this->nodes, $document->documentElement, []);
        }
        catch (ComparisonFailure $e) {
            if ($returnResult) {
                return false;
            }
            $this->fail($e->getMessage(), $description, $e);
        }

        if ($returnResult) {
            return true;
        }
        return null;
    }

    private function match(array $nodes, \DOMElement $parent, array $pathes)
    {
        if ($parent->childNodes->count() && empty($nodes)) {
            $this->throwComparisonFailure(sprintf('%s not should be empty', implode('/', $pathes) ?: '/'), $parent, $nodes);
        }

        foreach ($nodes as $path => $attrs) {
            $fullpath = [...$pathes, $path];
            $cpath = $path;
            if (ctype_alpha($path[0])) {
                $cpath = ($pathes ? './' : '//') . $path;
            }

            $xpath = new \DOMXPath($parent->ownerDocument);
            $nodelist = $xpath->query($cpath, $parent);
            if ($nodelist === false || count($nodelist) !== 1) {
                $this->throwComparisonFailure(sprintf('%s should be single element. found %s elements', implode('/', $fullpath), $nodelist ? count($nodelist) : 'false'), $parent, $nodes);
            }
            $this->compare($nodelist[0], $attrs, $fullpath);
        }
    }

    private function compare(\DOMElement $element, array $expected, array $fullpath)
    {
        foreach ($expected as $attr => $value) {
            if ($attr === 'class' && is_array($value)) {
                $classes = preg_split('#\s#', $element->getAttribute($attr), -1, PREG_SPLIT_NO_EMPTY);
                if (($diff = array_diff($value, $classes)) !== []) {
                    $this->throwComparisonFailure(sprintf('%s[%s] should contain "%s"', implode('/', $fullpath), $attr, implode(' ', $diff)), $element, $expected);
                }
            }
            elseif (is_array($value)) {
                $this->match([$attr => $value], $element, $fullpath);
            }
            elseif (is_int($attr)) {
                if (!strlen($value) && strlen($element->textContent)) {
                    $this->throwComparisonFailure(sprintf('%s textContent should be empty', implode('/', $fullpath)), $element, $expected);
                }
                if (strlen($value) && strpos($element->textContent, $value) === false) {
                    $this->throwComparisonFailure(sprintf('%s textContent should contain "%s"', implode('/', $fullpath), $value), $element, $expected);
                }
            }
            elseif (is_bool($value)) {
                if (!$element->hasAttribute($attr) && $value === true) {
                    $this->throwComparisonFailure(sprintf('%s[%s] should exist', implode('/', $fullpath), $attr), $element, $expected);
                }
                if ($element->hasAttribute($attr) && $value === false) {
                    $this->throwComparisonFailure(sprintf('%s[%s] should not exist', implode('/', $fullpath), $attr), $element, $expected);
                }
            }
            elseif ($value instanceof Closure) {
                if (!$value($element->getAttribute($attr), $element)) {
                    $this->throwComparisonFailure(sprintf('%s[%s] should satisfy closure', implode('/', $fullpath), $attr), $element, $expected);
                }
            }
            else {
                if ($element->getAttribute($attr) !== (string) $value) {
                    $this->throwComparisonFailure(sprintf('%s[%s] should be "%s"', implode('/', $fullpath), $attr, $value), $element, $expected);
                }
            }
        }
    }

    private function throwComparisonFailure($message, $actual, $expected)
    {
        $actualArray = $this->nodeToArray($actual);
        throw new ComparisonFailure(
            $this->nodeArrayToString($expected),
            $this->nodeArrayToString($actualArray),
            $this->exporter()->export($expected),
            $this->exporter()->export(array_filter($actualArray, fn($array) => !is_array($array))),
            false,
            $message,
        );
    }

    private function nodeToArray(\DOMNode $node)
    {
        $result = [];

        foreach ($node->attributes as $attribute) {
            $result[$attribute->name] = $attribute->value;
        }

        $child_type = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMText) {
                $text = trim($child->textContent);
                if (strlen($text)) {
                    $result[] = $text;
                }
            }
            if ($child instanceof \DOMElement) {
                $tag = $child->tagName;
                $child_type[$tag] = ($child_type[$tag] ?? 0) + 1;
                $result["{$tag}[{$child_type[$tag]}]"] = $this->nodeToArray($child);
            }
        }

        return $result;
    }

    private function nodeArrayToString($array)
    {
        $string = var_export2($array, true);
        return preg_replace('#^(\s+)\d+\s+=> #mu', '$1', $string);
    }
}
