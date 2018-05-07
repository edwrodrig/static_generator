<?php

namespace test\edwrodrig\static_generator\template;

use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\FileData;

class TemplateHtmlBasicTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \Exception
     */
    public function testGenerateTemplate()
    {

        $page = new PagePhp(
            new FileData(0, 'template_html_basic_test.php', __DIR__ . '/../files'),
            '/tmp'
        );

        $page->generate();


        $this->assertRegexp(
            "#some\_name Hola Mundo.*</html#",
            file_get_contents($page->getAbsolutePath())
        );

    }
}

