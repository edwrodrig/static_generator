<?php

class PageFunctionTest extends \PHPUnit\Framework\TestCase {

function testGenerateString() {

  $page = new edwrodrig\static_generator\PageFunction;
  $page->function = function() { echo "Hola mundo"; };
  $output = $page->generate_string();

  $this->assertEquals("Hola mundo", $output);

}

}

