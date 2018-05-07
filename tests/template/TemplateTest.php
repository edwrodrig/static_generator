<?php

namespace test\edwrodrig\static_generator\template;

use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\FileData;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \Exception
     */
    public function testGenerateTemplate()
    {

        $page = new PagePhp(
            new FileData(0, 'template_test.php', __DIR__ . '/../files'),
            '/tmp'
        );

        $page->generate();

        $this->assertStringEqualsFile($page->getAbsolutePath(), "some_name Hola Mundo");
    }
}

