<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;

class PageFunctionTest extends \PHPUnit\Framework\TestCase
{

    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }


    function testGenerateString()
    {
        $logger = new TemporaryLogger;

        $context = new Context('', $this->root->url());
            $context->setLogger($logger);

        $page = new \edwrodrig\static_generator\PageFunction(
            'out',
            $context);

        $page->function = function () {
            echo "Hola mundo";
        };

        $output = $page->generate();

        $this->assertEquals('Generating file [out]...DONE', $logger->getTargetData());
        $this->assertEquals("Hola mundo", $output);

    }

}

