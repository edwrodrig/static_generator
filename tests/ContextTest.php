<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\exception\NoTranslationAvailableException;
use edwrodrig\static_generator\exception\RelativePathCanNotBeFullException;
use edwrodrig\static_generator\exception\UnregisteredWebDomainException;
use edwrodrig\static_generator\template\Template;
use edwrodrig\static_generator\util\exception\IgnoredPageFileException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws NoTranslationAvailableException
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

    public function testTranslateNoTranslation()
    {
        $this->expectException(NoTranslationAvailableException::class);
        $this->expectExceptionMessage("[Array
(
)
][es]");
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr([]));
        $s->tr([]);
    }


    /**
     * @throws NoTranslationAvailableException
     */
    public function testTranslateNoTranslationDefault()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr([]));
        $this->assertEquals('hola', $s->tr([], 'hola'));

    }

    /**
     * @throws NoTranslationAvailableException
     */
    public function testTranslateNoTranslation2()
    {
        $this->expectException(NoTranslationAvailableException::class);
        $this->expectExceptionMessage("[][es]");

        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertFalse($s->hasTr(null));
        $s->tr(null);
    }

    /**
     * @throws NoTranslationAvailableException
     */
    public function testTranslateNoTranslation3()
    {
        $this->expectException(NoTranslationAvailableException::class);
        $this->expectExceptionMessage("[][es]");

        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $s->tr('');
    }

    /**
     * @throws InvalidTemplateClassException
     * @throws IgnoredPageFileException
     */
    public function testGetTemplates() {
        /**
         * @var $templates Template[]|iterable
         */
        $templates = (new Context(__DIR__ . '/files/test_dir', $this->root->url()))->getTemplates();


        $this->assertCount(3, $templates);
    }


    public function testFullUrlSimple() {
        $s = new Context('', '');
        $s->setTargetWebDomain('http://www.edwin.cl');
        $this->assertEquals('/hola', $s->getUrl('/hola'));
        $this->assertEquals('http://www.edwin.cl/hola', $s->getFullUrl('/hola'));
    }

    public function testFullUrlSimpleEmptyDomain() {
        $this->expectException(UnregisteredWebDomainException::class);

        $s = new Context('', '');
        $this->assertEquals('/hola', $s->getUrl('/hola'));
        $s->getFullUrl('/hola');
    }

    public function testFullUrlSimpleTargetWebPath() {
        $s = new Context('', '');
        $s->setTargetWebPath('base');
        $s->setTargetWebDomain('http://www.edwin.cl');
        $this->assertEquals('/base/hola', $s->getUrl('/hola'));
        $this->assertEquals('http://www.edwin.cl/base/hola', $s->getFullUrl('/hola'));
    }

    /**
     * @throws RelativePathCanNotBeFullException
     * @throws UnregisteredWebDomainException
     */
    public function testFullUrlSimpleException() {
        $this->expectException(RelativePathCanNotBeFullException::class);
        $this->expectExceptionMessage("hola");

        $s = new Context('', '');
        $s->setTargetWebDomain('http://www.edwin.cl');
        $this->assertEquals('hola', $s->getUrl('hola'));
        $s->getFullUrl('hola');
    }


}

