<?
global $APPLICATION, $USER;

session_start();

if(!$USER->IsAdmin())
	return;

if(!CModule::IncludeModule("mailtrig.events"))
	return;

$obClient = new CMailTrigClient;

IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_MAIN"),
		"ICON" => "ib_settings",
		"TITLE" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_MAIN_TITLE")
	),
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_INFO"),
		"ICON" => "ib_settings",
		"TITLE" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_INFO_TITLE")
	),
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_ADDITIONAL"),
		"ICON" => "ib_settings",
		"TITLE" => GetMessage("MAILTRIG_EVENTS_OPTIONS_TAB_ADDITIONAL_TITLE")
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// save other options
if($REQUEST_METHOD=="POST" && (strlen($SaveRegister) > 0 || strlen($Update) > 0) && check_bitrix_sessid())
{
	COption::SetOptionString("mailtrig.events", "site_name", trim($_POST["site_name"]));
	COption::SetOptionString("mailtrig.events", "server_name", trim($_POST["server_name"]));
	COption::SetOptionString("mailtrig.events", "site_email", trim($_POST["site_email"]));
	COption::SetOptionString("mailtrig.events", "support_phone", trim($_POST["support_phone"]));
	COption::SetOptionString("mailtrig.events", "profile_url", trim($_POST["profile_url"]));
	COption::SetOptionString("mailtrig.events", "basket_url", trim($_POST["basket_url"]));
	COption::SetOptionString("mailtrig.events", "order_url", trim($_POST["order_url"]));
	COption::SetOptionString("mailtrig.events", "partner", trim($_POST["partner"]));

	if($_POST["debug_mode"] == "Y")
		COption::SetOptionString("mailtrig.events", "debug_mode", "Y");
	else
		COption::RemoveOption("mailtrig.events", "debug_mode");
}

