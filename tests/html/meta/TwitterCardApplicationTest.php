<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:51
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\TwitterCardApplication;
use PHPUnit\Framework\TestCase;

class TwitterCardApplicationTest extends TestCase
{

    public function testPrint()
    {
        $s = new TwitterCardApplication;

        ob_start();
        $s->print();

        $output = ob_get_clean();
        $this->assertEquals('<meta name="twitter:card" content="app"/>', $output);
    }
}
