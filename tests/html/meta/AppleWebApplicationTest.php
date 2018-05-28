<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:54
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\AppleWebApplication;
use PHPUnit\Framework\TestCase;

class AppleWebApplicationTest extends TestCase
{

    public function testPrint()
    {
        $s = new AppleWebApplication;
        $s->setWebCapable(true);

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEquals('<meta name="apple-mobile-web-app-capable" content="yes">', $output);
    }


    public function testPrintNotWebCapable()
    {
        $s = new AppleWebApplication;
        $s->setWebCapable(false);

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEmpty($output);
    }

    public function testPrintDefaultWebCapable()
    {
        $s = new AppleWebApplication;

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEmpty($output);
    }
}
