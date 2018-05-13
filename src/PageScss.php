<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

class PageScss extends PageFile
{
    public function getTargetRelativePath() : string
    {
        $relative_path = preg_replace(
            '/\.scss/',
            '.css',
            parent::getTargetRelativePath()
        );

        return $relative_path;
    }

    /**
     * Generates the scss target.
     *
     * @uses Compiler
     * @return string An empty string.
     */
    public function generate() : string
    {
        $this->getLogger()->begin(sprintf("Processing style [%s]...",$this->getTargetRelativePath()));

            $this->getLogger()->begin("Compiling...");
            $scss = new Compiler();
            $scss->setImportPaths($this->context->getSourceRootPath());
            $scss->setFormatter(Crunched::class);
            $compiled_scss = $scss->compile($this->getSourceFileContents());
            $this->getLogger()->end("DONE\n", false);
            $this->writePage($compiled_scss);
        $this->getLogger()->end("DONE\n", false);
        return '';
    }

}