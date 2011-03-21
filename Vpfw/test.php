<?php
$var = null;
$arr['var'] = null;
echo '<pre>';
echo 'expected true ';
var_dump(isset($var));
echo 'expected true ';
var_dump(isset($arr['var']));
unset($arr['lol']);