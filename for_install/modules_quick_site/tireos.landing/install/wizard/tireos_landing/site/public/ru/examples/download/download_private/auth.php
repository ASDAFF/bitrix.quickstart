<?
define("NEED_AUTH",true);

$arAuthResult["MESSAGE"] = "Доступ к файлу закрыт";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$FILE_PERM = $APPLICATION->GetFileAccessPermission($_REQUEST["DIR"]."/files/".$_REQUEST["fname"], $USER->GetUserGroupArray());
$FILE_PERM = (strlen($FILE_PERM)>0 ? $FILE_PERM : "D");

if($FILE_PERM < "R")
	$APPLICATION->AuthForm($arAuthResult["MESSAGE"]);
else
	LocalRedirect($_REQUEST["DIR"]."/".urlencode($_REQUEST["fname"]));
?>