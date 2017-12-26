<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$site = new edwrodrig\static_generator\Site;
$site->input_dir = 'files';
$site->output_dir = 'output';

$site->generate();

