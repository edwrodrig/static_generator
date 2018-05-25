<?php
/**
 * @template
 * @var $this \edwrodrig\static_generator\template\TemplateHtmlBasic
 */

use edwrodrig\static_generator\cache\ImageItem;
?>
<h1><?=$this->tr(['es' => 'Amelia de Slayers', 'en' => 'Amelia of Slayers'])?></h1>
<h2>Original:</h2>
<img src="<?=$this->getCache('cache/images')->update((new ImageItem(__DIR__ . '/../data', 'amelia.jpg')))?>">
<h2>Thumbnail:</h2>

<img src="<?=$this->getCache('cache/images')->update((new ImageItem(__DIR__ . '/../data', 'amelia.jpg'))->resizeContain(100,100))?>">


