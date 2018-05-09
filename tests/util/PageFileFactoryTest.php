<?php

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\PageFileFactory;

class PageFileFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $expected
     * @param string $input_file
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \edwrodrig\static_generator\util\exception\IgnoredPageFileException
     * @testWith    ["edwrodrig\\static_generator\\PagePhp", "h.php"]
     *              ["edwrodrig\\static_generator\\PageCopy", "h.jpg"]
     *              ["edwrodrig\\static_generator\\PageScss", "hola.scss"]
     */
    function testCreate(string $expected, string $input_file)
    {
        $page = PageFileFactory::createPage(
            $input_file,
            new Context('', '')
        );
        $this->assertInstanceOf($expected, $page);

    }


    /**
     * @param string $input_file
     * @expectedException \edwrodrig\static_generator\util\exception\IgnoredPageFileException
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @testWith    ["_hola.scss"]
     *              ["hola.swp"]
     */
    function testCreateIgnored(string $input_file) {
        PageFileFactory::createPage(
            $input_file,
            new Context('', '')
        );
    }

    public function testCreateTemplates()
    {
        /**
         * @var $templates \edwrodrig\static_generator\PagePhp[]
         */
        $templates = iterator_to_array(PageFileFactory::createTemplates(new Context(__DIR__ . '/../files/test_dir', '/tmp')));


        $this->assertCount(3, $templates);

        $this->assertEquals('template_test', $templates['template_test.php']->getTargetRelativePath());
        $this->assertEquals('template_html_basic_test', $templates['template_html_basic_test.php']->getTargetRelativePath());
        $this->assertEquals('hola', $templates['hola.php']->getTargetRelativePath());
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \edwrodrig\static_generator\util\exception\IgnoredPageFileException
     */
    public function testCreatePages()
    {
        /**
         * @var $pages \edwrodrig\static_generator\PageFile[]
         */
        $pages = iterator_to_array(PageFileFactory::createPages(new Context(__DIR__ . '/../files/test_dir', '/tmp')));

        $this->assertCount(5, $pages);

        $this->assertEquals('template_test', $pages['template_test.php']->getTargetRelativePath());
        $this->assertEquals('template_html_basic_test', $pages['template_html_basic_test.php']->getTargetRelativePath());
        $this->assertEquals('hola.html', $pages['hola.html']->getTargetRelativePath());
        $this->assertEquals('hola', $pages['hola.php']->getTargetRelativePath());
        $this->assertEquals('sub/chao.html', $pages['sub/chao.html']->getTargetRelativePath());

    }
}
