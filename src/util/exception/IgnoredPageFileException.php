<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-05-18
 * Time: 13:39
 */

namespace edwrodrig\static_generator\util\exception;


use Exception;

/**
 * Class IgnoredPageFileException
 * @package edwrodrig\static_generator\util\exception
 * @api
 */
class IgnoredPageFileException extends Exception
{

    /**
     * IgnoredPageFileException constructor.
     * @internal
     * @param string $ignored_filename
     */
    public function __construct(string $ignored_filename)
    {
        parent::__construct($ignored_filename);
    }
}