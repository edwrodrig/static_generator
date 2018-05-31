<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 31-05-18
 * Time: 11:51
 */

namespace edwrodrig\static_generator\exception;


use Exception;

/**
 * Class RelativePathCanBeFullException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class RelativePathCanNotBeFullException extends Exception
{

    /**
     * RelativePathCanBeFullException constructor.
     * @param string $path
     * @internal
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
    }
}