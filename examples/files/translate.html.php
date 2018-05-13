<?php
/**
 * @var $this \edwrodrig\static_generator\template\Template
 */

$this->tr(['es'=> 'hola', 'en' => 'hello']);

if ( $this->getLang() == 'es') :
?>
hola como te va
<?php
elseif ( $this->getLang() == 'en' ) :
?>

hello how are you
<?php
endif;