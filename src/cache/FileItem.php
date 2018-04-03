<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-04-18
 * Time: 11:31
 */

namespace edwrodrig\static_generator\cache;

use DateTime;

class FileItem implements CacheItem
{
    private $base_folder;
    protected $filename;
    protected $suffix;

    public function __construct(string $base_folder, string $filename, string $suffix = '') {
        $this->base_folder = $base_folder;
        $this->filename = $filename;
        $this->suffix = $suffix;
    }

    public function get_cache_key() : string {
        $base_name = self::get_basename($this->filename);

        if ( empty($this->suffix) )
            return $base_name;
        else
            return $base_name . '_' . $this->suffix;
    }

    public function get_source_filename() {
        return $this->base_folder . DIRECTORY_SEPARATOR . $this->filename;
    }

    public function get_last_modification_time() : DateTime {
        $date = new DateTime();
        $date->setTimestamp(filemtime($this->get_source_filename()));
        return $date;
    }

    public function get_cached_file() : string {
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

        if ( empty($extension) )
            return $this->get_cache_key();
        else
            return $this->get_cache_key() . '.' . $extension;
    }

    public function cache_generate(Cache $cache) {
        copy(
            $this->get_source_filename(),
            $cache->cache_filename($this->get_cached_file())
        );
    }

    public static function get_basename(string $filename) : string {
        $info = pathinfo($filename);
        if ( $info['dirname'] == '.' )
            $info['dirname'] = '';
        else
            $info['dirname'] = $info['dirname'] . DIRECTORY_SEPARATOR;

        return $info['dirname'] . $info['filename'];
    }
}