<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 16-03-18
 * Time: 17:00
 */

namespace edwrodrig\static_generator\exception;


use Exception;

class TemplateNotDefinedException extends Exception
{

    /**
     * TemplateNotDefinedException constructor.
     * @param $php_file
     */
    public function __construct(string $php_file)
    {
        parent::__construct($php_file);
    }
}