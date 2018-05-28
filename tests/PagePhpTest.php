<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PagePhpTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \Exception
     */
    function testGenerateString()
    {
        $logger = new TemporaryLogger;

        $context = new Context(__DIR__, $this->root->url());
            $context->setLogger($logger);

        $page = new PagePhp(
            'files/test_dir/hola.php',
            $context
        );
        $page->generate();

$expected_log = <<<LOG
Processing file [files/test_dir/hola.php]...
  Generating file [files/test_dir/hola]...DONE
DONE
LOG;


        $this->assertEquals('files/test_dir/hola', $page->getTargetRelativePath());
        $this->assertEquals($this->root->url() . '/files/test_dir/hola', $page->getTargetAbsolutePath());
        $this->assertFileExists($page->getTargetAbsolutePath());
        $this->assertEquals($expected_log, $logger->getTargetData());
        $this->assertStringEqualsFile($page->getTargetAbsolutePath(), "Hola mundo");

    }

    /**
     * @expectedException \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @expectedExceptionMessage UnexistantTemplate
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateMetadataException
     */
    public function testUnexistantTemplate()
    {
        $logger = new TemporaryLogger;

        $context = new Context(__DIR__ . '/files', $this->root->url());
            $context->setLogger($logger);

        new PagePhp(
            'unexistant_template.php',
            $context
        );
    }
}

