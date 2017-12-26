<?php

class PageTest extends \PHPUnit\Framework\TestCase {


function createProvider() {
  return [
  [
    \edwrodrig\static_generator\PageCopy::class,
    'h.copy.php'
  ],
  [
    \edwrodrig\static_generator\PageProc::class,
    'h.proc.php'
  ],
  [
    \edwrodrig\static_generator\PagePhp::class,
    'h.php'
  ],
  [
    \edwrodrig\static_generator\PageCopy::class,
    'h.jpg'
  ]


];

}
/**
 * @dataProvider createProvider
 */
function testCreate($expected, $input_file) {
  $page = edwrodrig\static_generator\Page::create(['relative_path' => $input_file]);
  $this->assertInstanceOf($expected, $page);
}

}

