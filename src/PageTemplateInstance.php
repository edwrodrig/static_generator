<?php

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\Util;

class PageTemplateInstance extends Page
{

    /**
     * @return null|string
     */
    public function prepare_output() : ?string
    {
        if (is_null($this->output_relative_path)) {
            $output = preg_replace('/\.php$/', '', $this->input_relative_path);
            $this->output_relative_path = $output;
        }
        return parent::prepare_output();
    }

    /**
     * @throws exception\TemplateClassDoesNotExistsException
     * @throws exception\TemplateNotDefinedException
     */
    public function generate()
    {
        $output = $this->prepare_output();

        $this->log(sprintf("Rendering file [%s]...", $this->output_relative_path));
        file_put_contents($output, $this->generate_string());
        $this->log("DONE\n");
    }

    /**
     * @return string
     * @throws exception\TemplateClassDoesNotExistsException
     * @throws exception\TemplateNotDefinedException
     */
    public function generate_string() : string
    {
        $php_file = $this->input_absolute_path;
        self::push($this);

        $metadata = Util::get_comment_data($php_file, 'METADATA');
        $metadata = json_decode(trim($metadata), true);
        if (!isset($metadata['template']))
            throw new exception\TemplateNotDefinedException($php_file);

        $template_class = $metadata['template'];

        if (!class_exists($template_class))
            throw new exception\TemplateClassDoesNotExistsException($template_class);

        $template = new $template_class;
        $template->metadata = $metadata;
        $template->template_content['body'] = function () use ($php_file) {
            include($php_file);
        };

        $content = strval($template);
        self::pop();

        return $content;
    }

}
