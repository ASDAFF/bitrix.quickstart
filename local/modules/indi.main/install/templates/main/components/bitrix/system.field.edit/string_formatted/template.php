<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$dir = explode(PATH_SEPARATOR, __DIR__);
array_pop($dir);
$dir = implode(PATH_SEPARATOR, $dir);

include $dir . '/string/template.php';
