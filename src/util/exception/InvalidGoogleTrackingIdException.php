<?php
declare(strict_types=1);
namespace edwrodrig\static_generator\util\exception;


use Exception;

/**
 * Class InvalidGoogleTrackingIdException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class InvalidGoogleTrackingIdException extends Exception
{
    /**
     * InvalidGoogleTrackingIdException constructor.
     * @param string $tracking_id
     * @internal
     */
    public function __construct(string $tracking_id) {
        parent::__construct($tracking_id);
    }
}