<?php

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\exception\InvalidTemplateMetadataException;
use edwrodrig\static_generator\PageFile;
use edwrodrig\static_generator\template\Template;
use edwrodrig\static_generator\util\exception\IgnoredPageFileException;
use edwrodrig\static_generator\util\PageFileFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class PageFileFactoryTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }


    /**
     * @param string $expected
     * @param string $input_file
     * @throws InvalidTemplateClassException
     * @throws IgnoredPageFileException
     * @throws InvalidTemplateMetadataException
     * @testWith    ["edwrodrig\\static_generator\\PagePhp", "h.php"]
     *              ["edwrodrig\\static_generator\\PageCopy", "h.jpg"]
     *              ["edwrodrig\\static_generator\\PageScss", "hola.scss"]
     */
    function testCreate(string $expected, string $input_file)
    {
        $page = PageFileFactory::createPage(
            $input_file,
            new Context('', $this->root->url())
        );
        $this->assertInstanceOf($expected, $page);

    }


    /**
     * @param string $input_file
     * @throws IgnoredPageFileException
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     * @testWith    ["_hola.scss"]
     *              ["hola.swp"]
     *              [".cache_index.json"]
     */
    function testCreateIgnored(string $input_file) {
        $this->expectException(IgnoredPageFileException::class);
        PageFileFactory::createPage(
            $input_file,
            new Context('', $this->root->url())
        );
    }

    /**
     * @throws IgnoredPageFileException
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     */
    public function testCreateTemplates()
    {
        /**
         * @var $templates Template[]|iterable
         */
        $templates = iterator_to_array(PageFileFactory::createTemplates(new Context(__DIR__ . '/../files/test_dir', $this->root->url())));


        $this->assertCount(3, $templates);

        $this->assertEquals('template_test', $templates['template_test.php']->getPageInfo()->getTargetRelativePath());
        $this->assertEquals('template_html_basic_test', $templates['template_html_basic_test.php']->getPageInfo()->getTargetRelativePath());
        $this->assertEquals('hola', $templates['hola.php']->getPageInfo()->getTargetRelativePath());
    }

    /**
     * @throws IgnoredPageFileException
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     */
    public function testCreatePages()
    {
        /**
         * @var $pages PageFile[]|iterable
         */
        $pages = iterator_to_array(PageFileFactory::createPages(new Context(__DIR__ . '/../files/test_dir', $this->root->url())));

        $this->assertCount(5, $pages);

        $this->assertEquals('template_test', $pages['template_test.php']->getTargetRelativePath());
        $this->assertEquals('template_html_basic_test', $pages['template_html_basic_test.php']->getTargetRelativePath());
        $this->assertEquals('hola.html', $pages['hola.html']->getTargetRelativePath());
        $this->assertEquals('hola', $pages['hola.php']->getTargetRelativePath());
        $this->assertEquals('sub/chao.html', $pages['sub/chao.html']->getTargetRelativePath());

    }
}

