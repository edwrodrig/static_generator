<?php
namespace edwrodrig\static_generator;

trait Stack {
 
static public $stack = [];

static function level() {
  return count(self::$stack);
}

function log($str) {
  printf(
    "%s%s",
    str_repeat("  ", self::level()),
    $str
  );
}

static function get() {
  if ( empty(self::$stack) ) {
    self::push();
  }
  return self::$stack[self::level() - 1];
} 

static function push() {
  self::$stack[] = new self;
}

static function pop() {
  array_pop(self::$stack);
}

static function reset() {
  self::$stack = [];
}

}

