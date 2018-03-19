<?php
/*
@type template
@template Template
@data
{
   "title" : "title2"
}
 */

use \edwrodrig\static_generator\Page;
use edwrodrig\static_generator\Site;

echo Page::get()->get_title(),"\n";
$templates = Site::get()->get_templates();

foreach ( $templates['template'] as $template ) {
     echo $template->get_title() ,"\n";
}
?>
holahola