<?php

require_once(__DIR__ . '/../vendor/autoload.php');

class Template extends \edwrodrig\static_generator\Template {

    /**
     * @return mixed
     * @throws \edwrodrig\static_generator\exception\WrongDataException
     */
    public function get_title() {
        return $this->metadata->get_data()['title'];
    }

    public function get_name() : string {
        return 'template';
    }

};


$site = new edwrodrig\static_generator\Site;
$site->input_dir = 'files';
$site->output_dir = 'output';

$minifier = new edwrodrig\static_generator\ResourceMinifier;
$minifier->sources = [
  __DIR__ . '/js'
];

$minifier->js()->minify(__DIR__ . '/files/lib.js');

$site->regenerate();

