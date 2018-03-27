<?php

use edwrodrig\static_generator\Site;
use function edwrodrig\static_generator\tr;

echo tr(['es'=> 'hola', 'en' => 'hello']);

if ( Site::get()->get_lang() == 'es' ) :
?>

hola como te va
<?php
elseif ( Site::get()->get_lang() == 'en' ) :
?>

hellow how are you
<?php
endif;