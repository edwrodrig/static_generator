<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\TemporaryLogger;

class PagePhpTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    function testGenerateString()
    {
        exec('rm -rf /tmp/static_generator/test');

        $logger = new TemporaryLogger;

        $context = new Context(__DIR__, '/tmp/static_generator/test');
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


        $this->assertEquals($expected_log, $logger->getTargetData());
        $this->assertStringEqualsFile($page->getTargetAbsolutePath(), "Hola mundo");

    }

    /**
     * @expectedException \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @expectedExceptionMessage UnexistantTemplate
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     */
    public function testUnexistantTemplate()
    {
        $logger = new TemporaryLogger;

        $context = new Context(__DIR__ . '/files', '/tmp');
            $context->setLogger($logger);

        new PagePhp(
            'unexistant_template.php',
            $context
        );
    }
}

