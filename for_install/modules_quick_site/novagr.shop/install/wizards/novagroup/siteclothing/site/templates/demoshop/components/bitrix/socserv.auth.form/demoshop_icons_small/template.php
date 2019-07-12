<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<div class="bx-auth-serv-icons">
<?
foreach($arParams["~AUTH_SERVICES"] as $service):
if ($service["ID"] == "VKontakte") {

	//$linkClass = "soc-vk";
	//$pic = SITE_TEMPLATE_PATH . '/images/vk-ico.png';

	$appID = CSocServVKontakte::GetOption("vkontakte_appid");
	$appSecret = CSocServVKontakte::GetOption("vkontakte_appsecret");

	$gAuth = new CVKontakteOAuthInterface($appID, $appSecret);
	$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id=VKontakte');

	$state = 'site_id='.SITE_ID.'&backurl='.urlencode($GLOBALS["APPLICATION"]->GetCurPageParam('check_key='.$_SESSION["UNIQUE_KEY"], array("logout", "auth_service_error", "auth_service_id")));

	$url = $gAuth->GetAuthUrl($redirect_uri, $state);

	//$function = 'onclick="BX.util.popup(\''.htmlspecialcharsbx(CUtil::JSEscape($url)).'\', 580, 400)"';


} elseif ($service["ID"] == "Facebook") {
	//$linkClass = "soc-fb";
	//$pic = SITE_TEMPLATE_PATH . '/images/ff-ico.png';

	$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id=Facebook&check_key='.$_SESSION["UNIQUE_KEY"]);

	$appID = CSocServFacebook::GetOption("facebook_appid");
	$appSecret = CSocServFacebook::GetOption("facebook_appsecret");

	$fb = new CFacebookInterface($appID, $appSecret);
	$url = $fb->GetAuthUrl($redirect_uri);
	

	
	//return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialchars(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button facebook-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_fb_note").'</span>';

	//$function = 'onclick="BX.util.popup(\''.htmlspecialchars(CUtil::JSEscape($url)).'\', 580, 400)"';
	//
	/*onclick="BxShowAuthFloat('<?=$service["ID"]?>', '<?=$arParams["SUFFIX"]?>')"*/
}


?>
	<a title="<?=htmlspecialchars($service["NAME"])?>" href="javascript:void(0)" onclick="$('#authForm').modal('hide');BX.util.popup('<?=htmlspecialcharsbx(CUtil::JSEscape($url))?>', 580, 400);"><i class="bx-ss-icon <?=htmlspecialchars($service["ICON"])?>"></i></a>
<?endforeach?>
</div>
