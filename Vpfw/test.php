<?php
$foo = null;
$foo = is_null($foo) ? 'bar' : $foo;
$bar = 'foo';
$bar = is_null($bar) ? 'bar' : $bar;
echo 'Expected bar: ';
var_dump($foo);
echo 'Expected foo: ';
var_dump($bar);