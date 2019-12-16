<?php

namespace test\edwrodrig\static_generator\widget;

use edwrodrig\static_generator\widget\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{

    function testPrint1()
    {
        $obj = new class extends Component
        {
            function content()
            {
                echo '@@@';
            }
        };

        $obj->setReplacement('id');
        ob_start();
        $obj->print();
        $this->assertEquals('id', ob_get_clean());

    }

    function testPrint2()
    {
        $obj = new class extends Component
        {
            function content()
            {
                echo '@@@ @@@ @@@';
            }

        };

        $obj->setReplacement('id');
        ob_start();
        $obj->print();
        $this->assertEquals('id id id', ob_get_clean());

    }

}

