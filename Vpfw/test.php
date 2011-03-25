<?php
include 'Interface/Observer.php';
include 'ObserverArray.php';
include 'Interface/Observable.php';
$arr = new Vpfw_ObserverArray();
echo '<pre>';
echo 'expected 0 - ';
var_dump(count($arr));
$arr[] =
