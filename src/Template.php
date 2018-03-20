<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 19-03-18
 * Time: 14:32
 */

namespace edwrodrig\static_generator;


class Template
{
    protected $filename;
    protected $metadata;

    public function __construct(string $filename, PageMetadata $metadata) {
        $this->filename = $filename;
        $this->metadata = $metadata;
    }

    public function get_metadata() : PageMetadata {
        return $this->metadata;
    }

    public function print() {
        /** @noinspection PhpIncludeInspection */
        include $this->filename;
    }

    public function get_name() : string {
        return 'base';
    }

}