<?php

namespace edwrodrig\static_generator;

class PageTemplateInstance extends Page
{

    protected $template = null;

    /**
     * @return null|string
     */
    public function prepare_output() : string
    {
        if (is_null($this->output_relative_path)) {
            $output = preg_replace('/\.php$/', '', $this->input_relative_path);
            $this->output_relative_path = $output;
        }
        return parent::prepare_output();
    }

    /**
     * @throws exception\TemplateClassDoesNotExistsException
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
     */
    public function generate_string() : string
    {
        $template = $this->get_template();
        self::push($this);

        $content = Util::ob_safe(function() use ($template) { $template->print(); });
        self::pop();

        return $content;
    }

    /**
     * @return Template
     * @throws exception\TemplateClassDoesNotExistsException
     */
    public function get_template() : Template {
        if ( is_null($this->template) ) {
            $php_file = $this->input_absolute_path;

            $metadata = new PageMetadata($php_file);
            $template_class = $metadata->get_template_class();

            $this->template = new $template_class($php_file, $metadata);
        }
        return $this->template;
    }

    public function __call(string $method, array $arguments) {
        return $this->template->{$method}(...$arguments);
    }
}
