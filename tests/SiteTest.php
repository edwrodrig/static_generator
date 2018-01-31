<?php

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


}

