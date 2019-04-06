<?
error_reporting(E_ALL &~E_NOTICE);

if (!$argv[2])
	die("This script inserts language phrases back to the code and prints the result.\nUsage: <path to php> ".basename(__FILE__)." <code file> <lang file>\n");
if (!file_exists($code = $argv[1]))
	die("File does not exist: ".$code."\n");
if (!file_exists($lang = $argv[2]))
	die("File does not exist: ".$lang."\n");
include($lang);
$str = file_get_contents($code);
foreach($MESS as $k=>$v)
{
	$str = str_replace('<'.'?=GetMessage("'.$k.'")?'.'>',$v,$str);
	$str = str_replace('GetMessage("'.$k.'")',"'".$v."'",$str);
}
echo $str;
