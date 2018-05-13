<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 12-05-18
 * Time: 22:27
 */

namespace edwrodrig\static_generator\exception;

use Exception;

/**
 * Class CopyException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class CopyException extends Exception
{

    /**
     * CopyException constructor.
     * @param string $error
     * @internal
     */
    public function __construct(string $error)
    {
        parent::__construct($error);
    }
}