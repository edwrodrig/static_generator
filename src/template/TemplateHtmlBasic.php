<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\template;

/**
 * Class TemplateHtmlBasic
 * Template for a very basic html page. It includes the very {@see TemplateHtmlBasic::print() minimal stuff} to work nice in desktop and mobile devices.
 * Override {@see TemplateHtmlBasic::head()} and {@see TemplateHtmlBasic::body()} to change it contents.
 * @api
 * @package edwrodrig\static_generator\template
 */
class TemplateHtmlBasic extends Template
{


    public function print() {?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->head()?>
</head>
<body>
    <?php $this->body()?>
</body>
</html>
    <?php
    }

    /**
     * Echo the content between the head tags without including it.
     *
     * @api
     */
    public function head() : void {}

    /**
     * Echo the content between the body tags without including it.
     * By default are the contents of the {@see PageFile::getSourceAbsolutePath() source file}.
     *
     * @api
     */
    public function body() : void {
        /** @noinspection PhpIncludeInspection */
        include $this->page_info->getSourceAbsolutePath();
    }


    /**
     * @api
     * @return string
     */
    public function getTemplateType() : string {
        return 'html_basic';
    }

}