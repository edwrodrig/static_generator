<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:23
 */

namespace edwrodrig\static_generator\cache;

use DateTime;
use JsonSerializable;

class CacheEntry implements JsonSerializable
{
    protected $generation_date;

    protected $cached_file;

    protected $cache_key;
    protected $output_filename;

    public function get_cache_key() : string {
        return $this->cache_key;
    }

    public static function create_from_item(CacheItem $item) {
        $entry = new self;
        $entry->cached_file = $item->get_cached_file();
        $entry->output_filename = $item->get_output_filename();
        $entry->generation_date = new DateTime();
        $entry->cache_key = $item->get_cache_key();
        return $entry;
    }

    public static function create_from_array(array $data) {
        $entry = new self;
        $entry->cached_file = $data['cached_file'];
        $entry->generation_date = new DateTime();
        $entry->generation_date->setTimestamp((int)$data['generation_date']);
        $entry->output_filename = $data['output_filename'];
        $entry->cache_key = $data['cache_key'];
        return $entry;
    }

    public function get_generation_date() : DateTime {
        return $this->generation_date;
    }

    public function get_cached_file() : string {
        return $this->cached_file;
    }

    public function get_output_filename() : string {
        return $this->get_output_filename();
    }

    public function __toString() : string {
        return $this->get_output_filename();
    }

    public function jsonSerialize() {
        return [
            'cache_key' => $this->cache_key,
            'generation_date' => $this->generation_date->getTimestamp(),
            'output_filename' => $this->output_filename,
            'cached_file' => $this->cached_file
        ];
    }
}