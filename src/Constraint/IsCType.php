<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsCType extends AbstractConstraint
{
    public const CTYPE_ALNUM  = 'alnum';
    public const CTYPE_ALPHA  = 'alpha';
    public const CTYPE_CNTRL  = 'cntrl';
    public const CTYPE_DIGIT  = 'digit';
    public const CTYPE_GRAPH  = 'graph';
    public const CTYPE_LOWER  = 'lower';
    public const CTYPE_PRINT  = 'print';
    public const CTYPE_PUNCT  = 'punct';
    public const CTYPE_SPACE  = 'space';
    public const CTYPE_UPPER  = 'upper';
    public const CTYPE_XDIGIT = 'xdigit';

    private const KNOWN_TYPES = [
        self::CTYPE_ALNUM  => true,
        self::CTYPE_ALPHA  => true,
        self::CTYPE_CNTRL  => true,
        self::CTYPE_DIGIT  => true,
        self::CTYPE_GRAPH  => true,
        self::CTYPE_LOWER  => true,
        self::CTYPE_PRINT  => true,
        self::CTYPE_PUNCT  => true,
        self::CTYPE_SPACE  => true,
        self::CTYPE_UPPER  => true,
        self::CTYPE_XDIGIT => true,
    ];

    private $type;

    public function __construct(string $type)
    {
        if (!isset(self::KNOWN_TYPES[$type])) {
            throw new \PHPUnit\Framework\Exception(sprintf('Type specified for %s <%s> ' . 'is not a valid type.', __CLASS__, $type));
        }

        $this->type = $type;
    }

    protected function matches($other): bool
    {
        $funcname = 'ctype_' . $this->type;
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $result = $funcname("$other");
        setlocale(LC_CTYPE, $locale);
        return $result;
    }

    public function toString(): string
    {
        return sprintf('is of ctype "%s"', $this->type);
    }
}
