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

class PageScss extends PageFile
{
    public function getRelativePath() : string
    {
        $relative_path = preg_replace(
            '/\.scss/',
            '.css',
            parent::getRelativePath()
        );

        return $relative_path;
    }

    public function generate()
    {
        $this->getLogger()->begin(sprintf("Processing style [%s]...",$this->getRelativePath()));

            $this->getLogger()->begin("Compiling...");
            $scss = new Compiler();
            $scss->setImportPaths($this->context->getSourceRootPath());
            $scss->setFormatter(Crunched::class);
            $compiled_scss = $scss->compile($this->source_file_data->getFileContents());
            $this->getLogger()->end("DONE\n", false);
            $this->writePage($compiled_scss);
        $this->getLogger()->end("DONE\n", false);
    }

}