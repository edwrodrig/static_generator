<?php
/**
 * @silent
 */

for ( $i = 0; $i < 10; $i++ ) {
  $this->generateFromFunction(
    "sub/hola_$i.html",
    function() use ($i) {
      echo "My $i";
    }
  );
}
