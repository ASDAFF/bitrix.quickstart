<?
error_reporting(E_ALL &~E_NOTICE);

if (!$argv[2])
	die("This script shows unused language phrases.\nUsage: <path to php> ".basename(__FILE__)." <lang file> <code file> [code file...]\n");
if (!file_exists($lang = $argv[1]))
	die("File does not exist: ".$lang."\n");
if (!file_exists($code1 = $argv[2]))
	die("File does not exist: ".$code1."\n");
include($lang);
$str = '';
for($i = 2; $i <= count($argv); $i++)
	if (file_exists($argv[$i]))
		$str .= file_get_contents($argv[$i]);
foreach($MESS as $k=>$v)
{
	if (false === strpos($str, '"'.$k.'"') && false === strpos($str, "'".$k."'"))
		echo $k."\n";
}
