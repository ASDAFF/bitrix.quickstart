<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/dbconn.php");
if(!defined("BX_UTF") || BX_UTF!="Y"){
	$_POST['name'] = iconv('utf-8','windows-1251',$_POST['name']);
	$_POST['val'] = iconv('utf-8','windows-1251',$_POST['val']);
}
setcookie($_POST['name'],$_POST['val'],time()+24*3600*5,'/');?>