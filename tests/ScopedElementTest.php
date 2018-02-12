<?php

class ScopedElementTest extends \PHPUnit\Framework\TestCase {

function testPrint1() {

  $obj = new class extends edwrodrig\static_generator\ScopedElement {
     function content() {
       echo '@@@';
     }

  };

  $obj->preffix = 'id';
  ob_start();
  $obj->print();
  $this->assertEquals('id', ob_get_clean());

}

function testPrint2() {

  $obj = new class extends edwrodrig\static_generator\ScopedElement {
     function content() {
       echo '@@@ @@@ @@@';
     }

  };

  $obj->preffix = 'id';
  ob_start();
  $obj->print();
  $this->assertEquals('id id id', ob_get_clean());

}


}

