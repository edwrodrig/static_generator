<?php

namespace edwrodrig\static_generator;

class Utils {

static function html_string($data) {
  return htmlspecialchars(self::ob_safe($data));
}

static function iterate_files($sources) {
  foreach ( $sources as $source ) {
    if ( !file_exists($source)) throw new \Exception('FILE_DOES_NOT_EXISTS');

    if ( is_dir($source) ) {
      foreach ( new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source)) as $file )
      {
        if ( !$file->isFile() ) continue;
        yield $file;
      }
    } else {
      yield new \SplFileInfo($source);
    }
  }

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

static function get_comment_data($filename, $name) {
  $tokens = token_get_all(file_get_contents($filename));
  foreach ( $tokens as $token ) {
    if ( $token[0] !== T_COMMENT ) continue;
    $content = $token[1];
    if ( strpos($content, '/*' . $name ) === 0 ) {
      $content = preg_replace('/^\/\*' . $name . '/', '', $content);
      $content = preg_replace('/\*\/$/', '', $content);
      return $content;
    }
  }
  return null;

}

}
