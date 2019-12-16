<?php

use edwrodrig\file_cache\ImageItem;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?=$this->url('/style.css')?>">
</head>
<body>
<div style="width:30%">
    <div class="responsive-square">
        <img src="<?=$this->getCache('cache/images')->update((new ImageItem(__DIR__ . '/../../data', 'amelia.jpg')))?>">
    </div>
    <br/>
    <div class="responsive-4-3">
        <img src="<?=$this->getCache('cache/images')->update((new ImageItem(__DIR__ . '/../../data', 'amelia.jpg')))?>">
    </div>
</div>

</body>
</html>