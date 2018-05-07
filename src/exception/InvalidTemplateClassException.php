<?php

namespace edwrodrig\static_generator\exception;


use Exception;

/**
 * Class InvalidTemplateClassException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class InvalidTemplateClassException extends Exception
{

    /**
     * InvalidTemplateClassException constructor.
     * @param \phpDocumentor\Reflection\DocBlock\Tag $template_class
     * @internal
     */
    public function __construct($template_class)
    {
        parent::__construct($template_class);
    }
}