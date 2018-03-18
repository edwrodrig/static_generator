<?php

namespace edwrodrig\static_generator;

class PageCopy extends Page
{

    /**
     * @return null|string
     */
    public function prepare_output() : string
    {
        if (is_null($this->output_relative_path)) {
            $this->output_relative_path = $this->input_relative_path;
        }

        return parent::prepare_output();
    }

    public function generate()
    {
        $output = $this->prepare_output();

        $this->log(sprintf("Copying file [%s]...", $this->input_relative_path));

        $command = sprintf("cp %s %s", $this->input_absolute_path, $output);
        passthru($command);

        $this->log("DONE\n");
    }

}