if($REQUEST_METHOD=="POST" && strlen($SaveRegister) > 0 && check_bitrix_sessid())
{
	$sMtApiUrl = "http://" . COption::GetOptionString("mailtrig.events", "server_name") . (($_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'') . "/bitrix/tools/mailtrig_events_api.php";
	$sPartner = COption::GetOptionString("mailtrig.events", "partner");

	$arRegisterResult = $obClient->regUser(trim($_POST["email"]), $_POST["password"], $sMtApiUrl, $sPartner);
	/*
	if($arRegisterResult["error"])
		$arError[] = GetMessage("MAILTRIG_EVENTS_OPTIONS_RESPONSE_FROM_MAILTRIG") . $arRegisterResult["error_message"];
	*/

	if($arRegisterResult["status"] == "200")
	{
		COption::SetOptionString("mailtrig.events", "login", trim($_POST["email"]));
		COption::SetOptionString("mailtrig.events", "password", $_POST["password"]);
		COption::SetOptionString("mailtrig.events", "appId", $arRegisterResult["data"]["appId"]);

		$_SESSION["MAILTRIG_OPTIONS"]["NOTE"][] = GetMessage("MAILTRIG_EVENTS_OPTIONS_DATA_SAVED");
		LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=mailtrig.events");
	}
	else
	{
		$_SESSION["MAILTRIG_OPTIONS"]["ERROR"][] = GetMessage("MAILTRIG_EVENTS_OPTIONS_RESPONSE_FROM_MAILTRIG") . $arRegisterResult["error_message"];
		LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=mailtrig.events");
		//$arError[] = GetMessage("MAILTRIG_EVENTS_OPTIONS_RESPONSE_FROM_MAILTRIG") . $arRegisterResult["error_message"];
	}
}

if($REQUEST_METHOD=="POST" && strlen($Update) > 0 && check_bitrix_sessid())
{
	$sMtApiUrl = "http://" . COption::GetOptionString("mailtrig.events", "server_name") . (($_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'') . "/bitrix/tools/mailtrig_events_api.php";

	$sPartner = COption::GetOptionString("mailtrig.events", "partner");

	$arAppData = $obClient->getAppId(trim($_POST["email"]), $_POST["password"], $sMtApiUrl, $sPartner);
	if($arAppData["status"] == 200)
	{
		COption::SetOptionString("mailtrig.events", "login", trim($_POST["email"]));
		COption::SetOptionString("mailtrig.events", "password", $_POST["password"]);
		COption::SetOptionString("mailtrig.events", "appId", $arAppData["data"]["appId"]);

		$_SESSION["MAILTRIG_OPTIONS"]["NOTE"][] = GetMessage("MAILTRIG_EVENTS_OPTIONS_DATA_SAVED");
		LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=mailtrig.events");
	}
	else
	{
		$_SESSION["MAILTRIG_OPTIONS"]["ERROR"][] = GetMessage("MAILTRIG_EVENTS_OPTIONS_RESPONSE_FROM_MAILTRIG") . $arRegisterResult["error_message"];
		LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=mailtrig.events");
	}
}

// get module options
$sLogin = COption::GetOptionString("mailtrig.events", "login");
$sPassword = COption::GetOptionString("mailtrig.events", "password");
$sAppId = COption::GetOptionString("mailtrig.events", "appId");
$sPartner = COption::GetOptionString("mailtrig.events", "partner");

$sSiteName = COption::GetOptionString("mailtrig.events", "site_name");
$sServerName = COption::GetOptionString("mailtrig.events", "server_name");
$sSiteEmail = COption::GetOptionString("mailtrig.events", "site_email");
$sSupportPhone = COption::GetOptionString("mailtrig.events", "support_phone");
$sProfileUrl = COption::GetOptionString("mailtrig.events", "profile_url");
$sBasketUrl = COption::GetOptionString("mailtrig.events", "basket_url");
$sOrderUrl = COption::GetOptionString("mailtrig.events", "order_url");

$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;

if(!empty($_SESSION["MAILTRIG_OPTIONS"]["ERROR"]))
	foreach($_SESSION["MAILTRIG_OPTIONS"]["ERROR"] as $value)
		$arError[] = $value;

if(!empty($_SESSION["MAILTRIG_OPTIONS"]["NOTE"]))
	foreach($_SESSION["MAILTRIG_OPTIONS"]["NOTE"] as $value)
		$arNote[] = $value;

if(!empty($arError))
	$sError = implode("<br />", $arError);
if(strlen($sError) > 0)
	CAdminMessage::ShowMessage($sError);

if(!empty($arNote))
	$sNote = implode("<br />", $arNote);
if(strlen($sNote) > 0)
	CAdminMessage::ShowNote($sNote);

$messageDescription = new CAdminMessage(array(
	"MESSAGE" => GetMessage("MAILTRIG_EVENTS_OPTIONS_MODULE_DESCRIPTION", array("#LANG#" => LANG)),
	"TYPE" => "OK",
	"DETAILS" => GetMessage("MAILTRIG_EVENTS_OPTIONS_MODULE_DESCRIPTION_DETAILS", array("#LANG#" => LANG)),
	"HTML" => true
));
echo $messageDescription->Show();

unset($_SESSION["MAILTRIG_OPTIONS"]);

$tabControl->Begin();
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?=bitrix_sessid_post();?>
	<?$tabControl->BeginNextTab();?>
	<?if(!empty($sAppId)):?>
		<tr>
			<td valign="middle" width="40%"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_APPID")?>:</td>
			<td valign="middle" width="60%"><?=$sAppId?></td>
		</tr>
	<?endif;?>
	<tr>
		<td valign="middle" width="40%">
			<label for="email"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_EMAIL")?>:</label>
		</td>
		<td valign="middle" width="60%">
			<input type="text" name="email" id="email" value="<?=$sLogin?>" size="60" />
		</td>
	</tr>
	<tr>
		<td valign="middle" width="40%">
			<label for="password"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_PASSWORD")?>:</label>
		</td>
		<td valign="middle" width="60%">
			<input type="text" name="password" id="password" value="<?=$sPassword?>" size="60" />
		</td>
	</tr>
	<?if(!empty($sLogin) && !empty($sPassword)):
		$sAuthUrl = $obClient->getAutoLoginUri($sLogin, $sPassword);
		if(!empty($sAuthUrl)):
			?>
			<tr>
				<td></td>
				<td><a href="<?=$sAuthUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_AUTH_LINK_MAILTRIG")?></a></td>
			</tr>
		<?
		endif;
	endif;?>
	<tr>
		<td valign="middle" width="40%">
			<label for="partner"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_PARTNER")?></label>
		</td>
		<td valign="middle" width="60%">
			<input type="text" name="partner" id="partner" value="<?=$sPartner?>" size="60" />
		</td>
	</tr>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td valign="middle" width="40%"><label for="site_name"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_SITE_NAME")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="site_name" id="site_name" value="<?=$sSiteName?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="server_name"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_SERVER_NAME")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="server_name" id="server_name" value="<?=$sServerName?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="site_email"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_SITE_EMAIL")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="site_email" id="site_email" value="<?=$sSiteEmail?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="support_phone"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_SUPPORT_PHONE")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="support_phone" id="support_phone" value="<?=$sSupportPhone?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="profile_url"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_PROFILE_URL")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="profile_url" id="profile_url" value="<?=$sProfileUrl?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="basket_url"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_BASKET_URL")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="basket_url" id="basket_url" value="<?=$sBasketUrl?>" /></td>
	</tr>
	<tr>
		<td valign="middle" width="40%"><label for="order_url"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_ORDER_URL")?></label></td>
		<td valign="middle" width="60%"><input type="text" name="order_url" id="order_url" value="<?=$sOrderUrl?>" /></td>
	</tr>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td valign="middle" width="40%"><label for="debug_mode"><?=GetMessage("MAILTRIG_EVENTS_OPTIONS_DEBUG_MODE")?>:</label></td>
		<td valign="middle" width="60%"><input type="checkbox" name="debug_mode" id="debug_mode" value="Y"<?=($bDebugMode)?' checked="checked"':''?> /></td>
	</tr>
	<?$tabControl->Buttons();?>
	<?if(empty($sAppId)):?>
		<input type="submit" name="SaveRegister" value="<?=GetMessage("MAILTRIG_EVENTS_OPTIONS_BTN_REGISTER")?>" class="adm-btn-save" />
	<?else:?>
		<input type="submit" name="Update" value="<?=GetMessage("MAILTRIG_EVENTS_OPTIONS_BTN_UPDATE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<?endif;?>
	<?$tabControl->End();?>
</form>