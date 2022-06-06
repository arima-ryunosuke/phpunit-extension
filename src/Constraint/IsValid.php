<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsValid extends AbstractConstraint
{
    public const VALID_INT   = 'int';
    public const VALID_FLOAT = 'float';
    public const VALID_EMAIL = 'email';
    public const VALID_IP    = 'ip';
    public const VALID_IPV4  = 'ipv4';
    public const VALID_IPV6  = 'ipv6';
    public const VALID_MAC   = 'mac';
    public const VALID_URL   = 'url';

    private const KNOWN_TYPES = [
        self::VALID_INT   => [
            FILTER_VALIDATE_INT => 0,
        ],
        self::VALID_FLOAT => [
            FILTER_VALIDATE_FLOAT => 0,
        ],
        self::VALID_EMAIL => [
            FILTER_VALIDATE_EMAIL => 0,
        ],
        self::VALID_IP    => [
            FILTER_VALIDATE_IP => 0,
        ],
        self::VALID_IPV4  => [
            FILTER_VALIDATE_IP => FILTER_FLAG_IPV4,
        ],
        self::VALID_IPV6  => [
            FILTER_VALIDATE_IP => FILTER_FLAG_IPV6,
        ],
        self::VALID_MAC   => [
            FILTER_VALIDATE_MAC => 0,
        ],
        self::VALID_URL   => [
            FILTER_VALIDATE_URL => 0,
        ],
    ];

    private $type;
    private $flags;

    public function __construct(string $type, $flags = 0)
    {
        if (!isset(self::KNOWN_TYPES[$type])) {
            throw new \PHPUnit\Framework\Exception(sprintf('Type specified for %s <%s> ' . 'is not a valid type.', __CLASS__, $type));
        }

        $this->type = $type;
        $this->flags = $flags;
    }

    protected function matches($other): bool
    {
        $result = true;
        foreach (self::KNOWN_TYPES[$this->type] as $filter => $flags) {
            $result = $result && filter_var($other, $filter, ['flags' => $this->flags | $flags]) !== false;
        }
        return $result;
    }

    public function toString(): string
    {
        return sprintf('is valid "%s"', $this->type);
    }
}
