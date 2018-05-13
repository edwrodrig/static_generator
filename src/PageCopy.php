<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

class PageCopy extends PageFile
{

    public function generate() : string
    {
        $source = $this->getSourceAbsolutePath();
        $target =  $this->getTargetAbsolutePath();
        $this->getLogger()->begin(sprintf("Copying file [%s]...", $this->getTargetRelativePath()));

        $command = sprintf("cp %s %s", $source, $target);
        exec($command);

        $this->getLogger()->end("DONE", false);
        return $source;
    }

}
