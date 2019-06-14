<?php
$module_id = "sozdavatel.sms";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

// sms-kontakt api
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/api/smskontakt_api.php");
// sms-bliss api
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/api/smsbliss_api.php");
// script.js
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/script.js");
// style.css
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/style.css");

$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($SMS_RIGHT >="R"):

CModule::IncludeModule("sozdavatel.sms");

global $SMS;

$arMainOptions = array(
	array(	"SMS_SERVICE", 
			GetMessage("OPT_SMS_SERVICE"), 
			"SMSDISABLED", 
			array(	"selectbox", 
					Array(	"SMSDISABLED"	=> GetMessage("SELECT_SERVICE"),
							"SMSBLISS"		=> GetMessage("SMSBLISS"),
							"SMSKONTAKT"	=> GetMessage("SMSKONTAKT"),
					)
				)
		),
	array(	"SMS_DEFAULT_RECIEVER_PHONE", 
			GetMessage("SMS_DEFAULT_RECIEVER_PHONE"), 
			"7...", 
			array("text", 18)
		),
	array(	"SMS_SEND_COPY_TO_DEFAULT_RECIEVER", 
			GetMessage("SMS_SEND_COPY_TO_DEFAULT_RECIEVER"), 
			"N", 
			array("checkbox", "Y")
		),
	array(	"COPY_SMS_TO_EMAIL_PHONE", 
			GetMessage("COPY_SMS_TO_EMAIL_PHONE"), 
			"7...", 
			array("text", "18")
		),
	array(	"COPY_SMS_TO_EMAIL_EMAIL", 
			GetMessage("COPY_SMS_TO_EMAIL_EMAIL"), 
			"", 
			array("text", "18")
		),
);

//   smsbliss
$arBlissSenders = Array("TEST"=>"TEST");
$bliss_login = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_LOGIN");
$bliss_password = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_PASSWORD");
if ((!!$bliss_login)&&(!!$bliss_password)&&(substr($bliss_login, 0, 3) == "szd"))
{
	$bliss_gate = new Smsbliss_JsonGate($bliss_login, $bliss_password);
	$bliss_senders = $bliss_gate->senders(); 
	if ($bliss_senders["status"] == "ok")
	{
		$arBlissSenders = $bliss_senders["senders"];
		$arBlissSenders = array_flip($arBlissSenders);
		foreach ($arBlissSenders as $key=>$val)
		{
			$arBlissSenders[$key] = $key;
		}
	}
}
	
$arSMSBlissOptions = array(
	array(	"SMSBLISS_LOGIN", 
			GetMessage("SMSBLISS_OPT_LOGIN"), 
			"", 
			array("text", 18)
		),
	array(	"SMSBLISS_PASSWORD", 
			GetMessage("SMSBLISS_OPT_PASSWORD"), 
			"", 
			array("text", 18)
		),
	array(	"SMSBLISS_SENDER_ID", 
			GetMessage("SMSBLISS_OPT_SENDER_ID"), 
			$arBlissSenders[0], 
			array(	"selectbox", 
					$arBlissSenders
				)
		),
);

$arSMSKontaktOptions = array(
	array(	"SMSKONTAKT_SENDER_ID", 
			GetMessage("SMSKONTAKT_OPT_SENDER_ID"), 
			"", 
			array("text", 18)
		),
	array(	"SMSKONTAKT_SENDER_PHONE", 
			GetMessage("SMSKONTAKT_OPT_SENDER_PHONE"), 
			"7", 
			array("text", 18)
		),
	array(	"SMSKONTAKT_API_KEY", 
			GetMessage("SMSKONTAKT_OPT_API_KEY"), 
			"", 
			array("text", 18)
		),
);

