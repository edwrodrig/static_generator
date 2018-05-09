<?php

namespace edwrodrig\static_generator\cache;

use DateTime;

class FileItem implements CacheableItem
{
    private $base_folder;
    protected $filename;
    protected $suffix;
    protected $output_folder = '';
    protected $extension = null;
    protected $salt = null;

    public function __construct(string $base_folder, string $filename, string $suffix = '') {
        $this->base_folder = $base_folder;
        $this->filename = $filename;
        $this->suffix = $suffix;
    }

    public function set_salt() {
        $this->salt = uniqid();
    }

    public function getKey() : string {
        $base_name = self::get_basename($this->filename);

        if ( empty($this->suffix) )
            return $base_name;
        else
            return $base_name . '_' . $this->suffix;
    }

    public function get_source_filename() {
        return $this->base_folder . DIRECTORY_SEPARATOR . $this->filename;
    }

    public function getLastModificationTime() : DateTime {
        $date = new DateTime();
        $date->setTimestamp(filemtime($this->get_source_filename()));
        return $date;
    }

    public function getTargetRelativePath() : string {
        $extension = $this->extension ?? pathinfo($this->filename, PATHINFO_EXTENSION);

        $file = $this->getKey();

        if ( !empty($this->salt) )
            $file .= '_' . $this->salt;

        if ( empty($extension) )
            return $file;
        else
            return $file . '.' . $extension;
    }

    public function generate(CacheManager $cache) {
        copy(
            $this->get_source_filename(),
            $cache->cache_filename($this->getTargetRelativePath())
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