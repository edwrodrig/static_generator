<?php

namespace test\edwrodrig\static_generator\widget;

use edwrodrig\static_generator\widget\ComponentFile;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ComponentFileTest extends TestCase
{
    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }

    function testInclude()
    {
        $file = $this->root->url() . DIRECTORY_SEPARATOR . 'file';
        file_put_contents($file, '@@@ @@@ @@@');

        ob_start();
        (new ComponentFile($file, 'id'))->print();
        $this->assertEquals('id id id', ob_get_clean());

    }

}

