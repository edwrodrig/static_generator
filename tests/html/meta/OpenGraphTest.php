<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:35
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\OpenGraph;
use PHPUnit\Framework\TestCase;

class OpenGraphTest extends TestCase
{

    public function testPrint()
    {
        $s = new OpenGraph;
        $s->setType('website');
        $s->setDescription('hola');

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEquals('<meta property="og:type" content="website"/><meta property="og:description" content="hola"/>', $output);
    }
}
