<?php

namespace test\edwrodrig\static_generator\widget;

use edwrodrig\static_generator\util\exception\InvalidGoogleTrackingIdException;
use edwrodrig\static_generator\util\GoogleTrackingId;
use edwrodrig\static_generator\widget\GoogleTrackingSnippet;
use PHPUnit\Framework\TestCase;

class GoogleTrackingSnippetTest extends TestCase
{

    /**
     * @throws InvalidGoogleTrackingIdException
     */
    public function testHtml()
    {
        $tracking_id = new GoogleTrackingId('UA-123123-12');
        $snippet = new GoogleTrackingSnippet($tracking_id);
        ob_start();
        $snippet->html();
        /**
         * Thats all I want to test because implementation could change
         */
        $this->assertStringContainsString($tracking_id, ob_get_clean());
    }
}
