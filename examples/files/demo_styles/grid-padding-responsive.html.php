<?php

use edwrodrig\static_generator\cache\ImageItem;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?=$this->url('/style.css')?>">
</head>
<body>
<style>

    .grid-fixed {
        background-color: blue;
    }

</style>
<div class="grid-fixed">
    <?php for ( $i = 0 ; $i < 50 ; $i++ ) : ?>
    <div>
        <div class="responsive-square">
            <img src="<?=$this->getCache('cache/images')->update((new ImageItem(__DIR__ . '/../../data', 'amelia.jpg')))?>">
        </div>
    </div>
    <?php endfor ?>
</div>

</body>
</html>