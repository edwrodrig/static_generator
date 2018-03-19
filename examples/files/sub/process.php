<?php
/*
 * @type process
*/

for ( $i = 0; $i < 10; $i++ ) {
  edwrodrig\static_generator\Page::get()->generate_from_function(
    "sub/hola_$i.html",
    function() use ($i) {
      echo "My $i";
    }
  );
}
