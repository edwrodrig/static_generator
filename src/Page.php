<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\Logger;

/**
 * Class Page
 *
 * Derive this when you want to register new types of pages. This case is very advanced and most used doesn't need that.
 * @see PageFileFactory for examples of page usaged
 * @package edwrodrig\static_generator
 * @api
 */
abstract class Page
{
    /**
     * The relative path of this page to the {@see Context::getTargetAbsolutePath()}
     * @var string
     */
    protected $relative_path;

    /**
     * The generation context of the page.
     *
     * This is mandatory because contains path information required for generation
     * @var Context
     */
    protected $context;

    /**
     * Page constructor.
     * @api
     * @param string $relative_path
     * @param Context $context
     */
    public function __construct(string $relative_path, Context $context) {
        $this->setTargetRelativePath($relative_path);
        $this->context = $context;
    }

    /**
     * Get the page relative path.
     *
     * It is used for output.
     *
     * You can overload this method if you want to do transformations.
     * @api
     * @return string
     */
    public function getTargetRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * It removes the leading ./ for caution.
     * @param string $relative_path
     * @api
     * @return Page
     */
    public function setTargetRelativePath(string $relative_path) : Page {
        $this->relative_path = preg_replace(
            '/^\.\//',
            '',
            $relative_path
        );
        return $this;
    }

    /**
     * Get the absolute path where the output should be written in the file system.
     *
     * @api
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
     * Get the current logger of the context.
     *
     * You should use the logger to show debug and output messages instead the stdout
     * @uses Context::getLogger()
     * @api
     * @return Logger
     */
    public function getLogger() : Logger {
        return $this->context->getLogger();
    }

    /**
     * Get the current context
     *
     * Useful when page generation needs information about the context
     * @api
     * @return Context
     */
    public function getContext() : Context {
        return $this->context;
    }

    /**
     * This function generates the page.
     *
     * This function must be implemented for new types of pages
     * @see PageCopy::generate() forn an example
     * @api
     * @return string
     */
    abstract public function generate() : string;
}

