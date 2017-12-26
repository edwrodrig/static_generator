<?php
namespace edwrodrig\static_generator;

class PageProc extends Page {

public function generate() {
  $this->log(sprintf("Proccessing file [%s]...\n", $this->input_relative_path));

  self::push($this);
  require($this->input_absolute_path);
  self::pop();

  $this->log(sprintf("File [%s] processed\n", $this->input_relative_path));
  
}

public function generate_from_function($output, $function) {
  $page = new PageFunction;
  $page->output_relative_path = $output;
  $page->function = $function;

  $page->generate();
}


}