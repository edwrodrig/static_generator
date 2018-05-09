<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\widget;
use edwrodrig\static_generator\util\GoogleTrackingId;

/**
 * Class to add a google tracking snippet for html web pages.
 * When the class is created you eed to {@see GoogleTrackingIdSnippet::html() echo the output}.
 * You need to provide a {@see https://analytics.google.com google} tracking id provided by google.
 * @api
 * @package edwrodrig\static_generator
 */
class GoogleTrackingSnippet
{
    /**
     * A google tracking Id
     * @var GoogleTrackingId
     */
    private $tracking_id;

    /**
     * GoogleTrackingId constructor.
     * @api
     * @param GoogleTrackingId $tracking_id
     */
    public function __construct(GoogleTrackingId $tracking_id)
    {
        $this->tracking_id =  $tracking_id;
    }

    /**
     * echo the html tracking snippet.
     *
     * IT MUST BE IN THE HEAD PART OF THE SITE.
     * @api
     * @see https://support.google.com/analytics/answer/1008080?hl=en&authuser=1&ref_topic=1008079 Source info from 2018-05-05
     */
    public function html() {
        ?>
        <!-- Global Site Tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?=strval($this->tracking_id)?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '<?=strval($this->tracking_id)?>');
        </script>
        <?php
    }
}