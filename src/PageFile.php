<?php

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\FileData;
use edwrodrig\static_generator\util\Logger;

class PageFile extends Page
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
     * @return string
     */
    public function getSourceFileContents() : string {
        $file = $this->getSourceAbsolutePath();

        if ( file_exists($file) )
            return file_get_contents($file);
        else
            return "";
    }

}

