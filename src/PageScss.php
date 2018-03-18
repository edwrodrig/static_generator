<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 18-03-18
 * Time: 9:36
 */

namespace edwrodrig\static_generator;


use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

class PageScss extends Page
{
    public function prepare_output() : string
    {
        if (is_null($this->output_relative_path)) {
            $output = preg_replace('/\.scss$/', '.css', $this->input_relative_path);
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

        if ( strpos(basename($output, '.css'), '_') === 0 )
            return;

        $string = $this->generate_string();

        if ( empty($string) )
            return;


        $this->log(sprintf("Compiling style [%s]...", $this->output_relative_path));

        file_put_contents($output, $string);
        $this->log("DONE\n");
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generate_string() : string
    {
        $scss_file = $this->input_absolute_path;
        $scss = new Compiler();
        $scss->setImportPaths(Site::get()->input_dir);
        $scss->setFormatter(Crunched::class);
        return $scss->compile(file_get_contents($scss_file));
    }
}