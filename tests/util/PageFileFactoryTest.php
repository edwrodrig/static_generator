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

}

