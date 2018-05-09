<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    function testTranslate()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertEquals('es', $s->tr(['es' => 'es', 'en' => 'en']));

        setlocale(LC_ALL, 'en_US.utf-8');
        $this->assertEquals('en', $s->tr(['es' => 'es', 'en' => 'en']));
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
        $s->tr([]);
    }


    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     */
    public function testTranslateNoTranslationDefault()
    {
        $s = new Context('', '');
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertequals('hola', $s->tr([], 'hola'));
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


}

