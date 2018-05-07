<?php

namespace edwrodrig\static_generator;


class Template
{
    protected $page_info;

    public function __construct(PagePhp $page_info) {
        $this->page_info = $page_info;
    }

    public function print() {
        /** @noinspection PhpIncludeInspection */
        include $this->page_info->getInput()->getAbsolutePath();
    }

    public function getTemplateType() : string {
        return 'base';
    }

    public function getId() : string {
        return basename($this->getRelativePath()->ilename, '.php');
    }

}