<?php
declare(strict_types=1);

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
     * @param string $template_class
     * @internal
     */
    public function __construct(string $template_class)
    {
        parent::__construct($template_class);
    }
}