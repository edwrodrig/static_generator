<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\Util;

/**
 * Class PageFunction
 *
 * This page is used to generate pages from an arbitrary function.
 * Useful when the you want to generate a set of files or when the file is can not written.
 * (Examples: when a set of files depends on a database entries like post entries)
 * @package edwrodrig\static_generator
 */
class PageFunction extends Page
{

    /**
     * Function that echo the function
     * @var callable
     */
    private $function;

    /**
     * PageFunction constructor.
     * @param string $relative_path
     * @param Context $context
     * @param callable $function
     */
    public function __construct(string $relative_path, Context $context, callable $function) {
        parent::__construct($relative_path, $context);
        $this->function = $function;
    }

    /**
     * @throws \Exception
     */
    public function generate() : string
    {
        $content = Util::outputBufferSafe($this->function);
        $this->writePage($content);

        return $content;
    }
}
