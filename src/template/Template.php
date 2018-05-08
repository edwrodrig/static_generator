<?php

namespace edwrodrig\static_generator\template;


use edwrodrig\static_generator\PagePhp;

class Template
{
    /**
     * @var PagePhp
     */
    protected $page_info;

    public function __construct(PagePhp $page_info) {
        $this->page_info = $page_info;
    }

    public function print() {
        /** @noinspection PhpIncludeInspection */
        include $this->page_info->getSourceAbsolutePath();
    }

    /**
     * Get the template type.
     *
     * This function is useful when you want to classificate by types
     * @return string
     */
    public function getTemplateType() : string {
        return 'base';
    }

    /**
     * Get the relative path of the output of the template.
     *
     * By default is the same as the {@see PagePhp::getRelativePath() source}.
     * @return string
     */
    public function getRelativePath() : string {
        return $this->page_info->getRelativePath();
    }

    /**
     * Get the input file of the content in the file system
     * @return string
     */
    public function getInputAbsolutePath() : string {
        return $this->page_info->getTargetAbsolutePath();
    }

    public function getInfo() : PagePhp {
        return $this->page_info;
    }

    public function getId() : string {
        return basename($this->getRelativePath(), '.php');
    }

}