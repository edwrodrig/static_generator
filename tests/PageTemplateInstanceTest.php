<?php

class TestPageTemplateInstance extends \edwrodrig\static_generator\Template {

public function print() {
  echo $this->metadata->get_data()['name'];
  parent::print();
}

};

class PageTemplateInstanceTest extends \PHPUnit\Framework\TestCase {

function testGenerateString() {

  file_put_contents('/tmp/template_instance_test.tpl.php', <<<EOF
<?php
/*
 @template TestPageTemplateInstance
 @data {
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

