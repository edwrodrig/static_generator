<?php

class PageTest extends \PHPUnit\Framework\TestCase {


function createProvider() {
  return [
  [
    \edwrodrig\static_generator\PageCopy::class,
    ['page_type' => 'copy'],
    'h.php'
  ],
  [
    \edwrodrig\static_generator\PageProc::class,
    ['page_type' => 'process'],
    'h.php'
  ],
  [
    \edwrodrig\static_generator\PagePhp::class,
    [],
    'h.php'
  ],
  [
    \edwrodrig\static_generator\PageTemplateInstance::class,
    ['page_type' => 'template']
    ,
    'h.php'
  ],
  [
    \edwrodrig\static_generator\PageCopy::class,
    [],
    'h.jpg'
  ]


];

}
/**
 * @dataProvider createProvider
 */
function testCreate($expected, $metadata, $input_file) {
  $filename = '/tmp/' . $input_file;
  $file = file_put_contents($filename, "<?php\n/*METADATA\n" . json_encode($metadata) . "\n*/?>");

  $page = edwrodrig\static_generator\Page::create(['absolute_path' => $filename, 'relative_path' => $input_file]);
  $this->assertInstanceOf($expected, $page);
}

}

