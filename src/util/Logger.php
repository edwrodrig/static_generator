<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;


class Logger
{
    /**
     * @var resource
     */
    private $target;

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

    public function begin(string $message) : Logger {
        $this->log($message);
        $this->last_nesting_level = $this->current_nesting_level;
        $this->current_nesting_level++;

        return $this;
    }

    public function end(string $message, bool $indent = true) : Logger {
        $this->current_nesting_level--;
        $this->log($message, $indent);
        return $this;

    }

    /**
     * @param string $message The message to log
     * @param bool $indent To indent or to not indent?
     * @return Logger
     */
    public function log(string $message, bool $indent = true) : Logger {
        $new_line = false;
        if ( $this->last_nesting_level < $this->current_nesting_level ) {
            $this->last_nesting_level = $this->current_nesting_level;
            $new_line = true;
            $indent = true;

        } else if ( $this->last_nesting_level > $this->current_nesting_level ) {
            $this->last_nesting_level = $this->current_nesting_level;
            $new_line = true;
            $indent = true;
        }

        if ( $new_line && $this->getLastCharWritten() != "\n" ) {
            fwrite($this->target, "\n");
        }

        if ( $indent ) {
            $this->printIndentation();
        }

        fwrite($this->target, $message);

        return $this;
    }

    public function printIndentation() : void {
        fwrite(
            $this->target,
            str_repeat("  ", $this->getCurrentNestingLevel())
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