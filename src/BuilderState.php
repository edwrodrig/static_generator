<?php
namespace ephp\web;

class BuilderState {
 
public static $states = [];

static function get() {
  return self::$states[count(self::$states) - 1];
} 

static function push() {
  $obj = new \stdClass();

  $obj->style_used_selector = [];
  $obj->tag_stack = [];
  $obj->id_seed_t = 0;
  $obj->id_seed_c = 0;
  $obj->define_guards_defs = [];
  $obj->define_guards_elems = [];
  self::$states[] = $obj;
}

static function pop() {
  array_pop(self::$states);
}

}

