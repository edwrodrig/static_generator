<?php

class TestPageTemplateInstance {

public function print() {
  echo $this->metadata['name'];
  ($this->content)();
}

};

class PageTemplateInstanceTest extends \PHPUnit\Framework\TestCase {

function testGenerateString() {

  file_put_contents('/tmp/template_instance_test.tpl.php', <<<EOF
<?php
/*METADATA
{
  "template" : "TestPageTemplateInstance",
  "name" : "some_name"
}
*/
echo " Hola";
?> Mundo
EOF
);

  $page = new \edwrodrig\static_generator\PageTemplateInstance;
  $page->input_absolute_path = '/tmp/template_instance_test.tpl.php';
  $output = $page->generate_string();

  $this->assertEquals("some_name Hola Mundo", $output);

}

}

