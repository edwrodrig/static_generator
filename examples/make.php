<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$site = new edwrodrig\static_generator\Site;
$site->input_dir = 'files';
$site->output_dir = 'output';

$minifier = new edwrodrig\static_generator\ResourceMinifier;
$minifier->sources = [
  __DIR__ . '/js',
  __DIR__ . '/css'
];

$minifier->js()->minify(__DIR__ . '/files/lib.js');
$minifier->css()->minify(__DIR__ . '/files/style.css');

$site->regenerate();

