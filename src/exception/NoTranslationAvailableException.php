<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 27-03-18
 * Time: 17:09
 */

namespace edwrodrig\static_generator\exception;


use Exception;

class NoTranslationAvailableException extends Exception
{

    /**
     * NoTranslationAvailableException constructor.
     * @param $translatable
     * @param string $lang
     */
    public function __construct($translatable, $lang)
    {
        parent::__construct(self::message($translatable, $lang));
    }

    public static function message($translatable, $lang) {
        ob_start();
        print_r($translatable);
        return sprintf("[%s][%s]", ob_get_clean(), $lang);
    }
}