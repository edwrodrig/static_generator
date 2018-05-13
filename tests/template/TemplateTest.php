<?php

namespace test\edwrodrig\static_generator\template;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }


    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \Exception
     */
    public function testGenerateTemplate()
    {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
            $context->setLogger($logger);

        $page = new PagePhp(
            'template_test.php',
            $context
        );

        $page->generate();

        $expected_log = <<<LOG
Processing file [template_test.php]...
  Generating file [template_test]...DONE
DONE
LOG;
        $this->assertEquals('template_test', $page->getTargetRelativePath());
        $this->assertEquals($this->root->url() . '/template_test', $page->getTargetAbsolutePath());
        $this->assertFileExists($page->getTargetAbsolutePath());
        $this->assertEquals($expected_log, $logger->getTargetData());
        $this->assertStringEqualsFile($page->getTargetAbsolutePath(), "some_name Hola Mundo");
    }


    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testUrl()
    {
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setTargetWebPath('es');
        $page = new PagePhp('out.html', $context);
        $template = $page->getTemplate();
        $this->assertEquals('out.html', $page->getTargetRelativePath());
        $this->assertEquals($this->root->url() . '/out.html', $page->getTargetAbsolutePath());
        $this->assertEquals('in.html', $template->url('in.html'));
        $this->assertEquals('/es/in.html', $template->url('/in.html'));
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testUrlEmptyTargetWebPath()
    {
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $page = new PagePhp('out.html', $context);
        $template = $page->getTemplate();
        $this->assertEquals('out.html', $page->getTargetRelativePath());
        $this->assertEquals($this->root->url() . '/out.html', $page->getTargetAbsolutePath());
        $this->assertEquals('in.html', $template->url('in.html'));
        $this->assertEquals('/in.html', $template->url('/in.html'));
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testCurrentUrl()
    {
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setTargetWebPath('es');
        $page = new PagePhp('out.html', $context);
        $template = $page->getTemplate();
        $this->assertEquals('/es/out.html', $template->currentUrl());
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testCurrentUrlEmptyTargetWebPat()
    {
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $page = new PagePhp('out.html', $context);
        $template = $page->getTemplate();
        $this->assertEquals('/out.html', $template->currentUrl());
    }
}

