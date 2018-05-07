<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Site;

class SiteTest extends \PHPUnit\Framework\TestCase {

    function testInheritSite() {
      $s = new class extends \edwrodrig\static_generator\Site {
        function __construct() {
          $this->cache_dir = 'cache_2';
        }
      };

      $this->assertEquals('files', $s->input_dir);
      $this->assertEquals('cache_2', $s->cache_dir);
    }

    function testTranslate() {
        $s = new Site;
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertEquals('es', $s->tr(['es' => 'es', 'en' => 'en']));

        setlocale(LC_ALL, 'en_US.utf-8');
        $this->assertEquals('en', $s->tr(['es' => 'es', 'en' => 'en']));
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [Array
     * (
     * )
     * ][es]
     */
    public function testTranslateNoTranslation() {
        $s = new Site;
        setlocale(LC_ALL, 'es_CL.utf-8');
        $s->tr([]);
    }


    public function testTranslateNoTranslationDefault() {
        $s = new Site;
        setlocale(LC_ALL, 'es_CL.utf-8');
        $this->assertequals('hola', $s->tr([], 'hola'));
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     */
    public function testTranslateNoTranslation2() {
        $s = new Site;
        setlocale(LC_ALL, 'es_CL.utf-8');
        $s->tr(null);
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     */
    public function testTranslateNoTranslation3() {
        $s = new Site;
        setlocale(LC_ALL, 'es_CL.utf-8');
        $s->tr('');
    }




}

