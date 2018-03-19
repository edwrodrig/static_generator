<?php

namespace edwrodrig\static_generator;

class Page
{

    /**
     * @var string
     */
    public $input_relative_path;

    /**
     * @var string
     */

    public $input_absolute_path;

    /**
     * @var string|null
     */
    public $output_relative_path = null;

    /**
     * @var string|null
     */
    public $output_absolute_path = null;

    /**
     * @var int
     */
    public $level = 0;

    use Stack;

    public function set_data($data)
    {
        $this->input_absolute_path = $data['absolute_path'] ?? null;
        $this->input_relative_path = $data['relative_path'] ?? null;

    }

    public function current_url() : string
    {
        return Site::get()->url($this->input_relative_path);
    }

    /**
     * @return null|string
     */
    public function prepare_output() : string
    {
        $this->output_relative_path = preg_replace('/^\.\//', '', $this->output_relative_path);
        $this->output_absolute_path = Site::get()->output($this->output_relative_path);
        @mkdir(dirname($this->output_absolute_path), 0777, true);
        return $this->output_absolute_path;
    }

    /**
     * @param $data
     * @return Page|null
     */
    static public function create($data) : ?Page
    {
        $path = $data['relative_path'];

        if (preg_match('/\.php$/', $path)) {
            $metadata = new PageMetadata($data['absolute_path']);

            $type = $metadata->get_type();

            if ($type == 'copy') {
                $page = new PageCopy();
                $page->set_data($data);
                return $page;

            } else if ($type == 'process') {
                $page = new PageProc();
                $page->set_data($data);
                return $page;

            } else if ($type == 'template') {
                $page = new PageTemplateInstance();
                $page->set_data($data);
                return $page;

            } else {
                $page = new PagePhp();
                $page->set_data($data);
                return $page;
            }
        } else if ( preg_match('/\.scss$/', $path)) {
            $page = new PageScss();
            $page->set_data($data);
            return $page;

        } else if (!preg_match('/\.swp$/', $path)) {
            $page = new PageCopy();
            $page->set_data($data);
            return $page;
        } else {
            return null;
        }
    }

    /**
     * @param $data
     * @return Template|null
     * @throws exception\TemplateClassDoesNotExistsException
     */
    static public function instance_template($data) : ?Template
    {
        $path = $data['relative_path'];

        if (preg_match('/\.php$/', $path)) {
            $metadata = new PageMetadata($data['absolute_path']);

            $type = $metadata->get_type();

            if ($type == 'template') {
                $page = new PageTemplateInstance();
                $page->set_data($data);
                return $page->get_template();
            }
        }

        return null;
    }

}