$aTabs = array(
	array(	"DIV" => "edit1", 
			"TAB" => GetMessage("TAB_MAIN"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_MAIN_TITLE")
		),
	array(	"DIV" => "edit2", 
			"TAB" => GetMessage("TAB_SMSBLISS"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_SMSBLISS_TITLE")
		),
	array(	"DIV" => "edit3", 
			"TAB" => GetMessage("TAB_SMSKONTAKT"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_SMSKONTAKT_TITLE")
		),
	array(	"DIV" => "edit4", 
			"TAB" => GetMessage("TAB_EXAMPLE"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_EXAMPLE_TITLE")
		),
	array(	"DIV" => "edit5", 
			"TAB" => GetMessage("TAB_RIGHTS"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_RIGHTS_TITLE")
		),
	array(	"DIV" => "edit6", 
			"TAB" => GetMessage("TAB_LOG"), 
			"ICON" => "sms_settings", 
			"TITLE" => GetMessage("TAB_LOG_TITLE")
		)
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);



if($REQUEST_METHOD=="POST" && strlen($_REQUEST["SMSBLISS_REGISTER"].$_REQUEST["SMSKONTAKT_GetApiKey"].$Update.$Apply.$RestoreDefaults)>0 && $SMS_RIGHT >= "W" && check_bitrix_sessid())
{
	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption("sozdavatel.sms");
		$APPLICATION->DelGroupRight("sozdavatel.sms");
	}
	else
	{	
		COption::RemoveOption("sozdavatel.sms", "sid");
		$arAllOptions = Array();
		$arAllOptions = array_merge(	$arMainOptions,
										$arSMSBlissOptions,
										$arSMSKontaktOptions
									);
		foreach($arAllOptions as $arOption)
		{
			$name=$arOption[0];
			$val=$_REQUEST[$name];
			if($arOption[2][0]=="checkbox" && $val!="Y")
				$val="N";
			COption::SetOptionString("sozdavatel.sms", $name, $val, $arOption[1]);
		}
	}
}


$tabControl->Begin();?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">

<?
//  
$tabControl->BeginNextTab();

?><div class="tab-text"><?
if (!function_exists(json_decode))
{
	ShowError(GetMessage("ERR_PHP_JSON_NOT_FOUND"));
}
?>
<?if (!extension_loaded('curl')):?>
	ShowError(GetMessage("ERR_PHP_CURL_NOT_FOUND"));
<?endif?>

<?
echo GetMessage("TAB_MAIN_TEXT");
echo "<br/><br/>";
?></div><?

//   ,   
$sms_service = COption::GetOptionString("sozdavatel.sms", "SMS_SERVICE");
if (($sms_service == SMSDISABLED)||(!$sms_service))
{
	ShowError(GetMessage("ERR_MODULE_OFF"));
}

foreach($arMainOptions as $arOption):
	__AdmSettingsDrawRow("sozdavatel.sms", $arOption);
endforeach;



// sms-bliss
$tabControl->BeginNextTab();

?><div class="tab-text"><?
	// 
	$bliss_login = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_LOGIN");
	$bliss_password = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_PASSWORD");
	$bliss_show_regform = true;

	if ((!!$bliss_login)&&(!!$bliss_password))
	{
		if (substr($bliss_login, 0, 3) != "szd")
		{
			ShowError(GetMessage("ERR_SMSBLISS_INCORRECT_LOGIN"));
			$bliss_show_regform = true;
		}
		else
		{	
			$bliss_gate = new Smsbliss_JsonGate($bliss_login, $bliss_password);
			$bliss_credits = $bliss_gate->credits(); 
			if ($bliss_credits["status"] == "ok")
			{
				echo GetMessage("SMSBLISS_BALANCE").": ".$bliss_credits["credits"]." ".GetMessage("SMS")." (<a href='http://smsbliss.ru/tariffs/#paySys' target='_blank'>".GetMessage("SMSBLISS_BALANCE_ADD")."</a>).<br/><br/>";
				$bliss_show_regform = false;
			}
			else
			{
				echo ShowError(GetMessage("ERR_SMSBLISS_CONNECT"));
				$bliss_show_regform = true;
			}
		}
	}
	else
	{
		$bliss_show_regform = true;
	}

	echo GetMessage("SMSBLISS_INSTRUCTION");
	echo "<br/>";

	if ($bliss_show_regform)
	{
		?>
		<div class="bliss-reg-form">
			<span><b><?=GetMessage("BLISS_GERFORM_INFO")?></b></span>
			<br/>
			<span class="hint"><?=GetMessage("BLISS_REQUIRED_HINT")?></span>
			<br/>
			<br/>
			<span class="szd-field">
				<?=GetMessage("BLISS_USER_COMPANY")?><span class="required">*</span>
			</span>
			<br/>
			<input type="text" id="bliss_user_company" size="30" value="<?=$_REQUEST['bliss_user_company']?>"/>
		
			<br/>
			<span class="szd-field">
				<?=GetMessage("BLISS_USER_NAME")?><span class="required">*</span>
			<span>
			<br/>
			<input type="text" id="bliss_user_name" size="30" value="<?=$_REQUEST['bliss_user_name']?>"/>
			
			<br/>
			<span class="szd-field">
				<?=GetMessage("BLISS_USER_PHONE")?><span class="required">*</span>
			</span>
			<br/>
			<input type="text" id="bliss_user_phone" size="30" value="<?=$_REQUEST['bliss_user_phone']?>"/>
			
			</br>
			<span class="szd-field">
				<?=GetMessage("BLISS_USER_EMAIL")?><span class="required">*</span>
			</span>
			<br/>
			<input type="text" name="bliss_user_email" id="bliss_user_email" size="30" value="<?=$_REQUEST['bliss_user_email']?>"/>
			
			<br/><br/>
			<input class="smsbliss-reg-button" type="button" onClick="SubmitBlissRegForm();" value="<?=GetMessage("BLISS_REGISTER_SUBMIT")?>">
		</div>
		<?
	}
	
	//      smsbliss
	if ($_REQUEST["SMSBLISS_REGISTER"] == "Y")
	{
		$phone = $_REQUEST["bliss_user_phone"];
		$name = $_REQUEST["bliss_user_name"];
		$company = $_REQUEST["bliss_user_company"];
		$email = $_REQUEST["bliss_user_email"];
		
		if ((!$phone)||(!$name)||(!$company)||(!$email))
		{
			ShowError(GetMessage("SMSBLISS_ALL_REQUIRED"));
		}
		else
		{
			$subject = ".:      SMS-Bliss";
			$subject = iconv(LANG_CHARSET, "CP1251", $subject);
			$subject = '=?CP1251?B?'.base64_encode($subject).'?=';
			
			$fromEmail = "sms@sozdavatel.ru";
			
			$headers =  "Content-Type: text/plain; charset=windows-1251\r\n";
			$headers .= "From: ".$fromEmail."\r\n";
			
			$message = 	"      SMS-Bliss".
						"\r\n: ".$company.
						"\r\n: ".$name.
						"\r\n: ".$phone.
						"\r\nE-Mail: ".$email;
						
			if (mail(	"sms@sozdavatel.ru", 
						$subject, 
						$message, 
						$headers	))
			{
				ShowNote(GetMessage("SMSBLISS_REGISTER_OK"));
				ShowNote(GetMessage("SMSBLISS_REGISTER_OK2"));
			}
			else
			{
				ShowError(GetMessage("SMSBLISS_REGISTER_ERROR"));
			}
		}
	}
?></div><br/><?

foreach($arSMSBlissOptions as $arOption):
	__AdmSettingsDrawRow("sozdavatel.sms", $arOption);
endforeach;



// sms-kontakt
$tabControl->BeginNextTab();
?><div class="tab-text"><?
//    
$sender_id	= COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_ID");
$user_phone	= FormatPhone(COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_PHONE"));
$api_key	= COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_API_KEY");

if ((!!$sender_id)&&(!!$user_phone)&&(!!$api_key))
{
	$smskontakt = new SMSkontakt($sender_id, $user_phone, $api_key);
	$balance_result = $smskontakt->GetInfo('balance');
	$json_balance_result = json_decode($balance_result);
	$balance = $json_balance_result[0]->describe;
	
	if ($balance)
	{
		echo GetMessage("SMSKONTAKT_BALANCE").": ".$balance." ".GetMessage("RUB")." (<a href='https://secure.onpay.ru/pay/sms_kontakt?f=7&pay_mode=free&pay_for=".$user_phone."'
target='_blank'>".GetMessage("SMSKONTAKT_BALANCE_ADD")."</a>).<br/>";
	}
	
	$price_result = $smskontakt->GetInfo('personal_price');
	$json_price_result = json_decode($price_result);
	$price = $json_price_result[0]->describe;
	
	if ($price)
	{
		echo GetMessage("SMSKONTAKT_PRICE")." ".$price." ".GetMessage("RUB")."<br/>";
	}
}

if ($balance || $price)
	echo "<br/>";
echo GetMessage("SMSKONTAKT_INSTRUCTION");

?></div><?

//  API-key
if ($REQUEST_METHOD=="POST" && strlen($_REQUEST["SMSKONTAKT_GetApiKey"])>0 && $SMS_RIGHT >= "W" && check_bitrix_sessid())
{
	$sender_id	= COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_ID");
	$user_phone	= FormatPhone(COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_PHONE"));
	
	if (!$sender_id)
	{
		ShowError(GetMessage('SMSKONTAKT_ERR_EMPTY_PARAM').' "'.GetMessage("SMSKONTAKT_OPT_SENDER_ID").'"');
	}
	
	if (!$user_phone)
	{
		ShowError(GetMessage('SMSKONTAKT_ERR_EMPTY_PARAM').' "'.GetMessage("SMSKONTAKT_OPT_SENDER_PHONE").'"');
	}
	
	if ((!!$sender_id)&&(!!$user_phone))
	{
		$smskontakt = new SMSkontakt($sender_id, $user_phone);
		$result = $smskontakt->SendAPIKeyToUser();
		$json_result = json_decode($result);
		
		if ($json_result[0]->result == "success")
		{
			ShowNote(GetMessage('SMSKONTAKT_MSG_API_KEY_SENT')." ".$user_phone.".");
			ShowNote(GetMessage('SMSKONTAKT_MSG_API_KEY_SENT_2'));
		}
		else
		{
			ShowError(GetMessage('SMSKONTAKT_ERR_API_KEY_NOT_SENT'));
			ShowError($json_result[0]->describe);
			ShowError(GetMessage('SUPPORT'));
		}
	}
}

?><br/><?
//    
foreach($arSMSKontaktOptions as $arOption):
	__AdmSettingsDrawRow("sozdavatel.sms", $arOption);
endforeach;

?><input type="submit" name="SMSKONTAKT_GetApiKey" value=<?=GetMessage("SMSKONTAKT_OPT_GET_API_KEY_BUTTON")?>" title="<?=GetMessage("SMSKONTAKT_OPT_GET_API_KEY_TITLE")?>" class="smskontakt-getapikey-button"/><?

//  
$tabControl->BeginNextTab();
?><div class="tab-text"><br/><?=GetMessage("SMS_SUPPORT")?><br/><br/><?=GetMessage("SMS_EXAMPLE");?></div><?

// 
$tabControl->BeginNextTab();
?>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) 
{
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());	
}
?>


<? // 
$tabControl->BeginNextTab(); ?>
<div class="sms-log">
<?	
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/log.html"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/log.html"); 
?>
</div>

<? $tabControl->Buttons(); ?>
	<input <?if ($SMS_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input <?if ($SMS_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<? else: //if ($SMS_RIGHT >="R"):?>
	<?=CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));?>
<? endif;?>