<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$site = new edwrodrig\static_generator\Site;
$site->input_dir = 'files';
$site->output_dir = 'output';

$js = new edwrodrig\static_generator\LibJs;
$js->mode = $js::MODE_MINIFIED_FILES;
$js->output = 'lib';
$js->sources = [
  __DIR__ . '/js/console.js',
  __DIR__ . '/js/warn.js'
];

$css = new edwrodrig\static_generator\LibCss;
$css->mode = $css::MODE_FILES;
$css->output = 'style';
$css->sources = [
  __DIR__ . '/css/style1.css',
  __DIR__ . '/css/style2.css'
];

$site->globals = [
  'js' => $js,
  'css' => $css
];

$site->generate();

