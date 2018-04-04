<?php

namespace edwrodrig\static_generator;

trait Stack
{

    static public $stack = [];
    static public $last_element = null;

    static function level()
    {
        return count(self::$stack);
    }

    public static function log($str)
    {
        fprintf(STDOUT,
            "%s%s",
            str_repeat("  ", self::level()),
            $str
        );
    }

    static function get()
    {
        return self::$stack[self::level() - 1] ?? self::$last_element;
    }

    static function push($element)
    {
        self::$stack[] = $element;
    }

    static function pop()
    {
        self::$last_element = array_pop(self::$stack);
    }

    static function reset()
    {
        self::$stack = [];
        self::$last_element = null;
    }

}

