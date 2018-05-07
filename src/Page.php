<?php

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\FileData;

class Page
{
    /**
     * @var FileData
     */
    public $input_file_data;

    /**
     * @var string
     */
    private $output_base_dir = null;

    use Stack;

    public function __construct(FileData $data, string $output_base_dir) {
        $this->input_file_data = $data;
        $this->output_base_dir = $output_base_dir;
    }

    public function getRelativePath() : string {
        return preg_replace(
            '/^\.\//',
            '',
            $this->input_file_data->getRelativePath()
        );
    }

    public function getAbsolutePath() : string {
        return $this->output_base_dir . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }

    public function getInput() : FileData {
        return $this->input_file_data;
    }

    public function current_url() : string
    {
        return Site::get()->url($this->output_relative_path);
    }

    public function writePage(string $content) {
        $file_name = $this->getAbsolutePath();
        @mkdir(dirname($file_name), 0777, true);
        file_put_contents($file_name, $content);
    }

    /**
     * @param FileData $data
     * @param string $output_base_dir
     * @return Page|null
     * @throws exception\InvalidTemplateClassException
     */
    static public function create(FileData $data, string $output_base_dir) : Page
    {

        $class_name = $data->getGenerationClassName();

        $page = new $class_name($data, $output_base_dir);
        return $page;
    }

    /**
     * @param string $relative_path
     * @param callable $function
     * @throws \Exception
     */
    public function generate_from_function(string $relative_path, callable $function)
    {
        $page = new PageFunction($this->input_file_data->createChildData($relative_path));
        $page->function = $function;

        $page->generate();
    }
}

