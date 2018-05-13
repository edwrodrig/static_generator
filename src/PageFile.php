<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


abstract class PageFile extends Page
{

    public function __construct(string $source_path, Context $context) {
        parent::__construct($source_path, $context);
    }

    /**
     * Get the absolute path of the source file.
     * Reutnr null if the input file is not existant
     * @return null|string
     */
    public function getSourceAbsolutePath() : string {
        return $this->context->getSourceRootPath() . DIRECTORY_SEPARATOR . $this->getSourceRelativePath();
    }

    public function getSourceRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * Get the contents of the file.
     *
     * Return an empty string it the file does not exist.
     * The file should always exist.
     * This case is considered for testing
     * @return string
     */
    public function getSourceFileContents() : string {
        $file = $this->getSourceAbsolutePath();

        if ( file_exists($file) )
            return file_get_contents($file);
        else
            return "";
    }

    /**
     * Copy the source file to the target file.
     *
     * @throws exception\CopyException
     */
    protected function copyPage() {
        $source = $this->getSourceAbsolutePath();
        $target = $this->getTargetAbsolutePath();
        @mkdir(dirname($target), 0777, true);
        $this->getLogger()->begin(sprintf("Copying file [%s]...", $this->getTargetRelativePath()));

        if ( !copy($source, $target) ) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\CopyException('Error at copying');
        }

        $this->getLogger()->end("DONE", false);
    }
}

