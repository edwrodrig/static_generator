<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\html;

/**
 * Class Attributes
 *
 * Convenience function to print html attributes
 * ```
 * <div <?=Attributes::create(['href' => 'https://www.edwin.cl', 'title'=> 'my page'])?>></div>
 * ```
 * @see Attributes::__construct
 * @package edwrodrig\static_generator\html
 */
class Attributes
{

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Attributes constructor.
     * * null values are ignored
     * * boolean true values makes print only the name [example: required]
     * * string values prints the name and the value [example: href="http://www.edwin.cl"]
     * @param array $attributes An associative array.
     */
    public function __construct(array $attributes) {
        $this->attributes = $attributes;
    }


    public static function addPart(string $name, $value, array &$parts) {

        if ( is_null($value) ) {
            return;
        } else if ( is_bool($value) && $value === true ) {
            $parts[] = $name;
        } else if ( is_string($value) ) {
            $parts[] = sprintf('%s="%s"', $name, htmlentities($value));
        } else if ( is_int($value) ) {
            $parts[] = sprintf('%s="%d"', $name, $value);
        } else {
            return;
        }
    }

    /**
     * Conversion to string
     * @return string
     */
    public function __toString() {
        $parts = [];
        foreach ( $this->attributes as $name => $value)
            self::addPart($name, $value, $parts);

        return implode(' ', $parts);
    }

    /**
     * Convenience constructor.
     *
     * new notation is ugly.
     * @uses Attributes::__construct
     * @param array $attributes
     * @return Attributes
     */
    public static function create(array $attributes) : Attributes {
        return new self($attributes);
    }
}