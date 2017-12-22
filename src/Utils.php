<?php

namespace edwrodrig\static_generator;

class Utils {

static function html_string($data) {
  return htmlspecialchars(self::ob_safe($data));
}

static function ob_safe($content) {
  if ( !is_callable($content) ) {
    return strval($content);
  }

  $level = ob_get_level();
  try {
    ob_start();
      $content();
    return ob_get_clean();
  } catch ( \Exception $e ) {
    while ( ob_get_level() > $level ) ob_get_clean();
    throw $e;
  }
}


}
