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


}

