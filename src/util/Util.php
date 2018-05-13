<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;

use Error;
use Exception;

class Util
{

    /**
     * @param $data
     * @return string
     * @throws Exception
     */
    static function html_string($data) : string
    {
        return htmlspecialchars(self::outputBufferSafe($data));
    }

    /**
     * This function nicely closes a {@see ob_get_clean() output buffer} context in the case of an Error.
     *
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

}
