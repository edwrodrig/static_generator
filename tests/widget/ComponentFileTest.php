<?php

namespace test\edwrodrig\static_generator\widget;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ComponentFileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    function testInclude()
    {
        $file = $this->root->url() . DIRECTORY_SEPARATOR . 'file';
        file_put_contents($file, '@@@ @@@ @@@');

        ob_start();
        (new \edwrodrig\static_generator\widget\ComponentFile($file, 'id'))->print();
        $this->assertEquals('id id id', ob_get_clean());

    }

}

