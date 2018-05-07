<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 16-03-18
 * Time: 17:15
 */

namespace edwrodrig\static_generator\util\exception;

use Exception;

/**
 * Class FileDoesNotExistsException
 * @package edwrodrig\static_generator\util\exception
 * @api
 */
class FileDoesNotExistsException extends Exception
{

    /**
     * FileDoesNotExistsException constructor.
     * @param $source
     * @internal
     */
    public function __construct(string $source)
    {
        parent::__construct($source);
    }
}