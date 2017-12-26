<?php
namespace edwrodrig\static_generator;

class PageProc extends Page {

public function output() {
  $output = Site::get()->output($this->input_relative_path);
  dirname($output);
}

public function prepare_output() {
  if ( is_null($this->output_absolute_path) ) {
    $output = Site::get()->output($this->input_relative_path);
    $output = dirname($output), 0777, true);
    $this->output_absolute_path = $output;
  }
  
  return parent::prepare_output();
}

public function generate() {
  $this->log(sprintf("Proccessing file [%s]...\n", $this->input_relative_path));

  self::push($this);
  require($this->input_absolute_path);
  self::pop();

  $this->log("File [%s] processed\n", $this->input_relative_path);
  
}

}
