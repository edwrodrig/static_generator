<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 02-04-18
 * Time: 11:50
 */

namespace edwrodrig\static_generator;


class GoogleTrackingId
{
    private $tracking_id;

    public function __construct(string $tracking_id)
    {
        $this->tracking_id = $tracking_id;
    }

    public function html_block() {
        ?>
        <script>
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '<?=$this->tracking_id?>']);
            _gaq.push(['_trackPageview']);

            (function () {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        </script>
        <?php
    }
}