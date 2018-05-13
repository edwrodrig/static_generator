<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\Logger;

/**
 * Class Page
 * @package edwrodrig\static_generator
 */
abstract class Page
{
    /**
     * @var string
     */
    protected $relative_path;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(string $relative_path, Context $context) {
        $this->relative_path = $relative_path;
        $this->context = $context;
    }

    /**
     * Get the page relative path.
     *
     * It is used for output.
     * It removes the leading ./ for caution.
     * You can overload this method if you want to do other transformations.
     * @return string
     */
    public function getTargetRelativePath() : string {
        return preg_replace(
            '/^\.\//',
            '',
            $this->relative_path
        );
    }

    /**
     * Get the absolute path where the output should be written in the file system.
     * @return string
     */
    public function getTargetAbsolutePath() : string {
        return $this->context->getTargetRootPath() . DIRECTORY_SEPARATOR . $this->getTargetRelativePath();
    }

    /**
     * Write the output page.
     *
     * It uses the {@see Page::getAbsolutePath() absolute path} as s target.
     * It creates al directories if the path does not exists
     * @api
     * @param string $content The content to write
     */
    protected function writePage(string $content) {
        $this->getLogger()->begin(sprintf("Generating file [%s]...", $this->getTargetRelativePath()));

        if ( empty($content) ) {
          $this->getLogger()->end("Empty! SKIPPED", false);
        } else {
            $file_name = $this->getTargetAbsolutePath();
            @mkdir(dirname($file_name), 0777, true);
            file_put_contents($file_name, $content);

            $this->getLogger()->end("DONE", false);
        }
    }

    /**
     * @return Logger
     */
    public function getLogger() : Logger {
        return $this->context->getLogger();
    }

    /**
     * Get the current context
     * @return Context
     */
    public function getContext() : Context {
        return $this->context;
    }

    abstract public function generate() : string;
}

