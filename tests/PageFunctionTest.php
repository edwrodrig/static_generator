<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PageFunction;
use edwrodrig\static_generator\util\TemporaryLogger;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class PageFunctionTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }


    /**
     * @throws Exception
     */
    function testGenerateString()
    {
        $logger = new TemporaryLogger;

        $context = new Context('', $this->root->url());
            $context->setLogger($logger);

        $page = new PageFunction(
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

