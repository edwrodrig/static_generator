<?php

namespace edwrodrig\static_generator;

class PageFunction extends Page
{

    public $function;

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
        self::push($this);

        $content = Util::ob_safe($this->function);

        self::pop();
        return $content;
    }

}
