<?php
$_SERVER['REQUEST_METHOD'] = 'HEAD';
$_SERVER['REQUEST_URI'] = @$argv[1];
include dirname(__FILE__) . '/../index.php';
?>