<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\template;

/**
 * Class TemplateXml
 * This template is special to generate xml files
 * In the future I want to minify the xml.
 * @api
 * @package edwrodrig\static_generator\template
 */
class TemplateXml extends Template
{
    /**
     * Echoes the template
     *
     * @api
     */
    public function print() {

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        parent::print();
    }
}