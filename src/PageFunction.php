<?php
namespace edwrodrig\static_generator;

class PageFunction extends Page {

public $function;

public function generate() {
  $output = $this->prepare_output();

  $this->log(sprintf("Rendering file [%s]...", $this->output_relative_path));

  file_put_contents($output, $this->generate_string());

  $this->log("DONE\n");
}

public function generate_string() {
  self::push($this);

  $content = Utils::ob_safe($this->function);

  self::pop();
  return $content; 
}

}
