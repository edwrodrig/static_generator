<?php

class UtilsTest extends \PHPUnit\Framework\TestCase {

function testGetCommentData() {
  file_put_contents('/tmp/test_include', <<<EOF
<?php
/*METADATA
{
  "title" : "hola",
  "data" : "como te va"
}
*/
echo "hola";
EOF
);

  $data = edwrodrig\static_generator\Utils::get_comment_data('/tmp/test_include', 'METADATA');
  $data = json_decode(trim($data), true);
  $this->assertEquals(['title' => 'hola', 'data' => 'como te va'], $data);

}

}

