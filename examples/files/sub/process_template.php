<?php
/**
 * @silent
 */

use edwrodrig\static_generator\PagePhp;

for ($i = 0; $i < 10; $i++ ) {
  $this->generateFromFunction(
    "sub/hola_t_$i.html",
    function() use ($i) {
      $t = new class ($this->getPageInfo()) extends Template {
          
          public function print() {
              echo "hola";
          }
      };

      $t->print();

    }
  );
}
