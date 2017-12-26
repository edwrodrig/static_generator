<?php
namespace edwrodrig\static_generator;

class PageCopy extends Page {

public function prepare_output() {
  if ( is_null($this->output_absolute_path) ) {
    $output = Site::get()->output($this->input_relative_path);
    $output = preg_replace('/\.copy.php$/', '.php', $output);
    $this->output_absolute_path = $output;
  }

  return parent::prepare_output();
}

public function generate() {
  $output = $this->prepare_output();

  $this->log(sprintf("Copying file [%s]...", $this->input_relative_path);

  $command = sprintf("cp %s %s", $this->input_absolute_path, $output);
  passthru($command);

  $this->log("DONE\n");
}

}
