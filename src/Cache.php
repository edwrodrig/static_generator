<?php

namespace edwrodrig\static_generator;


class Image
{
    static $cache = null;
    static $images = null;
    static $keep = [];
    public $cache_dir = '';

    public function __construct(string $cache_dir) {

    }


    public function load_index() {
        file_get_contents($cache_dir . '/index.json');
    }

    static function cache_filename()
    {
        return \ephp\web\Context::cache('images_cache.json', true);
    }

    static function load_cache()
    {
        if (is_null(self::$cache)) {
            self::$cache = [];
            $cache_file = self::cache_filename();
            self::$loaded_cache = $cache_file;
            if (file_exists($cache_file)) {
                self::$cache = json_decode(file_get_contents($cache_file), true) ?? [];
            }
        }
    }

    static function href($id, $options = [])
    {
        if (empty($id)) return "";

        $data = [
            'id' => $id
        ];
        $name_parts = [$id];
        if (isset($options['type'])) {
            $data['type'] = $options['type'];
            $name_parts[] = $options['type'];
        }
        $dim = ['w' => '', 'h' => ''];
        if (isset($options['w'])) {
            $dim['w'] = $options['w'];
        }

        if (isset($options['h'])) {
            $dim['h'] = $options['h'];
        }
        $dim_str = implode('x', $dim);
        if ($dim_str != 'x')
            $name_parts[] = $dim_str;
        $data['tag'] = implode('_', $name_parts);
        $data['dim'] = $dim;
        return self::cache_image($data);
    }

    static function cache_image($image)
    {
        self::load_cache();
        $source = self::$images[$image['id']];
        $cached_image;
        if (!isset(self::$cache[$image['tag']]) || self::$cache[$image['tag']]['time'] < $source['time']) {
            $cached_image = [
                'time' => $source['time'],
                'utime' => time(),
                'ext' => pathinfo($source['filename'], PATHINFO_EXTENSION)
            ];

            $cached_image['filename'] = sprintf('%s_t%s.%s', $image['tag'], $cached_image['utime'], $cached_image['ext']);
            $cached_image['output'] = \ephp\web\Context::cache('images' . DIRECTORY_SEPARATOR . $cached_image['filename'], true);
            \ephp\web\Context::log("Processing image[%s]", $cached_image['filename']);
            if ($cached_image['ext'] == 'png')
                \ephp\Image::optimize_png($source['filename'], $cached_image['output'], $image['dim']);
            else if ($cached_image['ext'] == 'jpg')
                \ephp\Image::optimize_jpg($source['filename'], $cached_image['output'], $image['dim']);
            self::$cache[$image['tag']] = $cached_image;
        } else {
            $cached_image = self::$cache[$image['tag']];
        }
        self::$keep[$cached_image['filename']] = 1;

        return sprintf('/contento_images/%s', $cached_image['filename']);
    }

    static function link_images()
    {
        symlink(\ephp\web\Context::cache('images'), \ephp\web\Context::output('contento_images'));
    }

    static function save_cache()
    {
        if (is_null(self::$cache)) return;
        foreach (self::$cache as $id => $data) {
            if (isset(self::$keep[$data['filename']])) {
                continue;
            }
            printf("Image not longer used[%s]", $data['filename']);
            unlink($data['output']);
            echo "...DONE\n";
            unset(self::$cache[$id]);
        }
        file_put_contents(self::$loaded_cache, json_encode(self::$cache, JSON_PRETTY_PRINT));
    }
}
