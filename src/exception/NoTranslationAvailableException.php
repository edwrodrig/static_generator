<?php
declare(strict_types=1);

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

    /**
     * @param $translatable
     * @param $lang
     * @return string
     */
    private static function message($translatable, $lang) {
        return sprintf("[%s][%s]", print_r($translatable, true), $lang);
    }
}