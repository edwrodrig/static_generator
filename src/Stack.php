<?php

namespace edwrodrig\static_generator;

trait Stack
{

    static public $stack = [];

    static function level()
    {
        return count(self::$stack);
    }

    function log($str)
    {
        printf(
            "%s%s",
            str_repeat("  ", self::level()),
            $str
        );
    }

    static function get()
    {
        return self::$stack[self::level() - 1];
    }

    static function push($element)
    {
        self::$stack[] = $element;
    }

    static function pop()
    {
        array_pop(self::$stack);
    }

    static function reset()
    {
        self::$stack = [];
    }

}

