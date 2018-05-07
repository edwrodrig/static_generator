<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\util\FileData;

class PageFunctionTest extends \PHPUnit\Framework\TestCase {

function testGenerateString() {

  $page = new \edwrodrig\static_generator\PageFunction(
      new FileData(0, 'out'),
      '');
  $page->function = function() { echo "Hola mundo"; };
  $output = $page->generate_string();

  $this->assertEquals("Hola mundo", $output);

}

}

