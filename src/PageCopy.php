<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

/**
 * Class PageCopy
 *
 * This type of page is used when the source is copied as it is to the target.
 * Used by most document file types and static content that does not require processing like doc, pdf, txt, etc
 * @api
 * @package edwrodrig\static_generator
 */
class PageCopy extends PageFile
{

    /**
     * This just copy the page to the output.
     *
     * @api
     * @uses PageFile::copyPage()
     * @return string
     * @throws exception\CopyException
     */
    public function generate() : string
    {
        $this->copyPage();
        return '';
    }

}
