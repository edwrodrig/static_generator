<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PageFunctionTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }


    /**
     * @throws \Exception
     */
    function testGenerateString()
    {
        $logger = new TemporaryLogger;

        $context = new Context('', $this->root->url());
            $context->setLogger($logger);

        $page = new \edwrodrig\static_generator\PageFunction(
            'out',
            $context,
            function () {
              echo "Hola mundo";
            }
        );

        $output = $page->generate();

        $this->assertEquals('Generating file [out]...DONE', $logger->getTargetData());
        $this->assertEquals("Hola mundo", $output);

    }

}

