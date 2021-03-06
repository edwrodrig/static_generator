<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 12-05-18
 * Time: 12:04
 */

namespace edwrodrig\static_generator\template;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\util\Util;
use MatthiasMullie\Minify\JS;
use Throwable;


/**
 * Class TemplateJs
 * This template is special to generate minified js files.
 * The content echoes should be a valid javascript code
 * @api
 * @package edwrodrig\static_generator\template
 */
class TemplateJs extends Template
{

    /**
     * The internally minifier object
     * @var JS
     */
    private JS $minifier;

    /**
     * Template constructor.
     * @api
     * @param PagePhp $page_info
     */
    public function __construct(PagePhp $page_info) {
        parent::__construct($page_info);

        $this->minifier = new JS;

    }


    /**
     * Echoes a minified javascript
     * @throws Throwable
     * @api
     */
    public function print() {
        $output = Util::outputBufferSafe(function() {
            /** @noinspection PhpIncludeInspection */
            include $this->page_info->getSourceAbsolutePath();
        });
        $this->minifier->add($output);
        echo $this->minifier->minify();
    }

    /**
     * Get the template type
     *
     * @api
     * @return string
     */
    public function getTemplateType() : string {
        return 'js';
    }

}