<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 16-03-18
 * Time: 17:15
 */

namespace edwrodrig\static_generator\exception;

use Exception;

class FileDoesNotExistsException extends Exception
{

    /**
     * FileDoesNotExistsException constructor.
     * @param $source
     */
    public function __construct(string $source)
    {
        parent::__construct($source);
    }
}