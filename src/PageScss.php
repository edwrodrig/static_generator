<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

/**
 * Class PageScss
 *
 * This page correspond a scss file that should be compiled to a css file
 * @package edwrodrig\static_generator
 */
class PageScss extends PageFile
{
    /**
     * Get the target relative path.
     *
     * In this case, the source final extension is replaced from scss to css
     * @return string
     */
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
     * The scss is generated {@see Crunched minified}.
     * The included files should be in the {@see Context::getSourceRootPath() source root path}. Remember that their names should start with a underscore.
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