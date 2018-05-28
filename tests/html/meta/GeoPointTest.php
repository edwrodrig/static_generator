<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 12:39
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\GeoPoint;
use PHPUnit\Framework\TestCase;

class GeoPointTest extends TestCase
{

    public function testPrint()
    {
        $s = new GeoPoint;
        $s->setLatitude("0.123");

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEquals('<meta property="place:location:latitude"  content="0.123">', $output);
    }
}
