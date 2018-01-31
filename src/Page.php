<?php
namespace edwrodrig\static_generator;

class Page {

public $input_relative_path;
public $input_absolute_path;
public $output_relative_path = null;
public $output_absolute_path = null;
public $level = 0;

use Stack;

public function set_data($data) {
  $this->input_absolute_path = $data['absolute_path'] ?? null;
  $this->input_relative_path = $data['relative_path'] ?? null;

}

public function current_url() {
  return Site::get()->url($this->input_relative_path);
}

public function prepare_output() {
  $this->output_relative_path  = preg_replace('/^\.\//', '', $this->output_relative_path);
  $this->output_absolute_path = Site::get()->output($this->output_relative_path);
  @mkdir(dirname($this->output_absolute_path), 0777, true);
  return $this->output_absolute_path;
}

static public function create($data) {
  $path = $data['relative_path'];

  if ( preg_match('/\.copy.php$/', $path) ) {
    $page = new PageCopy();
    $page->set_data($data);
    return $page;

  } else if ( preg_match('/\.proc.php$/', $path) ) {
    $page = new PageProc();
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
  } else {
    return null;
  }
}

}

