<?php
namespace edwrodrig\static_generator;

class LibJs {

const MODE_FILES = 0;
const MODE_MINIFIED_FILES = 1;
const MODE_GZIPPED_FILES = 2;

public $output = '.';
public $sources = [];

function html() {
  if ( $this->mode == self::MODE_FILES ) {
    foreach ( $this->iterate_sources() as $source ) {
      printf('<script src="/%s"></script>', $this->output . DIRECTORY_SEPARATOR . $source['relative_path']);
    }
  } else if ( $this->mode == self::MODE_MINIFIED_FILES ) {
    printf('<script src="/%s"></script>', $this->output . '.js');    
  } else if ( $this->mode == self::MODE_GZIPPED_FILES ) {
    printf('<script src="/%s"></script>', $this->output . '.js.gz');
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
    $minifier = new \MatthiasMullie\Minify\JS;
    foreach ( $this->iterate_sources() as $source ) {
      $minifier->add($source['absolute_path']);
    }
    
    if ( $this->mode == self::MODE_MINIFIED_FILES ) {
      Page::get()->generate_from_function(
        $this->output . '.js',
        function() use ($minifier) {
          echo $minifier->minify();
        }
      );
    } else if ( $this->mode == self::MODE_GZIPPED_FILES ) {
      Page::get()->generate_from_function(
        $this->output . '.js.gz',
        function() use ($minifier) {
          echo $minifier->gzip();
        }
      );
    }
  }
}

}


