<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 13-05-18
 * Time: 16:48
 */

namespace edwrodrig\static_generator\exception;


use Exception;

class CacheAlreadyRegisteredException extends Exception
{

    /**
     * CacheAlreadyRegisteredException constructor.
     * @param string $web_path
     */
    public function __construct(string $web_path)
    {
        parent::__construct($web_path);
    }
}