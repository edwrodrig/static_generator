<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-05-18
 * Time: 12:25
 */

namespace edwrodrig\static_generator\util;

/**
 * Class TemporaryLogger
 *
 * A Logger that saves their output in a temporary file.
 * It provides a method to {@see TemporaryLogger::getTargetData() get all data logged}
 * @package edwrodrig\static_generator\util
 */
class TemporaryLogger extends Logger
{
    public function __construct() {
        parent::__construct(
            fopen('php://temp', 'w+')
        );
    }

    public function __destruct()
    {
        fclose($this->target);
    }

    public function getTargetData() : string {
        rewind($this->target);
        return stream_get_contents($this->target);
    }
}