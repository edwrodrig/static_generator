<?php
namespace edwrodrig\static_generator;

class Site {

use Stack;

public $input_dir = 'files';
public $output_dir = 'output';
public $cache_dir = 'cache';

private $base_url = '';

public function url(string $url) : string {
  $url = trim($url);
  foreach ( ['//', 'http://', 'https://'] as $protocol ) {
    if ( strpos($url, $protocol) === 0 ) return $url;
  }
  if ( strpos($url, '/') === 0 ) return $this->base_url . $url;
  return $url;
}

private function input(string $file) : string {
  return realpath($this->input_dir . DIRECTORY_SEPARATOR . $file);
}

public function output(string $file, bool $prepare = false) : string {
  $full_filename = $this->output_dir . DIRECTORY_SEPARATOR . $file;
  if ( $prepare ) @mkdir(dirname($full_filename), 0777, true);
  return $full_filename;
}

public function cache(string $file, bool $prepare = false) : string {
  $full_filename = $this->cache_dir . DIRECTORY_SEPARATOR . $file;
  if ( $prepare ) @mkdir(dirname($full_filename), 0777, true);
  return $full_filename;
}

private function set_base_url($base_url) {
  $this->base_url = trim($base_url ?? '');
  $this->base_url = preg_replace('/\/$/', '', $this->base_url);
}
/*
public function file_process(string $output, callable $function) {
  printf("%sRendering file [%s]...", str_repeat("  ", $this->level), $output);
  BuilderState::push();
  $this->current_output = $output;

  $content = Utils::ob_safe($function);

  file_put_contents($this->output($output, true), $content);
  BuilderState::pop();
  printf("  DONE\n");
}
*/
public function __get($name) {
  if ( $name === 'base_url' )
    return $this->base_url;
}

public function __set($name, $value) {
  if ( $name === 'base_url' ) {
    $this->set_base_url($value);
  }
}

public function iterate_dir($file) {
  $input = $this->input($file);

  if ( !file_exists($input) ) {

  } else if ( is_dir($input) ) {
    foreach ( scandir($input) as $index => $dir_file ) {
      if ( $key < 2 ) continue;
      yield from $this->iterate_dir($file . DIRECTORY_SEPARATOR . $dir_file);
    }
  } else {
    yield [
      'input' => $file;
    ];
  }
}
/*
public function process($file) {
  $indent = str_repeat("  ", $this->level);


  $input = $this->input($file);

  if ( !file_exists($input) ) {
    printf("%sFile not exists [%s]...SKIPPED\n", $indent, $file);
  } else if ( is_dir($input) ) {
    printf("%sProcessing directory [%s]...\n", $indent, $file);
    $this->level++;
    foreach ( scandir($input) as $key => $dir_file) {
      if ( $key < 2 ) continue;
      if ( $file === '.' ) $this($dir_file);
      else $this($file . DIRECTORY_SEPARATOR . $dir_file);
    }
    $this->level--;
    printf("%sDirectory [%s] completed\n", $indent, $file);
  } else {
    if ( preg_match('/\.copy.php$/', $input) ) {
      printf("%sCopying file [%s]...", $indent, $file);
      passthru(sprintf("cp %s %s", $input, $this->output(preg_replace('/\.copy.php$/', '.php', $file), true)));
      printf("  DONE\n");
    } else if ( preg_match( '/\.proc.php$/', $input ) ) {
      printf("%sProcessing file [%s]...\n", $indent, $file);
      $this->level++;
      require($input);
      $this->level--;
      printf("%sFile [%s] processed\n", $indent, $file);
    } else if ( preg_match( '/\.php$/', $input) ) {
      $this->file_process(preg_replace('/\.php$/', '', $file), function() use ($input) { require($input); });
    } else if ( !preg_match('/\.swp$/', $input) ) {
      printf("%sCopying file [%s]...", $indent, $file);
      passthru(sprintf("cp %s %s", $input, $this->output($file, true)));
      printf("  DONE\n");
    }
  }
}

public function finalize() {
  $previous_input_dir = $this->input_dir;
  foreach ( ProcFile::$files as $file => $input_dir ) {
    $this->input_dir = $input_dir;
    $this($file);
  }
  $this->input_dir = $previous_input_dir;

  ProcFile::reset();

  Context::$current_builder = null;
}
*/
}

