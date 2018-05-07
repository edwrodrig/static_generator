<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 27-03-18
 * Time: 17:09
 */

namespace edwrodrig\static_generator\exception;


use Exception;

/**
 * Class NoTranslationAvailableException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class NoTranslationAvailableException extends Exception
{

    /**
     * NoTranslationAvailableException constructor.
     * @param $translatable
     * @param string $lang
     * @internal
     */
    public function __construct($translatable, $lang)
    {
        parent::__construct(self::message($translatable, $lang));
    }

    public static function message($translatable, $lang) {
        return sprintf("[%s][%s]", print_r($translatable, true), $lang);
    }
}