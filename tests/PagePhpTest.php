<?php

class PagePhpTest extends \PHPUnit\Framework\TestCase {

function testGenerateString() {

  $page = new edwrodrig\static_generator\PagePhp;
  $page->input_absolute_path = __DIR__ . '/files/test_dir/hola.php';
  $output = $page->generate_string();

  $this->assertEquals("Hola mundo", $output);

}

}

