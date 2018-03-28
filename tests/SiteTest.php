<?php

use edwrodrig\static_generator\Site;

class SiteTest extends \PHPUnit\Framework\TestCase {

function testIterateItem() {

  $site = new edwrodrig\static_generator\Site;
  $site->input_dir = __DIR__ . '/files/test_dir';

  $files = iterator_to_array($site->iterate_item('.'));
  $this->assertArraySubset([
    [
      'level' => 0,
      'relative_path' => './hola.html',
    ],
    [
      'level' => 0,
      'relative_path' => './hola.php'
    ],
    [
      'level' => 1,
      'relative_path' => './sub/chao.html'
    ]
  ], $files);
}

function testIteratorAggregate() {
  $site = new edwrodrig\static_generator\Site;
  $site->input_dir = __DIR__ . '/files/test_dir';
  
  $files = iterator_to_array($site);

  $this->assertArraySubset([
    [
      'level' => 0,
      'relative_path' => './hola.html',
    ],
    [
      'level' => 0,
      'relative_path' => './hola.php'
    ],
    [
      'level' => 1,
      'relative_path' => './sub/chao.html'
    ]
  ], $files);


}

function testInheritSite() {
  $s = new class extends edwrodrig\static_generator\Site {
    function __construct() {
      $this->cache_dir = 'cache_2';
    }
  };

  $this->assertEquals('files', $s->input_dir);
  $this->assertEquals('cache_2', $s->cache_dir);
}

function testTranslate() {
    $s = new Site;
    $s->set_lang('es');
    $this->assertEquals('es', $s->tr(['es' => 'es', 'en' => 'en']));

    $s->set_lang('en');
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
        $s->set_lang('es');
        $s->tr([]);
    }


    public function testTranslateNoTranslationDefault() {
        $s = new Site;
        $s->set_lang('es');
        $this->assertequals('hola', $s->tr([], 'hola'));
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     */
    public function testTranslateNoTranslation2() {
        $s = new Site;
        $s->set_lang('es');
        $s->tr(null);
    }

    /**
     * @throws \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedException \edwrodrig\static_generator\exception\NoTranslationAvailableException
     * @expectedExceptionMessage [][es]
     */
    public function testTranslateNoTranslation3() {
        $s = new Site;
        $s->set_lang('es');
        $s->tr('');
    }




}

