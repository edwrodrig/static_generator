<?php

namespace edwrodrig\static_generator;

class PagePhp extends Page
{

    public function prepare_output() : string
    {
        if (is_null($this->output_relative_path)) {
            $output = preg_replace('/\.php$/', '', $this->input_relative_path);
            $this->output_relative_path = $output;
        }
        return parent::prepare_output();
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
     */
    public function generate_string() : string
    {
        $php_file = $this->input_absolute_path;
        self::push($this);

        $content = Util::ob_safe(function () use ($php_file) {
            require($php_file);
        });

        self::pop();
        return $content;
    }

}
