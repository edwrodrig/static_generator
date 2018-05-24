<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     */
    function testTranslate()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertEquals('es', $s->tr(['es' => 'es', 'en' => 'en']));
        $this->assertTrue($s->hasTr(['es' => 'es', 'en' => 'en']));

        setlocale(LC_ALL, 'en_US.utf-8');
        $this->assertEquals('en', $s->tr(['es' => 'es', 'en' => 'en']));
        $this->assertTrue($s->hasTr(['es' => 'es', 'en' => 'en']));
    }

    /**
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [Array
     * (
     * )
     * ][es]
     */
    public function testTranslateNoTranslation()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr([]));
        $s->tr([]);
    }


    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     */
    public function testTranslateNoTranslationDefault()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr([]));
        $this->assertEquals('hola', $s->tr([], 'hola'));

    }

    /**
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     */
    public function testTranslateNoTranslation2()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr(null));
        $s->tr(null);
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     */
    public function testTranslateNoTranslation3()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $s->tr('');
    }

    /**
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws \edwrodrig\static_generator\util\exception\IgnoredPageFileException
     */
    public function testGetTemplates() {
        /**
         * @var $templates \edwrodrig\static_generator\template\Template[]|iterable
         */
        $templates = (new Context(__DIR__ . '/files/test_dir', $this->root->url()))->getTemplates();


        $this->assertCount(3, $templates);
    }


}

