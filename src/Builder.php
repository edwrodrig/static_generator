<?php
namespace edwrodrig\static_generator;

class Builder {

public $input_dir = 'files';
public $output_dir = 'output';
public $cache_dir = 'cache';

private $level = 0;

private $current_output;
private $base_url = '';

public function url(string $url) : string {
  $url = trim($url);
  foreach ( ['//', 'http://', 'https://'] as $protocol ) {
    if ( strpos($url, $protocol) === 0 ) return $url;
  }
  if ( strpos($url, '/') === 0 ) return $this->base_url . $url;
  return $url;
}

public function current_url() : string {
  $url = trim($this->current_output);
  if ( strpos($url, '/') === 0 ) return $url;
  else return '/' . $url;
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


private function init() {
  if ( Context::has_started() ) return;
  register_shutdown_function(function() { while(ob_get_clean()){;} });
?>
#***********************#
#                       #
#     EPHP  BUILDER     #
#    Edwin Rodriguez    #
#                       #
#***********************#

<?php
  if ( !file_exists($this->input_dir) ) {
    printf("Input directory [%s] does not exists\n", $this->input_dir);
    exit;
  }
  printf("Input directory [%s] found\n", $this->input_dir);

  if ( file_exists($this->output_dir) ) {
    passthru(sprintf('rm -rf %s', $this->output_dir));
  }
  printf("Cache directory [%s] prepared\n", $this->cache_dir);

  printf("Output directory [%s] prepared\n", $this->output_dir);

  printf("Base url set to [%s]\n", $this->base_url);

  Context::$current_builder = $this;

}

private function set_base_url($base_url) {
  $this->base_url = trim($base_url ?? '');
  $this->base_url = preg_replace('/\/$/', '', $this->base_url);
}

public function file_process(string $output, callable $function) {
  printf("%sRendering file [%s]...", str_repeat("  ", $this->level), $output);
  BuilderState::push();
  $this->current_output = $output;

  $content = Utils::ob_safe($function);

  file_put_contents($this->output($output, true), $content);
  BuilderState::pop();
  printf("  DONE\n");
}

public function __get($name) {
  if ( $name === 'base_url' )
    return $this->base_url;
}

public function __set($name, $value) {
  if ( $name === 'base_url' ) {
    $this->set_base_url($value);
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

public function log(...$args) {
  $indent = str_repeat("  ", $this->level + 1);
  fprintf(STDOUT, "\n" . $indent . sprintf(...$args));
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

