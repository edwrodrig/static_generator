<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:30
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\Favicon;
use PHPUnit\Framework\TestCase;

class FaviconTest extends TestCase
{

    public function testPrint()
    {
        $s = new Favicon;
        $s->setIcon16x16('hola16');
        $s->setIcon48x48('hola48');

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEquals('<link rel="shortcut icon" sizes="16x16" href="hola16"><link rel="shortcut icon" sizes="48x48" href="hola48">', $output);
    }
}
