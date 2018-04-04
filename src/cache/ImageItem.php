<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-04-18
 * Time: 14:38
 */

namespace ecodim\site\data;

use edwrodrig\static_generator\cache\Cache;
use edwrodrig\static_generator\cache\FileItem;
use ecodim\site\Site;

class ImageItem extends FileItem
{
    private $width;
    private $height;
    public $svg_factor = 1;
    protected $mode = 'copy';
    protected $last_cache_used;

    public function __construct(string $base_folder, string $file, string $suffix = '')
    {
        parent::__construct($base_folder, $file, $suffix);

        if (pathinfo($file, PATHINFO_EXTENSION) == 'svg')
            $this->extension = 'png';

    }

    public function set_svg_factor(int $factor) {
        $this->svg_factor = $factor;
    }

    public function set_contain(int $width, int $height) {
        $this->width = $width;
        $this->height = $height;
        $this->suffix = $width . 'x' . $height . '_contain';
        $this->mode = 'contain';
    }

    public function set_cover(int $width, int $height) {
        $this->width = $width;
        $this->height = $height;
        $this->suffix = $width . 'x' . $height . '_cover';
        $this->mode = 'cover';
    }

    public function cache_generate(Cache $cache) {
        $this->last_cache_used = $cache;

        $img = \edwrodrig\image\Image::optimize($this->get_source_filename($this->filename), $this->svg_factor);
        if ( $this->mode == 'contain' ) {
            $img = \edwrodrig\image\Image::contain($img, $this->width, $this->height);

        } else if ( $this->mode == 'cover' ) {
            $img = \edwrodrig\image\Image::cover($img, $this->width, $this->height);
        }
        $img->writeImage($cache->cache_filename($this->get_cached_file()));
        $cache->update_cache($this);
    }

}
