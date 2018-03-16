<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 16-03-18
 * Time: 17:01
 */

namespace edwrodrig\static_generator\exception;

use Exception;

class TemplateClassDoesNotExistsException extends Exception
{

    /**
     * TemplateClassDoesNotExists constructor.
     * @param $template_class
     */
    public function __construct(string $template_class)
    {
        parent::__construct($template_class);
    }
}