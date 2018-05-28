<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 15:24
 */

namespace edwrodrig\static_generator\exception;

use Exception;

/**
 * Class InvalidTemplateMetadataException
 * @package Exception
 * @api
 */
class InvalidTemplateMetadataException extends Exception
{

    /**
     * InvalidTemplateMetadataException constructor.
     * @param mixed $parsed_data
     * @internal
     */
    public function __construct(string $parsed_data)
    {
        parent::__construct($parsed_data);
    }
}