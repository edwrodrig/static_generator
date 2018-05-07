<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\FileData;

class PagePhpTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    function testGenerateString()
    {
        exec('rm -rf /tmp/static_generator/test');

        $page = new PagePhp(
            new FileData(0, 'files/test_dir/hola.php', __DIR__),
            '/tmp/static_generator/test'
        );
        $page->generate();

        $this->assertStringEqualsFile($page->getAbsolutePath(), "Hola mundo");

    }

    /**
     * @expectedException \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @expectedExceptionMessage UnexistantTemplate
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testUnexistantTemplate()
    {

        new PagePhp(
            new FileData(0, 'unexistant_template.php', __DIR__ . '/files'),
            '/tmp'
        );
    }
}

