<?php


use edwrodrig\static_generator\cache\FileItem;
use edwrodrig\static_generator\Site;

$file = new FileItem(__DIR__ . '/../' , '.gitignore');

Site::get()->globals['cache']->update_cache($file);

