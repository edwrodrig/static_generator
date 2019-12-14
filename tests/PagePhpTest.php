<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\exception\InvalidTemplateMetadataException;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\TemporaryLogger;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class PagePhpTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws InvalidTemplateClassException
     * @throws Exception
     * @throws \Throwable
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
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     */
    public function testUnexistantTemplate()
    {
        $this->expectException(InvalidTemplateClassException::class);
        $this->expectExceptionMessage("UnexistantTemplate");
        $logger = new TemporaryLogger;

        $context = new Context(__DIR__ . '/files', $this->root->url());
            $context->setLogger($logger);

        new PagePhp(
            'unexistant_template.php',
            $context
        );
    }
}

