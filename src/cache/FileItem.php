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
    private $filename;

    public function __construct(string $base_folder, string $filename) {
        $this->base_folder = $base_folder;
        $this->filename = $filename;
    }

    public function get_cache_key() : string {
        return $this->filename;
    }

    public function get_last_modification_time() : DateTime {
        $date = new DateTime();
        $date->setTimestamp(filemtime($this->base_folder . DIRECTORY_SEPARATOR . $this->filename));
        return $date;
    }

    public function get_cached_file() : string {
        return $this->filename;
    }

    public function cache_generate(Cache $cache) {
        copy(
            $this->base_folder . DIRECTORY_SEPARATOR . $this->filename,
            $cache->absolute_filename($this->filename)
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