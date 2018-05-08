<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;

/**
 * Class Logger
 * Logger class to log messages with nesting levels.
 * @package edwrodrig\static_generator\util
 */
class Logger
{
    /**
     * @var resource
     */
    protected $target;

    /**
     * The current nesting level
     * @var
     */
    private $current_nesting_level = 0;

    /**
     * The nesting level of the last log.
     *
     * Useful to handle indentation correctly
     * @var int
     */
    private $last_nesting_level = 0;

    public function __construct($target = null)
    {

        $this->setTarget($target);
    }


    public function setTarget($target = null) {
        if ( is_null($target) ) {
            $this->target = STDOUT;
        } else {
            $this->target = $target;
        }
    }

    /**
     * When anything is written, the last char is considered a new line
     * @return string
     */
    public function getLastCharWritten() : string {
        if ( $pos = ftell($this->target) ) {
            fseek($this->target, -1, SEEK_CUR);
            return fgetc($this->target);
        } else {
            return "\n";
        }

    }

    /**
     * Use this when the message starts a new nesting level.
     *
     * This message is always indented
     * @param string $message
     * @return Logger
     */
    public function begin(string $message) : Logger {
        $this->log($message);
        $this->last_nesting_level = $this->current_nesting_level;
        $this->current_nesting_level++;

        return $this;
    }

    /**
     * Use this when the message finishes a nesting level
     * @param string $message
     * @param bool $indent To indent or to not indent?
     * @return Logger
     */
    public function end(string $message, bool $indent = true) : Logger {
        $this->current_nesting_level--;
        $this->log($message, $indent);
        return $this;

    }

    /**
     * Use this when the message does not start a new nesting level.
     *
     * In other world a normal log.
     * @param string $message The message to log
     * @param bool $indent To indent or to not indent?
     * @return Logger
     */
    public function log(string $message, bool $indent = true) : Logger {
        $message = trim ($message);
        $message = str_replace("\n", "\n" . $this->getIndentation(), $message);

        if ( $this->last_nesting_level < $this->current_nesting_level ) {
            $this->last_nesting_level = $this->current_nesting_level;
            $indent = true;

        } else if ( $this->last_nesting_level > $this->current_nesting_level ) {
            $this->last_nesting_level = $this->current_nesting_level;
            $indent = true;
        }

        if ( $indent ) {
            $this->printIndentation();
        }

        fwrite($this->target, $message);

        return $this;
    }

    protected function getIndentation() : string {
        return str_repeat("  ", $this->getCurrentNestingLevel());
    }

    protected function printIndentation() : void {

        if ( $this->getLastCharWritten() != "\n" ) {
            fwrite($this->target, "\n");
        }

        fwrite(
            $this->target,
            $this->getIndentation()
        );
    }

    /**
     * Get the current level.
     *
     * It may correspond to the indentation level of the log
     * @return int
     */
    private function getCurrentNestingLevel() : int {
        return $this->current_nesting_level;
    }
}