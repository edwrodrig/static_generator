<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\util\Util;

class PageFunction extends Page
{

    public $function;

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
