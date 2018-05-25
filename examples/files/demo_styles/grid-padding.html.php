<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?=$this->url('/style.css')?>">
</head>
<body>
<style>

    .grid {
        background-color: blue;
    }
    .grid > div {
        background-color: red;
        width: 200px;
        height: 200px;
    }
</style>
<div class="grid">
    <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
</div>

</body>
</html>