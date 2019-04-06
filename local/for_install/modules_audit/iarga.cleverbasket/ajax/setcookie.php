<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
if(defined("BX_UTF") && BX_UTF=="Y"){
	$_POST['name'] = iconv('utf-8','windows-1251',$_POST['name']);
	$_POST['name'] = iconv('utf-8','windows-1251',$_POST['name']);
}
setcookie($_POST['name'],$_POST['val'],time()+24*3600*5,'/');?>