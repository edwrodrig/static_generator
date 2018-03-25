<?php

namespace edwrodrig\static_generator\cache;


class Cache
{

    /**
     * @var array
     */
    private $index;

    private $cache_hits = [];

    /**
     * @var string
     */
    private $cache_dir;

    public function __construct(string $cache_dir) {
        $this->cache_dir = $cache_dir;
        $this->index = [];

        $this->load_index();
    }

    public function load_index() {
        $filename = $this->get_index_filename();
        if ( file_exists($filename) ) {
            $index_data = file_get_contents($filename);
            if ( $index_data = json_decode($index_data, true) ) {
                $this->index = $index_data;
            }
        }
    }

    public function get_index_filename() : string {
        return $this->cache_dir . '/index.json';
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

    public function cache(CacheEntry $cache)
    {
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

        $this->cache_hits[$cache->get_key()] = 1;

        return sprintf('/contento_images/%s', $cached_image['filename']);
    }

    protected function is_hitted(CacheEntry $entry) {
        return isset($this->cache_hits[$entry->get_key()]);
    }

    protected function clear_cache_entry(CacheEntry $entry) {
        unset($this->cache_hits[$entry->get_key()]);
        $entry->remove();
    }

    public function save_index()
    {
        foreach ($this->index as $id => $entry) {
            if ( $this->is_hitted($entry))
                continue;
            else
                $this->clear_cache_entry($entry);
        }

        file_put_contents(
            $this->get_index_filename(),
            json_encode($this->index, JSON_PRETTY_PRINT)
        );
    }
}
