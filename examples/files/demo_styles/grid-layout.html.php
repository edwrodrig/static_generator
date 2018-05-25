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

    .grid-fixed {
        background-color: blue;
    }
    .grid-fixed > div {
        background-color: red;
        height: 200px;
    }
</style>
<div class="grid-fixed">
    <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
</div>

</body>
</html>