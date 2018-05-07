<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 06-05-18
 * Time: 9:50
 */

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\exception\InvalidGoogleTrackingIdException;
use edwrodrig\static_generator\util\GoogleTrackingId;
use PHPUnit\Framework\TestCase;

class GoogleTrackingIdTest extends TestCase
{

    /**
     * @param string $tracking_id An invalid tracking id
     * @throws InvalidGoogleTrackingIdException
     * @expectedException \edwrodrig\static_generator\util\exception\InvalidGoogleTrackingIdException
     * @testWith    ["AB-123123-12"]
     */
    public function testInvalidTrackingId(string $tracking_id) {
        new GoogleTrackingId($tracking_id);
    }
}
