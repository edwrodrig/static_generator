<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

class PageCopy extends PageFile
{

    /**
     * @return string
     * @throws exception\CopyException
     */
    public function generate() : string
    {
        $this->copyPage();
        return '';
    }

}
