<?php

namespace edwrodrig\static_generator;

abstract class ScopedElement {

public $preffix_pattern = '@@@';
public $preffix;

function __construct($preffix = null) {
  if ( empty($preffix) ) {
    $preffix = uniqid();
  }
  $this->preffix = $preffix;
}

function print() {
  ob_start();
  $this->content();
  $content = ob_get_clean();

  $content = str_replace($this->preffix_pattern, $this->preffix, $content);
  echo $content;
}

}

