<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\Logger;

class Page
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
     * It removes the leading ./ for caution.
     * You can overload this method if you want to do other transformations.
     * @return string
     */
    public function getRelativePath() : string {
        return preg_replace(
            '/^\.\//',
            '',
            $this->relative_path
        );
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
        return $this->getRelativePath();
    }

    /**
     * Get the absolute path where the output should be written in the file system.
     * @return string
     */
    public function getTargetAbsolutePath() : string {
        return $this->context->getTargetRootPath() . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }


    /**
     * Get a url with absolute target path if needed.
     *
     * It is useful when the target web folder is not /, when you have different version of a site in different folder, for example, by languages
     * @param string $path
     * @return string
     */
    public function url(string $path) : string {
        if ( strpos($path, '/') === 0 )
            return '/' . $this->context->getTargetWebPath() . $path;
        else
            return $path;
    }

    /**
     * Get the url of the current page.
     *
     * Useful to determine if you're in a current page.
     * ```
     * $this->>currentUrl() == '/index.html'
     * ```
     * @return string
     */
    public function currentUrl() : string
    {
        return $this->url('/' . $this->getTargetRelativePath());
    }

    /**
     * Write the output page.
     *
     * It uses the {@see Page::getAbsolutePath() absolute path} as s target.
     * It creates al directories if the path does not exists
     * @param string $content The content to write
     */
    public function writePage(string $content) {
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
     * @param FileData $data
     * @param Context $context
     * @return Page|null
     * @throws util\exception\IgnoredPageFileException
     */
    public static function create(FileData $data, Context $context) : Page
    {
        $class_name = $data->getGenerationClassName();

        $page = new $class_name($data, $context);
        return $page;
    }

    /**
     * @param string $relative_path
     * @param callable $function
     * @throws \Exception
     */
    public function generate_from_function(string $relative_path, callable $function)
    {
        $page = new PageFunction($relative_path, $this->context);
        $page->function = $function;

        $page->generate();
    }

    /**
     * @return Logger
     */
    public function getLogger() : Logger {
        return $this->context->getLogger();
    }
}

