<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 27-03-18
 * Time: 17:09
 */

namespace edwrodrig\static_generator\exception;


class NoTranslationAvailableException extends Exception
{

    /**
     * NoTranslationAvailableException constructor.
     * @param $translatable
     * @param string $lang
     */
    public function __construct($translatable, $lang)
    {
        ob_start();
        var_dump($translatable);
        parent::__construct(sprintf("[%s][%s]", ob_get_clean(), $lang));
    }
}