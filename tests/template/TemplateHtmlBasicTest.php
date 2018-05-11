<?php

namespace test\edwrodrig\static_generator\template;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class TemplateHtmlBasicTest extends \PHPUnit\Framework\TestCase
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
           'template_html_basic_test.php',
            $context
        );

        $page->generate();

        $expected_log = <<<LOG
Processing file [template_html_basic_test.php]...
  Generating file [template_html_basic_test]...DONE
DONE
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
        $this->assertRegexp(
            "/<body>\s*some_name Hola Mundo\s*<\/body>\s*<\/html>/",
            file_get_contents($page->getTargetAbsolutePath())
        );

    }
}

