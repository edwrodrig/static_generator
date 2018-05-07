<?php

use edwrodrig\static_generator\cache\Cache;

require_once(__DIR__ . '/../vendor/autoload.php');

class Template extends \edwrodrig\static_generator\template\Template {

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
$site->input_dir = __DIR__ . '/files';
$site->output_dir = __DIR__ . '/output';
$site->cache_dir = __DIR__ . '/cache';

setlocale(LC_ALL, 'es_CL.utf-8');

$minifier = new \edwrodrig\static_generator\util\ResourceMinifier;
$minifier->sources = [
  __DIR__ . '/js'
];

$minifier->js()->minify(__DIR__ . '/files/lib.js');

$site->globals['cache'] = new Cache($site->cache('image'));

$site->regenerate();

$site->globals['cache']->save_index();
$site->globals['cache']->link_cached('.', 'cached');

