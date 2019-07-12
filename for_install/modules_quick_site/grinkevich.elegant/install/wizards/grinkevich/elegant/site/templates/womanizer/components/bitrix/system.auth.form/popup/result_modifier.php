<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arResult["BACKURL"] = $_SERVER["HTTP_REFERER"];
$arResult["AUTH_URL"] = $_SERVER["HTTP_REFERER"];
$url_vk = $_SERVER["HTTP_REFERER"];
$url_vk = str_replace("http://".$_SERVER["HTTP_HOST"], "", $_SERVER["HTTP_REFERER"]);


if(is_array($arResult["AUTH_SERVICES"]["VKontakte"])){
	$arResult["AUTH_SERVICES"]["VKontakte"]["FORM_HTML"] = str_replace("/includes/login.php", $url_vk, $arResult["AUTH_SERVICES"]["VKontakte"]["FORM_HTML"]);
}
?>