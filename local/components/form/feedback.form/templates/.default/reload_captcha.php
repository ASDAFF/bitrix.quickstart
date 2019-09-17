<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $APPLICATION;
echo json_encode($APPLICATION->CaptchaGetCode()); 
?>