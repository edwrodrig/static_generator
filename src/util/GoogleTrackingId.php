<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 02-04-18
 * Time: 11:50
 */

namespace edwrodrig\static_generator\util;
use edwrodrig\static_generator\util\exception\InvalidGoogleTrackingIdException;

/**
 * Class to hold a google tracking id
 * @see https://analytics.google.com To get tracking ids
 * @api
 * @package edwrodrig\static_generator
 */
class GoogleTrackingId
{
    /**
     * A google tracking Id
     * Generally something like {@see GoogleTrackingId::TRACKING_ID_REGEX UA-1234567-1}
     * @var string
     */
    private $tracking_id;

    /**
     * Regular expression that validated and tracking id.
     * @see https://gist.github.com/faisalman/924970
     * @used-by GoogleTrackingSnippet
     */
    const TRACKING_ID_REGEX = '/^ua-\d{4,9}-\d{1,4}$/i';

    /**
     * GoogleTrackingId constructor.
     * @api
     * @param string $tracking_id {@see GoogleTrackingId::$tracking_id }
     * @throws \edwrodrig\static_generator\util\exception\InvalidGoogleTrackingIdException when the tracking_id is invalid
     */
    public function __construct(string $tracking_id)
    {
        $tracking_id =  trim($tracking_id);
        if ( preg_match(self::TRACKING_ID_REGEX, $tracking_id) ) {
            $this->tracking_id = $tracking_id;
        } else {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new InvalidGoogleTrackingIdException($tracking_id);
        }
    }

    /**
     * Get the tracking id as string
     * @api
     * @return string
     */
    public function __toString() : string {
        return $this->tracking_id;
    }
}