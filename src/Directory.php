<?php
namespace edwrodrig\static_generator;

class Directory {

public function file_process(string $output, callable $function) {
  printf("%sRendering file [%s]...", str_repeat("  ", $this->level), $output);
  BuilderState::push();
  $this->current_output = $output;

  $content = Utils::ob_safe($function);

  file_put_contents($this->output($output, true), $content);
  BuilderState::pop();
  printf("  DONE\n");
}

public function __invoke(...$files) {

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

