<?php
namespace edwrodrig\static_generator;

class PagePhp extends Page {

public function prepare_output() {
  if ( is_null($this->output_absolute_path) ) {
    $output = Site::get()->output($this->input_relative_path);
    $output = preg_replace('/\.php$/', '', $output);
    $this->output_absolute_path = $output;
  }
  return parent::prepare_output();
}

public function generate() {
  $output = $this->prepare_output();

  $this->log(sprintf("Rendering file [%s]...", $output));
  file_put_contents($output, $this->generate_string());
  $this->log("DONE\n");
}

public function generate_string() {
  $php_file = $this->input_absolute_path;
  self::push($this);

  $content = Utils::ob_safe(function() use($php_file) {
    require($php_file);
  });

  self::pop();
  return $content; 
}

}
