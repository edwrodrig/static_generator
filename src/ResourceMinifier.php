<?php
namespace edwrodrig\static_generator;

class ResourceMinifier {

public $sources = [];

function iterate_sources() {
  foreach ( Utils::iterate_files($this->sources as $source ) {
    $filename = $source->getPathname();
    $ext = $source->getExtension();
    if ( in_array($ext, ['css', 'js']) ) {
      yield [
        'absolute_path' => $filename,
        'type' =>  $ext
      ];
    }
  }
}

function js() {
  $minifier = new \MatthiasMullie\Minify\JS;

  foreach ( $this->iterate_sources() as $source ) {
    if ( $source['type'] !== 'js' ) continue;
    $minifier->add($source['absolute_path']);
  }

  return $minifier;    
}

function css() {
  $minifier = new \MatthiasMullie\Minify\CSS;

  foreach ( $this->iterate_sources() as $source ) {
    if ( $source['type'] !== 'css' ) continue;
    $minifier->add($source['absolute_path']);
  }

  return $minifier;

}

}


