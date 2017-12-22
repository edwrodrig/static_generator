<?php
namespace edwrodrig\static_generator;

class Context {

public static $current_builder = null;

static function has_started() {
  return !is_null(self::$current_builder);
}

static function __callStatic($name, $args) {
  if ( !self::has_started() ) throw new \Exception("Error: Build process not initialized");
  return self::$current_builder->{$name}(...$args);
}

}

