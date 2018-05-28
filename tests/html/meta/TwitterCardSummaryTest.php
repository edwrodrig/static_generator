<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:49
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\TwitterCardSummary;
use PHPUnit\Framework\TestCase;

class TwitterCardSummaryTest extends TestCase
{

    public function testPrintSummary()
    {
        $s = new TwitterCardSummary;

        ob_start();
        $s->print();

        $output = ob_get_clean();
        $this->assertEquals('<meta name="twitter:card" content="summary"/>', $output);
    }

    public function testPrintSummaryLarge()
    {
        $s = new TwitterCardSummary;
        $s->setLargeImage(true);

        ob_start();
        $s->print();

        $output = ob_get_clean();
        $this->assertEquals('<meta name="twitter:card" content="summary_large_image"/>', $output);
    }
}
