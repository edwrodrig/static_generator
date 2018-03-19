<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 19-03-18
 * Time: 13:38
 */

namespace edwrodrig\static_generator\exception;

use Exception;

class WrongDataException extends Exception
{

    /**
     * WrongMetadataException constructor.
     * @param mixed $parsed_metadata
     */
    public function __construct($parsed_data)
    {
        ob_start();
        var_dump($parsed_data);
        parent::__construct(ob_get_clean());
    }
}