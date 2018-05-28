<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;

use Error;
use Exception;

/**
 * Class Util
 * @package edwrodrig\static_generator\util
 * @api
 */
class Util
{
    /**
     * This function nicely closes a {@see ob_get_clean() output buffer} context in the case of an Error.
     *
     * @api
     * @param $content
     * @return string
     * @throws Exception
     * @throws Error
     */
    public static function outputBufferSafe($content) : string
    {
        if (!is_callable($content)) {
            return strval($content);
        }

        $level = ob_get_level();
        try {
            ob_start();
            $content();
            return ob_get_clean();

        } catch ( Exception | Error $e) {
            while (ob_get_level() > $level) ob_get_clean();
            throw $e;
        }
    }

    /**
     * sprintf if all args are not null, empty string otherwise.
     *
     * @see sprintf()
     * @param $str
     * @param string|null ...$args
     * @return string
     */
    public static function sprintfOrEmpty($str, ...$args) : string {
        foreach ( $args as &$arg ) {
            if ( is_null($arg) ) return "";
            if ( is_string($arg))
                $arg = htmlentities($arg);
        }
        return sprintf($str, ...$args);
    }

}
