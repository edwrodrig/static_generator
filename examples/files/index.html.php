<?php
/**
 * @template
 * @var $this \edwrodrig\static_generator\template\TemplateHtmlBasic
 */
?>
<h1><?=$this->tr(['es' => 'Amelia de Slayers', 'en' => 'Amelia of Slayers'])?></h1>
<h2>Original:</h2>
<img src="<?=$this->url('/assets/amelia.jpg')?>">
<h2>Thumbnail:</h2>

<img src="<?php

//TODO simplificar esto, es inaceptable que para cada imagen escriba 5 lineas
$image = new \edwrodrig\static_generator\cache\ImageItem(__DIR__ . '/assets', 'amelia.jpg');
$image->resizeContain(100, 100);

/**
 * @var $cache \edwrodrig\static_generator\cache\CacheManager
 */
$cache = $this->getPageInfo()->getContext()->cache;
echo $this->url('/cache/' . $cache->update($image)->getTargetRelativePath())?>">

