<?php
namespace edwrodrig\static_generator;

class ResourceMinifier {

public $dirs = [];

function iterate_sources() {
  foreach ( $this->dirs as $dir )
    foreach ( new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file ) {
      if ( !$file->isFile() ) continue;
      $filename = $file->getPathname();
      $ext = $file->getExtension();
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


