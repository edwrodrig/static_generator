<?php

namespace test\edwrodrig\static_generator\widget;

class ComponentFileTest extends \PHPUnit\Framework\TestCase
{

    function testInclude()
    {
        file_put_contents('/tmp/test_include', '@@@ @@@ @@@');

        ob_start();
        (new \edwrodrig\static_generator\widget\ComponentFile('/tmp/test_include', 'id'))->print();
        $this->assertEquals('id id id', ob_get_clean());

    }

}

