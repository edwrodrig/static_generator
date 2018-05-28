<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 28-05-18
 * Time: 11:52
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\BusinessContactData;
use PHPUnit\Framework\TestCase;

class BusinessContactDataTest extends TestCase
{

    public function testPrint()
    {
        $s = new BusinessContactData;
        $s->setWebsite('http://edwin.cl');

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEquals('<meta property="business:contact_data:website" content="http://edwin.cl" />', $output);
    }

    public function testPrintEmpty()
    {
        $s = new BusinessContactData;

        ob_start();
        $s->print();

        $output = ob_get_clean();

        $this->assertEmpty($output);
    }
}
