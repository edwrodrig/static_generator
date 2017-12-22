<?php

class Entity extends \edwrodrig\contento\data\Entity {

function some() {
  return ($this['name'] ?? '') . '_some' ;
}

}

class EntityTest extends \PHPUnit\Framework\TestCase {

function testAtrib() {
  $e = new Entity(['name' => 'edwin', 'surname' => 'rodriguez']);

  $this->assertEquals('rodriguez', $e['surname']);
  $this->assertEquals('edwin_some', $e['some']);
  $this->assertEquals('edwin_some', $e->some());
}

}

