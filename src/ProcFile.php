<?php

namespace ephp\web;

class ProcFile {

static $files = [];

static function register($input_dir, $file) {
  self::$files[$file] = $input_dir;
}

static function reset() {
  self::$files = [];
}

}
