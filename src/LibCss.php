<?php
namespace edwrodrig\static_generator;

class LibCss {

const MODE_FILES = 0;
const MODE_MINIFIED_FILES = 1;
const MODE_GZIPPED_FILES = 2;

public $output = '.';
public $sources = [];

function html() {
  if ( $this->mode == self::MODE_FILES ) {
    foreach ( $this->iterate_sources() as $source ) {
      printf('<link rel="stylesheet" type="text/css" href="/%s">', $this->output . DIRECTORY_SEPARATOR . $source['relative_path']);
    }
  } else if ( $this->mode == self::MODE_MINIFIED_FILES ) {
    printf('<link rel="stylesheet" type="text/css" href="/%s">', $this->output . '.css');    
  } else if ( $this->mode == self::MODE_GZIPPED_FILES ) {
    printf('<link rel="stylesheet" type="text/css" href="/%s">', $this->output . '.css.gz');
  }
}

function iterate_sources() {
  foreach ( $this->sources as $source ) {
    yield [
      'absolute_path' => $source,
      'relative_path' => basename($source)
    ];
  }
}

function generate() {
  if ( $this->mode == self::MODE_FILES ) {
    foreach ( $this->iterate_sources() as $source ) {
      Page::get()->generate_from_function(
        $this->output . DIRECTORY_SEPARATOR . $source['relative_path'],
        function() use ($source) {
          echo file_get_contents($source['absolute_path']);
        }
      );
    }
  }
  else {
    $minifier = new \MatthiasMullie\Minify\CSS;
    foreach ( $this->iterate_sources() as $source ) {
      $minifier->add($source['absolute_path']);
    }
    
    if ( $this->mode == self::MODE_MINIFIED_FILES ) {
      Page::get()->generate_from_function(
        $this->output . '.css',
        function() use ($minifier) {
          echo $minifier->minify();
        }
      );
    } else if ( $this->mode == self::MODE_GZIPPED_FILES ) {
      Page::get()->generate_from_function(
        $this->output . '.css',
        function() use ($minifier) {
          echo $minifier->gzip();
        }
      );
    }
  }
}

}


