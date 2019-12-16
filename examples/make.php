<?php

include_once __DIR__ . '/../vendor/autoload.php';

use edwrodrig\file_cache\CacheManager;
use edwrodrig\static_generator\Context;

class Template extends \edwrodrig\static_generator\template\Template {

    /**
     * @return mixed
     */
    public function getTitle() : string {
        return $this->getData()['title'];
    }

    public function getTemplateType(): string
    {
        return 'custom_template';
    }
};


$cache  = new CacheManager(__DIR__ . '/cache/images');
    $cache->setTargetWebPath('cache/images');

$context = new Context(__DIR__ . '/files', __DIR__ . '/output/es');
$context->registerCache($cache);
$context->setTargetWebPath('es');
setlocale(LC_ALL, 'es_CL.utf-8');

$context->generate();


$context = new Context(__DIR__ . '/files', __DIR__ . '/output/en');
$context->registerCache($cache);
$context->setTargetWebPath('en');
setlocale(LC_ALL, 'en_US.utf-8');

$context->generate();

$cache->save();





