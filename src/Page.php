<?php
namespace edwrodrig\static_generator;

class Page {

public $input_relative_path;
public $input_absolute_path;
public $output_absolute_path = null;
public $level = 0;

use Stack;

public function set_data($data) {
  $this->input_absolute_path = $data['input_absolute_path'] ?? null;
  $this->input_relative_path = $data['input_relative_path'] ?? null;

}

public function prepare_output() {
  @mkdir(dirname($this->output_absolute_path), 0777, true);
  return $this->output_absolute_path;
}

public function create($data) {
  $path = $data['relative_path'];

  if ( preg_match('/\.copy.php$/', $path) ) {
    $page = new PageCopy();
    $page->set_data($data);
    return $page;

  } else if ( preg_match('/\.proc.php$/', $path) ) {
    $page->set_data($data);
    return $page;

  } else if ( preg_match('/\.php$/', $path) ) {
    $page = new PagePhp();
    $page->set_data($data);
    return $page;

  } else if ( !preg_match( '/\.swp$/', $path ) ) {
    $page = new PageCopy();
    $page->set_data($data);
    return $page;
  }
}

public function __invoke(...$files) {
  $this->init();

  $indent = str_repeat("  ", $this->level);
  foreach ( $files as $file) {
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

}

