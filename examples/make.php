<?php

include_once __DIR__ . '/../vendor/autoload.php';

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


$context = new Context(__DIR__ . '/files', __DIR__ . '/output');

setlocale(LC_ALL, 'es_CL.utf-8');

$context->clearTarget();

foreach ( \edwrodrig\static_generator\util\PageFileFactory::createPages($context) as $page ) {
    $page->generate();
}


