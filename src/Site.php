<?php

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\FileData;

class Site implements \IteratorAggregate
{

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

    public function get_base_url() : string {
        return $this->base_url;
    }

    public function set_base_url(string $base_url)
    {
        $this->base_url = trim($base_url ?? '');
        $this->base_url = preg_replace('/\/$/', '', $this->base_url);
    }

    public function get_lang() : string {
        $locale = \setlocale(LC_ALL, "0");
        return substr($locale,0, 2);
    }

    /**
     * @param $translatable
     * @param null $default
     * @return string
     * @throws exception\NoTranslationAvailableException
     */
    public function tr($translatable, $default = null) : string
    {
        if (isset($translatable[$this->get_lang()]))
            return $translatable[$this->get_lang()];
        else if (is_string($default)) {
            return $default;
        } else
            throw new exception\NoTranslationAvailableException($translatable, $this->get_lang());
    }
    /**
     * @return \Generator|FileData[]
     */
    public function getIterator()
    {
        return $this->iterate_item('.');
    }

    /**
     * @param $file
     * @return \Generator|FileData[]
     */
    public function iterate_item($file)
    {
        $input = $this->input($file);
        $file_data = new FileData($file);

        if ( ! $file_data->exists() ) {

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
            yield $file_data;
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
            if ($page = Page::create( )) {
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
                    $this->templates[$template->getTemplateType()][] = $template;
                }
            }
        }

        return $this->templates[$name] ?? [];
    }

    public static function check_locales(...$langs) {
        $available_langs = explode("\n", shell_exec('locale -a'));

        foreach ( $langs as $lang ) {
            echo "Checking lang[$lang]...";
            if (!in_array($lang, $available_langs)) {
                echo "NOT AVAILABLE\n";
                return false;
            } else {
                echo "AVAILABLE\n";
            }
        }
        return true;
    }
}



