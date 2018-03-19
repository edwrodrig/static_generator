<?php

namespace edwrodrig\static_generator;

class Site implements \IteratorAggregate
{

    use Stack;

    public $input_dir = 'files';
    public $output_dir = 'output';
    public $cache_dir = 'cache';

    private $base_url = '';
    private $templates = null;
    public $globals = [];

    public function url(string $url): string
    {
        $url = trim($url);
        foreach (['//', 'http://', 'https://'] as $protocol) {
            if (strpos($url, $protocol) === 0) return $url;
        }
        if (strpos($url, '/') === 0) return $this->base_url . $url;
        return $url;
    }

    private function input(string $file): string
    {
        return realpath($this->input_dir . DIRECTORY_SEPARATOR . $file);
    }

    public function output(string $file): string
    {
        $full_filename = $this->output_dir . DIRECTORY_SEPARATOR . $file;
        return $full_filename;
    }

    public function cache(string $file): string
    {
        $full_filename = $this->cache_dir . DIRECTORY_SEPARATOR . $file;
        return $full_filename;
    }

    private function set_base_url($base_url)
    {
        $this->base_url = trim($base_url ?? '');
        $this->base_url = preg_replace('/\/$/', '', $this->base_url);
    }

    public function __get($name)
    {
        if ($name === 'base_url')
            return $this->base_url;
    }

    public function __set($name, $value)
    {
        if ($name === 'base_url') {
            $this->set_base_url($value);
        }
    }

    public function getIterator()
    {
        return $this->iterate_item('.');
    }

    public function iterate_item($file)
    {
        $input = $this->input($file);

        if (!file_exists($input)) {

        } else if (is_dir($input)) {
            foreach (scandir($input) as $index => $dir_file) {
                if ($dir_file == '.') continue;
                if ($dir_file == '..') continue;
                foreach ($this->iterate_item($file . DIRECTORY_SEPARATOR . $dir_file) as $item) {
                    if ($file != '.') $item['level'] += 1;
                    yield $item;
                }
            }
        } else {
            yield [
                'level' => 0,
                'relative_path' => $file,
                'absolute_path' => $input
            ];
        }
    }

    public function regenerate()
    {
        printf("Clearing output dir [%s]\n", $this->output_dir);
        passthru(sprintf('rm -rf %s', $this->output_dir));
        $this->templates = null;
        $this->generate();
    }

    public function generate()
    {
        self::push($this);
        foreach ($this as $file_data) {
            if ($page = Page::create($file_data)) {
                $page->generate();
            }
        }
        self::pop();
    }

    public function get_templates(string $name) : array {
        if ( is_null($this->templates) ) {
            $this->templates = [];
            foreach ($this as $file_data) {
                if ($template = Page::instance_template($file_data)) {
                    $this->templates[$template->get_name()][] = $template;
                }
            }
        }

        return $this->templates[$name] ?? [];
    }



}

