<?
/**
 * Module settings.
 */

/*
 * Include some standard language constants.
 */
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "imaginweb.sms";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);

$arErrors = array();
$arMessages = array();
$arRequest = $_REQUEST;

if($POST_RIGHT >= "R"):
	if(CModule::IncludeModule("sale")) {
		$db_props = CSaleOrderProps::GetList(array("SORT" => "ASC"),array("TYPE" => array('TEXT','TEXTAREA')));
		$arSaleProps = array();
		$arReplaces = array();
		while($arProps = $db_props->Fetch()) {
			if((!isset($arSaleProps[$arProps['CODE']]) || !in_array($arProps['NAME'],$arSaleProps[$arProps['CODE']])) && strlen($arProps['CODE'])>0) $arSaleProps[$arProps['CODE']][] = $arProps['NAME'];
			if((!isset($arReplaces['PROP_'.$arProps['CODE']]) || !in_array($arProps['NAME'],$arReplaces['PROP_'.$arProps['CODE']])) && strlen($arProps['CODE'])>0) $arReplaces['PROP_'.$arProps['CODE']][] = $arProps['NAME'];
		}
		
		$obStatus = CSaleStatus::GetList();
		$arStatus = array();
		while($arStat = $obStatus->Fetch()) {
			$arStatus[$arStat['ID']][$arStat['LID']] = array(
				'NAME'		=> $arStat['NAME'],
				'DESCRIPTION'	=> $arStat['DESCRIPTION']
			);
		}
	}
	
	$obSite = CSite::GetList($by="sort", $order="desc");
	$arSites = array();
	while($arResult = $obSite->Fetch()) {
		$arSites[$arResult['ID']] = $arResult['NAME'];
	}
	$arFields = Array(
		"ENTITY_ID" => "USER",
		"USER_TYPE_ID" => "string",
		'LANG' => LANGUAGE_ID
	);
	
	$obUserFields = CUserTypeEntity::GetList( array($by=>$order), $arFields );
	$arUserFields = array();
	while($arRes = $obUserFields->Fetch())
	{
		$arUserFields[] = $arRes;
	}
	$arGroups = array();
	$obGroups = CGroup::GetList(($by="c_sort"), ($order="desc") ); // выбираем группы
	while($arGr = $obGroups->Fetch()) {
		$arGroups[$arGr['ID']] = $arGr['NAME'];
	}
	
	
	//COption::SetOptionString('imaginweb.sms', 'host', 'http://gate.mobilmoney.ru/');
	//COption::SetOptionString('imaginweb.sms', 'host2', 'http://turbosms.in.ua/api/wsdl.html');
	//COption::SetOptionString('imaginweb.sms', 'host3', 'http://atompark.com/members/sms/xml.php');
	
	//require_once dirname(__FILE__).'/classes/lib/nusoap.php';
	require_once dirname(__FILE__).'/classes/iweb/Sender.php';
	
	$rsUser = CUser::GetByID(1);
    $arUser = $rsUser->Fetch();
    foreach($arUser as $key => $value){
        if(substr($key, 0, 8) == "PERSONAL" || substr($key, 0, 4) == "WORK" || substr($key, 0, 2) == "UF"){
			$arFieldsUser[$key] = $key;
        }
    }
	
	# settings
	$arAllOptions = array(
		array("allow_anonymous", GetMessage("opt_anonym"), array("checkbox", "Y")),
		array("show_auth_links", GetMessage("opt_links"), array("checkbox", "Y")),
		array("subscribe_max_lenght", GetMessage("subscribe_max_lenght"), array("text", 10)),
		array("subscribe_field_phone", GetMessage("subscribe_field_phone"), array("selectbox", $arFieldsUser)),
		array("posting_interval", GetMessage("opt_interval"), array("text", 5)),
		array("default_from", GetMessage("opt_def_from"), array("text", 35)),
		array("subscribe_auto_method", GetMessage("opt_method"), array("selectbox", array("agent" => GetMessage("opt_method_agent"), "cron" => GetMessage("opt_method_cron")))),
		array("subscribe_max_sms_per_hit", GetMessage("opt_max_per_hit"), array("text", 5))
	);
	
	$tabs = array(
			array(
				"DIV"   => 'shop',
				"TAB"   => (CModule::IncludeModule("sale"))?GetMessage("IMAGINWEB_SMS_INTERNET_MAGAZIN"):GetMessage("IMAGINWEB_SMS_OSNOVNYE_NASTROYKI"),
				"ICON"  => '',
				"TITLE" => (CModule::IncludeModule("sale"))?GetMessage("IMAGINWEB_SMS_NASTROYKI_INTERNET_M"):GetMessage("IMAGINWEB_SMS_OSNOVNYE_NASTROYKI"),
			),
			array(
				"DIV"   => 'shop-call',
				"TAB"   => (CModule::IncludeModule("sale"))?GetMessage("IMAGINWEB_SMS_INTERNET_MAGAZIN_ZV"):GetMessage("IMAGINWEB_SMS_OSNOVNYE_NASTROYKI1"),
				"ICON"  => '',
				"TITLE" => (CModule::IncludeModule("sale"))?GetMessage("IMAGINWEB_SMS_NASTROYKI_INTERNET_M"):GetMessage("IMAGINWEB_SMS_OSNOVNYE_NASTROYKI"),
			),
			array(
				"DIV"   => 'settings',
				"TAB"   => GetMessage("IMAGINWEB_SMS_SETTINGS"),
				"ICON"  => '',
				"TITLE" => GetMessage("IMAGINWEB_SMS_SETTING_PARAMS")
			),
			array(
				"DIV"   => 'gates',
				"TAB"   => GetMessage("IMAGINWEB_SMS_NASTROYKI_SLUZOV"),
				"ICON"  => '',
				"TITLE" => GetMessage("IMAGINWEB_SMS_NASTROYKI_SLUZOV")
			),
			array(
				"DIV"   => 'send',
				"TAB"   => GetMessage("IMAGINWEB_SMS_OTPRAVKA_SOOBSENIA"),
				"ICON"  => '',
				"TITLE" => GetMessage("IMAGINWEB_SMS_OTPRAVKA_SOOBSENIA")
			),
			array(
				"DIV"   => 'help',
				"TAB"   => GetMessage("IMAGINWEB_SMS_OPISANIE"),
				"ICON"  => '',
				"TITLE" => GetMessage("IMAGINWEB_SMS_OPISANIE")." api ".GetMessage("IMAGINWEB_SMS_I_FUNKCIONALA")
			),
			array(
				"DIV"   => 'rights',
				"TAB"   => GetMessage("IMAGINWEB_SMS_DOSTUP"),
				"ICON"  => '',
				"TITLE" => GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")
			),
	);
	$tabControl = new CAdminTabControl("iwebSMSSettings", $tabs);
	#debmes($_REQUEST);
	// отправка сообщений
	if($REQUEST_METHOD == "POST" && isset($_REQUEST["Send"]) && $POST_RIGHT == "W" && check_bitrix_sessid())
	{
		if(CModule::IncludeModule("imaginweb.sms"))
		{
			if(strlen(trim($arRequest["TO_FIELD"])) <= 0) $arErrors[] = GetMessage("IMAGINWEB_SMS_NED_TO_FIELD");
			elseif(!CIWebSMS::CheckPhoneNumber($arRequest["TO_FIELD"])) $arErrors[] = GetMessage("IMAGINWEB_SMS_WRONG_PHONE");
			if(strlen(trim($arRequest["BODY"])) <= 0) $arErrors[] = GetMessage("IMAGINWEB_SMS_NED_BODY");
			
			if(!$arErrors)
			{
				$sms = new CIWebSMS;
				$phone = $arRequest["TO_FIELD"];
				$arSendParams = array();
				if(strlen(trim($arRequest["FROM_FIELD"])) > 0)
					$arSendParams["ORIGINATOR"] = trim($arRequest["FROM_FIELD"]);
				
				$sms->Send($phone,$arRequest["BODY"],$arSendParams);
				//print_r($sms->return_mess);
				$arMessages[] = GetMessage("IMAGINWEB_SMS_MESS_SENT");
				unset($arRequest);
			}
		}
	}
	
	// отправка звонка
	if($REQUEST_METHOD == "POST" && isset($_REQUEST["Send_CALL"]) && $POST_RIGHT == "W" && check_bitrix_sessid())
	{
		if(CModule::IncludeModule("imaginweb.sms"))
		{
			if(strlen(trim($arRequest["TO_FIELD_CALL"])) <= 0) $arErrors[] = GetMessage("IMAGINWEB_SMS_NED_TO_FIELD");
			elseif(!CIWebSMS::CheckPhoneNumber($arRequest["TO_FIELD_CALL"])) $arErrors[] = GetMessage("IMAGINWEB_SMS_WRONG_PHONE");
			if(strlen(trim($arRequest["BODY_CALL"])) <= 0) $arErrors[] = GetMessage("IMAGINWEB_SMS_NED_BODY");
			
			if(!$arErrors)
			{
				$sms = new CIWebSMS;
				$phone = $arRequest["TO_FIELD_CALL"];
				$arSendParams = array();
				if(strlen(trim($arRequest["FROM_FIELD_CALL"])) > 0)
					$arSendParams["ORIGINATOR"] = trim($arRequest["FROM_FIELD_CALL"]);
				
				$sms->SendCall($phone,$arRequest["BODY_CALL"],$arSendParams);
				//print_r($sms->return_mess);
				$arMessages[] = GetMessage("IMAGINWEB_SMS_ZVONOK_POSTAVLEN_V_O");
				unset($arRequest);
			}
		}
	}
	
	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $POST_RIGHT == "W" && check_bitrix_sessid()){
		
		if(strlen($RestoreDefaults) > 0){
			COption::RemoveOption("imaginweb.sms");
			$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
			while($zr = $z->Fetch()){
				$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
			}	
		}
		else{
			foreach($arAllOptions as $arOption){
				$name = $arOption[0];
				if($arOption[2][0]=="text-list"){
					$val = "";
					for($j = 0; $j < count($$name); $j++){
						if(strlen(trim(${$name}[$j])) > 0){
							$val .= ($val <> ""? ",":"").trim(${$name}[$j]);
						}
					}
				}
				else{
					$val=$$name;
				}
				if($arOption[2][0] == "checkbox" && $val <> "Y"){
					$val = "N";
				}
				if($USER->IsAdmin()){
					COption::SetOptionString($module_id, $name, $val);
				}	
			}
		}
		CAgent::RemoveAgent("CPostingTemplate::Execute();", "imaginweb.sms");
		if(COption::GetOptionString("imaginweb.sms", "subscribe_template_method") !== "cron"){
			CAgent::AddAgent("SMSCPostingTemplate::Execute();", "imaginweb.sms", "N", COption::GetOptionString("imaginweb.sms", "subscribe_template_interval"));
		}
		
		$Update = $Update.$Apply;
		ob_start();
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
		ob_end_clean();
		
		foreach ($_POST['settings'] as $settingName => $settingValue) {
			if($settingName == 'password' && strlen(trim($settingValue)) == 0) continue;
			if(substr($settingName,0,strlen('user_groups_iskl')) == 'user_groups_iskl') continue;
			COption::SetOptionString('imaginweb.sms', $settingName, $settingValue);
		}
		
		foreach($arGroups as $id => $name) {
			if(isset($_POST['settings']['user_groups_iskl'.$id])) {
				COption::SetOptionString('imaginweb.sms', 'user_groups_iskl'.$id, '1');
			} else {
				COption::SetOptionString('imaginweb.sms', 'user_groups_iskl'.$id, '0');
			}
		}
		
		if(!isset($_POST['settings']['tf'])) {
			COption::SetOptionString('imaginweb.sms', 'tf', '0');
		} else {
			COption::SetOptionString('imaginweb.sms', 'tf', '1');
		}
	    
		if(strlen($_REQUEST["back_url_settings"]) > 0){
            if(strlen($Apply) > 0 || strlen($RestoreDefaults) > 0){
				 LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
            }
            else{
				LocalRedirect($_REQUEST["back_url_settings"]);
            }   
        }
        else{
            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
        }
	}
	
	if(count($arErrors) > 0)
	{
		CAdminMessage::ShowMessage(implode("<br />", $arErrors));
	}
	if(count($arMessages) > 0)
	{
		CAdminMessage::ShowNote(implode("<br />", $arMessages));
	}
	
	$tabControl->Begin();?>
	<form
		name="iwebSMSSettingsForm"
		method="post"
		action="<?=$GLOBALS['APPLICATION']->GetCurPage() ?>?mid=<?=urlencode($mid) ?>&amp;lang=<?=LANGUAGE_ID ?>">
		
		<style>
			.imaginweb-description p {
				font-size: 100% !important;
				padding: 0 0 0 30px;
			}
			
			.imaginweb-description-2 p, .imaginweb-description-2 ul {
				font-size: 120% !important;
				/*padding: 0 0 0 30px;*/
			}
			.code {
				color: blue;
			}
			.font-sz100 code {
				font-size: 160% !important;
			}
			.imaginweb-description .heading {
				font-size: 120%;
				font-weight: bold;
				padding: 10px 0;
			}
			.imaginweb-description li {font-size: 100% !important;}
			.toggle-link {
				text-decoration:none;
				border-bottom:2px dotted #000;
				cursor: pointer;
			}
			.tal-right {
				text-align: right !important;
			}
			.iweb-medium {
				font-size: 100%;
			}
			.gateShow {
				/*display: none;*/
			}
			.gateLinkHead td {
				/*background-color:#2675D7;*/
				/*border*/
				/*border-bottom: 15px solid #2675D7 !important;*/
				/*border-top: 15px solid #2675D7 !important;*/
				border-bottom: 0px solid #666 !important;
				border-top: 15px solid #666 !important;
			}
			.sites table {
				width: 100%;
			}
		</style>
		<? $jsdir = '/bitrix/js/imaginweb.sms/js';?>
		<script src="<?=$jsdir?>/highlight/highlight.pack.js"></script>
		
		<link rel="stylesheet" title="Default" href="<?=$jsdir?>/highlight/styles/default.css">
		
		<script>
			if(!window.jQuery) {
				document.write('<script type=\"text\/javascript\" src=\"<?=$jsdir?>\/jquery-1.6.4.min.js\"><\/script>');
			} else {
				$(document).ready(function(){
					$('.balance').each(function(){
						var gateAttr = $(this).attr('gate');
						var obj = $(this);
						$.get("/bitrix/admin/imaginweb.sms_balans.php", { gate: gateAttr},
							function(data){
								$(obj).html(data);
						});
					});
					$('.user_password_field').change(function() {
						if($(this).val()=='OFF') {
							$('.user_fields').hide();
						} else {
							$('.user_fields').show();
						}
					})
					$(document).ready(function(){
				//		gateControll OLD
						$('.gateControll').click(function() {
							//alert($('this').parent('.header').html());
							$('.'+$(this).attr('ref')).slideToggle('normal');
						});
					});
				})
			}

			function iwebtoggle(id) {
				if(document.getElementById(id).style.display=='none')
					document.getElementById(id).style.display=''
				else
					document.getElementById(id).style.display='none';
			}
			
			function changeSite(val) {
				$('.sites').hide();
				document.getElementById('site'+val).style.display='';
			}


		</script>
		<script>
			hljs.tabReplace = '        ';
			hljs.initHighlightingOnLoad(); 
		</script>
	
	<? $tabControl->BeginNextTab() ?>
		<tr>
			<td colspan="2">
			<div class="tab-text"><?=GetMessage("IMAGINWEB_SMS_VOZMOJNOSTI")?></div>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_OBSIE_NASTROYKI")?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage("IMAGINWEB_SMS_UPRAVLENIE_SLUZOM_S")?><span style="color: red;">*</span></td>
			<td width="50%">
				<select name="settings[gate]">
					
					<option value="OFF" style="color:red;"><?=GetMessage("IMAGINWEB_SMS_RASSYLKA_OTKLUCENA")?></option>
					
					<option value="redsms.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'redsms.ru'))?' selected="selected"':''?>>redsms.ru</option>
					<option value="am4u.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'am4u.ru'))?' selected="selected"':''?>>am4u.ru</option>
					<option value="sms-sending.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'sms-sending.ru'))?' selected="selected"':''?>>sms-sending.ru</option>
		
					
					<option value="alfa-sms.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'alfa-sms.ru'))?' selected="selected"':''?>>alfa-sms.ru</option>
					<option value="mainsms.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'mainsms.ru'))?' selected="selected"':''?>>mainSMS.ru</option>
					<option value="kompeito.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'kompeito.ru'))?' selected="selected"':''?>>kompeito.ru</option>
					<option value="infosmska.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'infosmska.ru'))?' selected="selected"':''?>>infoSMSka.ru</option>
					<option value="bytehand.com" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'bytehand.com'))?' selected="selected"':''?>>bytehand.com</option>
					<option value="imobis" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'imobis'))?' selected="selected"':''?>>Imobis.ru</option>
					<option value="axtele.com" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'axtele.com'))?' selected="selected"':''?>>axtele.com</option>
					<option value="nssms.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'nssms.ru'))?' selected="selected"':''?>>nssms.ru</option>
					<option value="mobilmoney.ru" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == '') || (COption::GetOptionString('imaginweb.sms', 'gate') == 'mobilmoney.ru'))?' selected="selected"':''?>>mobilmoney.ru</option>
					<option value="turbosms.ua" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'turbosms.ua'))?' selected="selected"':''?>>turbosms.ua</option>
					<option value="epochtasms" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'epochtasms'))?' selected="selected"':''?>>ePochtaSMS</option>
					<option value="giper.mobi" <?=((COption::GetOptionString('imaginweb.sms', 'gate') == 'giper.mobi'))?' selected="selected"':''?>>giper.mobi</option>
				</select>
			</td>
		</tr>
		<? if(CModule::IncludeModule("sale")) :?>
			<tr>
				<td width="50%"><a href="/bitrix/admin/sale_order_props.php?lang=ru"><?=GetMessage("IMAGINWEB_SMS_KOD_SVOYSTVA_ZAKAZA")?></a><span style="color: red;">*</span></td>
				<td width="50%">
					<select name="settings[property_phone]">
					<? foreach($arSaleProps as $code => $names):?>
						<option value="<?=$code?>"<?=(COption::GetOptionString('imaginweb.sms', 'property_phone') == $code)?' selected="selected"':''?>><?='['.$code.'] '.implode(' | ',$names)?></option>
					<? endforeach;?>
					</select>
				</td>
			</tr>
		<? endif?>
			<tr>
				<td width="50%">
				<?=GetMessage("IMAGINWEB_SMS_SOHRANATQ_PAROLQ_V_S")?> SMS)<span style="color: red;">*</span><br/>
				<?=GetMessage("IMAGINWEB_SMS_PAROLI_SOHRANAUTSA_V")?>.
				</td>
				<td width="50%">
					
					<select name="settings[user_password_field]" class="user_password_field">
						<option value="OFF" style="color:red;"><?=GetMessage("IMAGINWEB_SMS_NE_SOHRANATQ")?></option>
						<? foreach($arUserFields as $field):?>
							<option value="<?=$field['FIELD_NAME']?>" <?=((COption::GetOptionString('imaginweb.sms', 'user_password_field') == $field['FIELD_NAME']))?' selected="selected"':''?>><?=$field['FIELD_NAME']?></option>
						<? endforeach;?>
					</select>
				</td>
			</tr>
		<? if(CModule::IncludeModule("sale")) :?>
			<tr class="heading user_fields" <?=(COption::GetOptionString('imaginweb.sms', 'user_password_field')=='OFF' || strlen(COption::GetOptionString('imaginweb.sms', 'user_password_field')) <=0)?'style="display:none"':''?>>
				<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_DOSTUPNY_REGISTRACIO")?>: #LOGIN# - <?=GetMessage("IMAGINWEB_SMS_LOGIN1")?>, #PASSWORD# - <?=GetMessage("IMAGINWEB_SMS_PAROLQ1")?>, #EMAIL# - email, #NAME# - <?=GetMessage("IMAGINWEB_SMS_IMA")?>, #LAST_NAME# - <?=GetMessage("IMAGINWEB_SMS_FAMILIA")?>, #SECOND_NAME# - <?=GetMessage("IMAGINWEB_SMS_OTECESTVO_I_DRUGIE")?><a target="_blank" href="http://dev.1c-bitrix.ru/api_help/main/reference/cuser/index.php#fuser"><?=GetMessage("IMAGINWEB_SMS_POLA")?></a> <?=GetMessage("IMAGINWEB_SMS_V_TOM_CISLE_I_POLQZO")?>!)
				</td>
			</tr>
		<? endif?>
			<tr>
				<td width="50%"><?=GetMessage("IMAGINWEB_SMS_SAYT")?></td>
				<td width="50%">
					<select name="settings[site]" id='iwebsite' onchange='javascript:changeSite(this.value);'>
					<? foreach($arSites as $id => $value):?>
						<option value="<?=$id?>" <?=((COption::GetOptionString('imaginweb.sms', 'site') == $id))?' selected="selected"':''?>><?=$value?></option>
					<? endforeach;?>
					</select>
				</td>
			</tr>
		<? if(CModule::IncludeModule("sale")) :?>
			<tr class="heading">
				<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_TEKSTA_SOOBSENIY")?>
				</td>
			</tr>
		
			<tr id="add-field" style="display: none;" class="heading">
				
				<td width="50%" >
					<div class="tal-right">
						<strong>#STATUS_NAME#</strong> - <?=GetMessage("IMAGINWEB_SMS_STATUS_ZAKAZA")?><br/>
						<strong>#DELIVERY_NAME#</strong> - <?=GetMessage("IMAGINWEB_SMS_NAZVANIE_SLUJBY_DOST")?><br/>
						<strong>#DELIVERY_DOC_NUM#</strong> - <?=GetMessage("IMAGINWEB_SMS_NOMER_DOKUMENTA_OTGR")?><br/>
						<strong>#DELIVERY_DOC_DATE#</strong> - <?=GetMessage("IMAGINWEB_SMS_DATA_DOKUMENTA_OTGRU")?><br/>
						<? foreach($arReplaces as $code => $name):?>
							<strong>#<?=$code?>#</strong> - <?=implode(' | ',$name)?><br/>
						<? endforeach;?><br/><br/>
						<?=GetMessage("IMAGINWEB_SMS_UBEDITESQ_CTO_U_VSE")?><a href="/bitrix/admin/sale_order_props.php?lang=ru"><?=GetMessage("IMAGINWEB_SMS_SVOYSTV_ZAKAZA")?></a> <?=GetMessage("IMAGINWEB_SMS_ESTQ_MNEMONICESKIY")?>".
					</div>
				</td>
				<td width="50%">&nbsp</td>
			</tr>
			
			<tr class="heading">
				<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add-field')"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNYE_SABLO")?></a></td>
			</tr>
		<? endif?>
		<? foreach($arSites as $id => $value):?>
			<tr id="site<?=$id?>" <?=(COption::GetOptionString('imaginweb.sms', 'site')==$id)?'':'style="display:none"'?> class="sites">
				<td colspan="2">
				<table class="edit-table">
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA")?></strong><span style="color: red;">***</span><br/>
						(<?=GetMessage("IMAGINWEB_SMS_BUKVY_LATINSKOGO_ALF")?>: .&!*()-+=_ <?=GetMessage("IMAGINWEB_SMS_DLINNA_NE_DOLJNA_PRE")?><br/>
						<?=GetMessage("IMAGINWEB_SMS_ESLI_NE_UKAZANO_TO")?>
						</td>
						<td width="50%">
							<input name="settings[sender<?=$id?>]" value="<?=COption::GetOptionString('imaginweb.sms', 'sender'.$id) ?>" />
						</td>
					</tr>
				</table>
			<? if(CModule::IncludeModule("sale")) :?>
				<table class="edit-table">
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_NOVYY_ZAKAZ")?></strong><span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[new_order<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'new_order'.$id) ?></textarea>
						</td>
					</tr>
					<tr id="add_phone_new<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    name="settings[add_phone_new<?=$id?>]"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_new'.$id) ?>"/>
						</td>
					</tr>
					<tr id="new_order_2<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[new_order_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'new_order_2'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_new<?=$id?>');iwebtoggle('new_order_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS")?></a></td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_OPLATA_ZAKAZA")?></strong><span style="color: red;">**</span></td>
						<td width="50%">
						    <textarea name="settings[on_pay_order<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'on_pay_order'.$id) ?></textarea>
						</td>
					</tr>
					<tr id="add_phone_pay<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON1")?><span style="color: red;">**</span></td>
						<td width="50%">
							<input
								type="text"
								size="30"
								name="settings[add_phone_pay<?=$id?>]"
								value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_pay'.$id)?>"/>
						</td>
					</tr>
					<tr id="on_pay_order_2<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE1")?><span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[on_pay_order_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'on_pay_order_2'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_pay<?=$id?>');iwebtoggle('on_pay_order_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS1")?></a></td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_OTMENA_ZAKAZA")?></strong><span style="color: red;">**</span></td>
						<td width="50%">
						    <textarea name="settings[order_cancel<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'order_cancel'.$id) ?></textarea>
						</td>
					</tr>
					<tr id="add_phone_cancel<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    name="settings[add_phone_cancel<?=$id?>]"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_cancel'.$id) ?>"/>
						</td>
					</tr>
					<tr id="order_cancel_2<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[order_cancel_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'order_cancel_2'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_cancel<?=$id?>');iwebtoggle('order_cancel_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS2")?></a></td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_DOSTAVKA_RAZRESENA")?></strong><span style="color: red;">**</span></td>
						<td width="50%">
						    <textarea name="settings[order_delivery<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'order_delivery'.$id) ?></textarea>
						</td>
					</tr>
					<tr id="add_phone_delivery<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    name="settings[add_phone_delivery<?=$id?>]"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_delivery'.$id) ?>"/>
						</td>
					</tr>
					<tr id="order_delivery_2<?=$id?>" style="display: none;">
						<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[order_delivery_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'order_delivery_2'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_delivery<?=$id?>');iwebtoggle('order_delivery_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS3")?></a></td>
					</tr>
					
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr class="heading" id="statuses<?=$id?>" style="display: none;">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS4")?></td>
					</tr>
					<? foreach($arStatus as $statId => $val):?>
					<?
						$desc = $val[LANGUAGE_ID];
					?>
						<tr id="main_status_<?=$statId?><?=$id?>" style="display: none;">
							<td width="50%"><strong><?=$desc['NAME']?></strong><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[status_<?=$statId?><?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'status_'.$statId.$id)?></textarea>
							</td>
						</tr>
						<tr id="add_phone_status_<?=$statId?><?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
							<td width="50%">
							    <input
								    type="text"
								    size="30"
								    name="settings[add_phone_status_<?=$statId?><?=$id?>]"
								    value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_status_'.$statId.$id) ?>"/>
							</td>
						</tr>
						<tr id="status_<?=$statId?>_2<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[status_<?=$statId?>_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'status_'.$statId.'_2'.$id) ?></textarea>
							</td>
						</tr>
						<tr class="heading"  id="main_status_dop_<?=$statId?><?=$id?>" style="display: none;">
							<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_status_<?=$statId?><?=$id?>');iwebtoggle('status_<?=$statId?>_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS5")?><?=$desc['NAME']?>"</a></td>
						</tr>
					<? endforeach;?>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:<? foreach($arStatus as $statId => $val):?>iwebtoggle('main_status_<?=$statId?><?=$id?>');iwebtoggle('main_status_dop_<?=$statId?><?=$id?>');<? endforeach;?>iwebtoggle('statuses<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS4")?></a></td>
					</tr>
					<? /*
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr class="heading">
						<td colspan="2">Оповещения при регистрации пользователя (Шаблоны: #LOGIN# - Логин, #PASSWORD# - Пароль, #EMAIL# - email, #NAME# - Имя, #LAST_NAME# - Фамилия, #SECOND_NAME# - Отечество.)</td>
					</tr>
					<tr>
						<td width="50%" class="field-name">Регистрация пользователя<span style="color: red;">**</span></td>
						<td width="50%">
						    <textarea name="settings[user_register<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'user_register'.$id) ?></textarea>
						</td>
					</tr>
					<tr id="add_phone_user_register<?=$id?>" style="display: none;">
						<td width="50%" class="field-name">Телефон<span style="color: red;">**</span></td>
						<td width="50%">
							<input
								type="text"
								size="30"
								name="settings[add_phone_user_register<?=$id?>]"
								value="<?=COption::GetOptionString('imaginweb.sms', 'add_phone_user_register'.$id) ?>"/>
						</td>
					</tr>
					<tr id="order_user_register_2<?=$id?>" style="display: none;">
						<td width="50%" class="field-name">Сообщение при регистрации<span style="color: red;">**</span></td>
						<td width="50%">
							<textarea name="settings[order_user_register_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'order_user_register_2'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('add_phone_user_register<?=$id?>');iwebtoggle('order_user_register_2<?=$id?>');">Дополнительное сообщение при регистрации пользователя</a></td>
					</tr>
					   */?>
				</td>
				</table>
				<? endif?>
			</tr>
		<? endforeach;?>
		<? if(CModule::IncludeModule("sale")):?>
			<tr>
				<td colspan="2"><span style="color: red;">*</span> - <?=GetMessage("IMAGINWEB_SMS_OBAZATELQNYE_POLA")?><br/>
					<span style="color: red;">**</span> - <?=GetMessage("IMAGINWEB_SMS_PRIMECHANIE")?><br/>
					<span style="color: red;">***</span> - <?=GetMessage("IMAGINWEB_SMS_ESLI_NE_UKAZANO_BERE")?>
				</td>
			</tr>
		<? else: ?>
			<tr>
				<td colspan="2" style="font-size: 14px; color: red;"><?=GetMessage("IMAGINWEB_SMS_NE_USTANOVLEN_MODULQ")?> SMS <?=GetMessage("IMAGINWEB_SMS_VOZMOJNA_TOLQKO_CERE")?> API <?=GetMessage("IMAGINWEB_SMS_PODROBNEE_NA_VKLADKE")?></td>
			</tr>
		<? endif;?>
		
	<? //звонок///////////////////////////////////////////////////////////////////////////////////////////?>
	<? $tabControl->BeginNextTab() ?>
		<tr>
			<td colspan="2">
			<div class="tab-text"><?=GetMessage("IMAGINWEB_SMS_SOVERSAYTE_AVTOMATIC")?><br/>
			<?=GetMessage("IMAGINWEB_SMS_ZVONKI_MOGUT_OSUSEST")?>!
			</div>
			<? #debmes(phpversion(),PHP_VERSION_ID);
			#debmes(intval(substr(PHP_VERSION_ID,0,3)),CIWebSMS::checkPhpVer53());
			?>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_OBSIE_NASTROYKI")?></td>
		</tr>
		<tr>
			<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_VAS_KLUC_VYSYLAETSA")?> email <?=GetMessage("IMAGINWEB_SMS_POSLE_REGISTRACII")?></td>
			<td width="50%"><input name="settings[call_key]" type="password" value="<?=COption::GetOptionString('imaginweb.sms', 'call_key') ?>" /></td>
		</tr>
		<tr class="heading">
			<td colspan="2"><a target="_blank" href="http://imaginweb.ru/zvonki/"><?=GetMessage("IMAGINWEB_SMS_ZAREGISTRIROVATQSA")?></a></td>
		</tr>
		<? if(CIWebSMS::checkPhpVer53()):?>
			<tr>
				<td width="50%"><?=GetMessage("IMAGINWEB_SMS_VKLUCITQ_OTKLUCITQ_Z")?><span style="color: red;">*</span></td>
				<td width="50%">
					<select name="settings[call_gate]">
						
						<option value="OFF" style="color:red;"><?=GetMessage("IMAGINWEB_SMS_ZVONKI_OTKLUCENY")?></option>
						
						<option value="ON" <?=((COption::GetOptionString('imaginweb.sms', 'call_gate') == 'ON'))?' selected="selected"':''?>><?=GetMessage("IMAGINWEB_SMS_ZVONKI_VKLUCENY")?></option>
					</select>
				</td>
			</tr>
			<? if(CModule::IncludeModule("sale")) :?>
				<tr>
					<td width="50%"><a href="/bitrix/admin/sale_order_props.php?lang=ru"><?=GetMessage("IMAGINWEB_SMS_KOD_SVOYSTVA_ZAKAZA")?></a><span style="color: red;">*</span></td>
					<td width="50%">
						<select name="settings[call_property_phone]">
						<? foreach($arSaleProps as $code => $names):?>
							<option value="<?=$code?>"<?=(COption::GetOptionString('imaginweb.sms', 'call_property_phone') == $code)?' selected="selected"':''?>><?='['.$code.'] '.implode(' | ',$names)?></option>
						<? endforeach;?>
						</select>
					</td>
				</tr>
			<? endif?>
				<tr>
					<td width="50%">
					<?=GetMessage("IMAGINWEB_SMS_SOHRANATQ_PAROLQ_V_S")?> SMS)<span style="color: red;">*</span><br/>
					<?=GetMessage("IMAGINWEB_SMS_PAROLI_SOHRANAUTSA_V")?>.
					</td>
					<td width="50%">
						
						<select name="settings[user_password_field]" class="user_password_field">
							<option value="OFF" style="color:red;"><?=GetMessage("IMAGINWEB_SMS_NE_SOHRANATQ")?></option>
							<? foreach($arUserFields as $field):?>
								<option value="<?=$field['FIELD_NAME']?>" <?=((COption::GetOptionString('imaginweb.sms', 'user_password_field') == $field['FIELD_NAME']))?' selected="selected"':''?>><?=$field['FIELD_NAME']?></option>
							<? endforeach;?>
						</select>
					</td>
				</tr>
			<? if(CModule::IncludeModule("sale")) :?>
				<tr class="heading user_fields" <?=(COption::GetOptionString('imaginweb.sms', 'user_password_field')=='OFF' || strlen(COption::GetOptionString('imaginweb.sms', 'user_password_field')) <=0)?'style="display:none"':''?>>
					<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_DOSTUPNY_REGISTRACIO")?>: #LOGIN# - <?=GetMessage("IMAGINWEB_SMS_LOGIN1")?>, #PASSWORD# - <?=GetMessage("IMAGINWEB_SMS_PAROLQ1")?>, #EMAIL# - email, #NAME# - <?=GetMessage("IMAGINWEB_SMS_IMA")?>, #LAST_NAME# - <?=GetMessage("IMAGINWEB_SMS_FAMILIA")?>, #SECOND_NAME# - <?=GetMessage("IMAGINWEB_SMS_OTECESTVO_I_DRUGIE")?><a target="_blank" href="http://dev.1c-bitrix.ru/api_help/main/reference/cuser/index.php#fuser"><?=GetMessage("IMAGINWEB_SMS_POLA")?></a> <?=GetMessage("IMAGINWEB_SMS_V_TOM_CISLE_I_POLQZO")?>!)
					</td>
				</tr>
			<? endif?>
				<tr>
					<td width="50%"><?=GetMessage("IMAGINWEB_SMS_SAYT")?></td>
					<td width="50%">
						<select name="settings[call_site]" id='iwebsite' onchange='javascript:changeSite(this.value);'>
						<? foreach($arSites as $id => $value):?>
							<option value="<?=$id?>" <?=((COption::GetOptionString('imaginweb.sms', 'call_site') == $id))?' selected="selected"':''?>><?=$value?></option>
						<? endforeach;?>
						</select>
					</td>
				</tr>
			<? if(CModule::IncludeModule("sale")) :?>
				<tr class="heading">
					<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_TEKSTA_SOOBSENIY")?>
					</td>
				</tr>
			
				<tr id="call-add-field" style="display: none;" class="heading">
					
					<td width="50%" >
						<div class="tal-right">
							<strong>#STATUS_NAME#</strong> - <?=GetMessage("IMAGINWEB_SMS_STATUS_ZAKAZA")?><br/>
							<strong>#DELIVERY_NAME#</strong> - <?=GetMessage("IMAGINWEB_SMS_NAZVANIE_SLUJBY_DOST")?><br/>
							<strong>#DELIVERY_DOC_NUM#</strong> - <?=GetMessage("IMAGINWEB_SMS_NOMER_DOKUMENTA_OTGR")?><br/>
							<strong>#DELIVERY_DOC_DATE#</strong> - <?=GetMessage("IMAGINWEB_SMS_DATA_DOKUMENTA_OTGRU")?><br/>
							<? foreach($arReplaces as $code => $name):?>
								<strong>#<?=$code?>#</strong> - <?=implode(' | ',$name)?><br/>
							<? endforeach;?><br/><br/>
							<?=GetMessage("IMAGINWEB_SMS_UBEDITESQ_CTO_U_VSE")?><a href="/bitrix/admin/sale_order_props.php?lang=ru"><?=GetMessage("IMAGINWEB_SMS_SVOYSTV_ZAKAZA")?></a> <?=GetMessage("IMAGINWEB_SMS_ESTQ_MNEMONICESKIY")?>".
						</div>
					</td>
					<td width="50%">&nbsp</td>
				</tr>
				
				<tr class="heading">
					<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add-field')"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNYE_SABLO")?></a></td>
				</tr>
			<? endif?>
			<? foreach($arSites as $id => $value):?>
				<tr id="site<?=$id?>" <?=(COption::GetOptionString('imaginweb.sms', 'call_site')==$id)?'':'style="display:none"'?> class="sites">
					<td colspan="2">
					<table class="edit-table">
						<tr>
							<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_NOMER_TELEFONA_ISHOD")?></strong><span style="color: red;">***</span><br/><?=GetMessage("IMAGINWEB_SMS_DESATQ_ZNAKOV_NAPRI")?> 74955438162<br/><?=GetMessage("IMAGINWEB_SMS_PREDVARITELQNO_DOLJE")?> bitcall.ru
							</td>
							<td width="50%">
								<input name="settings[call_sender<?=$id?>]" value="<?=COption::GetOptionString('imaginweb.sms', 'call_sender'.$id) ?>" />
							</td>
						</tr>
					</table>
				<? if(CModule::IncludeModule("sale")) :?>
					<table class="edit-table">
						<tr>
							<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_NOVYY_ZAKAZ")?></strong><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[call_new_order<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_new_order'.$id) ?></textarea>
							</td>
						</tr>
						<tr id="call-add_phone_new<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
							<td width="50%">
							    <input
								    type="text"
								    size="30"
								    name="settings[call_add_phone_new<?=$id?>]"
								    value="<?=COption::GetOptionString('imaginweb.sms', 'call_add_phone_new'.$id) ?>"/>
							</td>
						</tr>
						<tr id="call-new_order_2<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[call_new_order_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_new_order_2'.$id) ?></textarea>
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add_phone_new<?=$id?>');iwebtoggle('call-new_order_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS")?></a></td>
						</tr>
						<tr>
							<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_OPLATA_ZAKAZA")?></strong><span style="color: red;">**</span></td>
							<td width="50%">
							    <textarea name="settings[call_on_pay_order<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_on_pay_order'.$id) ?></textarea>
							</td>
						</tr>
						<tr id="call-add_phone_pay<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON1")?><span style="color: red;">**</span></td>
							<td width="50%">
								<input
									type="text"
									size="30"
									name="settings[call_add_phone_pay<?=$id?>]"
									value="<?=COption::GetOptionString('imaginweb.sms', 'call_add_phone_pay'.$id)?>"/>
							</td>
						</tr>
						<tr id="call-on_pay_order_2<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE1")?><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[call_on_pay_order_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_on_pay_order_2'.$id) ?></textarea>
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add_phone_pay<?=$id?>');iwebtoggle('call-on_pay_order_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS1")?></a></td>
						</tr>
						<tr>
							<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_OTMENA_ZAKAZA")?></strong><span style="color: red;">**</span></td>
							<td width="50%">
							    <textarea name="settings[call_order_cancel<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_order_cancel'.$id) ?></textarea>
							</td>
						</tr>
						<tr id="call-add_phone_cancel<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
							<td width="50%">
							    <input
								    type="text"
								    size="30"
								    name="settings[call_add_phone_cancel<?=$id?>]"
								    value="<?=COption::GetOptionString('imaginweb.sms', 'call_add_phone_cancel'.$id) ?>"/>
							</td>
						</tr>
						<tr id="call-order_cancel_2<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[call_order_cancel_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_order_cancel_2'.$id) ?></textarea>
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add_phone_cancel<?=$id?>');iwebtoggle('call-order_cancel_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS2")?></a></td>
						</tr>
						<tr>
							<td width="50%" class="field-name"><strong><?=GetMessage("IMAGINWEB_SMS_DOSTAVKA_RAZRESENA")?></strong><span style="color: red;">**</span></td>
							<td width="50%">
							    <textarea name="settings[call_order_delivery<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_order_delivery'.$id) ?></textarea>
							</td>
						</tr>
						<tr id="call-add_phone_delivery<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
							<td width="50%">
							    <input
								    type="text"
								    size="30"
								    name="settings[call_add_phone_delivery<?=$id?>]"
								    value="<?=COption::GetOptionString('imaginweb.sms', 'call_add_phone_delivery'.$id) ?>"/>
							</td>
						</tr>
						<tr id="call-order_delivery_2<?=$id?>" style="display: none;">
							<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
							<td width="50%">
								<textarea name="settings[call_order_delivery_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_order_delivery_2'.$id) ?></textarea>
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add_phone_delivery<?=$id?>');iwebtoggle('call-order_delivery_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS3")?></a></td>
						</tr>
						
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr class="heading" id="call-statuses<?=$id?>" style="display: none;">
							<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS4")?></td>
						</tr>
						<? foreach($arStatus as $statId => $val):?>
						<?
							$desc = $val[LANGUAGE_ID];
						?>
							<tr id="call-main_status_<?=$statId?><?=$id?>" style="display: none;">
								<td width="50%"><strong><?=$desc['NAME']?></strong><span style="color: red;">**</span></td>
								<td width="50%">
									<textarea name="settings[call_status_<?=$statId?><?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_status_'.$statId.$id)?></textarea>
								</td>
							</tr>
							<tr id="call-add_phone_status_<?=$statId?><?=$id?>" style="display: none;">
								<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_TELEFON")?><span style="color: red;">**</span></td>
								<td width="50%">
								    <input
									    type="text"
									    size="30"
									    name="settings[call_add_phone_status_<?=$statId?><?=$id?>]"
									    value="<?=COption::GetOptionString('imaginweb.sms', 'call_add_phone_status_'.$statId.$id) ?>"/>
								</td>
							</tr>
							<tr id="call-status_<?=$statId?>_2<?=$id?>" style="display: none;">
								<td width="50%" class="field-name"><?=GetMessage("IMAGINWEB_SMS_SOOBSENIE")?><span style="color: red;">**</span></td>
								<td width="50%">
									<textarea name="settings[call_status_<?=$statId?>_2<?=$id?>]" cols="40" rows="5" wrap="SOFT"><?=COption::GetOptionString('imaginweb.sms', 'call_status_'.$statId.'_2'.$id) ?></textarea>
								</td>
							</tr>
							<tr class="heading"  id="call-main_status_dop_<?=$statId?><?=$id?>" style="display: none;">
								<td colspan="2"><a class="toggle-link" href="javascript:iwebtoggle('call-add_phone_status_<?=$statId?><?=$id?>');iwebtoggle('call-status_<?=$statId?>_2<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS5")?><?=$desc['NAME']?>"</a></td>
							</tr>
						<? endforeach;?>
						<tr class="heading">
							<td colspan="2"><a class="toggle-link" href="javascript:<? foreach($arStatus as $statId => $val):?>iwebtoggle('call-main_status_<?=$statId?><?=$id?>');iwebtoggle('call-main_status_dop_<?=$statId?><?=$id?>');<? endforeach;?>iwebtoggle('call-statuses<?=$id?>');"><?=GetMessage("IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS4")?></a></td>
						</tr>
					</td>
					</table>
					<? endif?>
				</tr>
			<? endforeach;?>
			<? if(CModule::IncludeModule("sale")):?>
				<tr>
					<td colspan="2"><span style="color: red;">*</span> - <?=GetMessage("IMAGINWEB_SMS_OBAZATELQNYE_POLA")?><br/>
						<span style="color: red;">**</span> - <?=GetMessage("IMAGINWEB_SMS_PRIMECHANIE")?><br/>
						<span style="color: red;">***</span> - <?=GetMessage("IMAGINWEB_SMS_ESLI_NE_UKAZANO_BERE")?>
					</td>
				</tr>
			<? else: ?>
				<tr>
					<td colspan="2" style="font-size: 14px; color: red;"><?=GetMessage("IMAGINWEB_SMS_NE_USTANOVLEN_MODULQ")?> SMS <?=GetMessage("IMAGINWEB_SMS_VOZMOJNA_TOLQKO_CERE")?> API <?=GetMessage("IMAGINWEB_SMS_PODROBNEE_NA_VKLADKE")?></td>
				</tr>
			<? endif;?>
		<? else:?>
			<tr>
				<td colspan="2" style="font-size: 14px; color: red;"><?=GetMessage("IMAGINWEB_SMS_TREBUETSA_VERSIA")?> php <?=GetMessage("IMAGINWEB_SMS_NE_NIJE")?> 5.3 <?=GetMessage("IMAGINWEB_SMS_OBRATITESQ_POJALUYST")?><br/>
<?=GetMessage("IMAGINWEB_SMS_DLA_RABOTY_TREBUETSA")?> OpenSSL <?=GetMessage("IMAGINWEB_SMS_I_ODNO_IZ_DVUH_RASSI")?>: SOAP <?=GetMessage("IMAGINWEB_SMS_ILI")?> cURL. 
				</td>
			</tr>
		<? endif?>
	<!--settings-->
	<?$tabControl->BeginNextTab();?>
		<?foreach($arAllOptions as $Option):?>
			<?
			$type = $Option[2];
			$val = COption::GetOptionString($module_id, $Option[0]);
			?>
			<tr>
				<td valign="top" width="50%">
					<?if($type[0] == "checkbox"):?>
						<label for="<?=htmlspecialchars($Option[0]);?>"><?=$Option[1];?></label>
					<?else:?>
						<?=$Option[1];?>
					<?endif;?>
				</td>
				<td valign="middle" width="50%">
					<?if($type[0] == "checkbox"):?>
						<input type="checkbox" name="<?=htmlspecialchars($Option[0]);?>" id="<?=htmlspecialchars($Option[0]);?>" value="Y"<?if($val == "Y") echo" checked";?>>
					<?elseif($type[0] == "text"):?>
						<?if($Option[0] == "default_from"):?>
							<input type="text" size="<?=$type[1];?>" maxlength="11" value="<?=htmlspecialchars($val)?>" name="<?=htmlspecialchars($Option[0]);?>">
						<?else:?>
							<input type="text" size="<?=$type[1];?>" maxlength="255" value="<?=htmlspecialchars($val)?>" name="<?=htmlspecialchars($Option[0]);?>">
						<?endif;?>
					<?elseif($type[0] == "textarea"):?>
						<textarea rows="<?=$type[1]?>" cols="<?=$type[2];?>" name="<?=htmlspecialchars($Option[0])?>"><?=htmlspecialchars($val);?></textarea>
					<?elseif($type[0] == "text-list"):?>
						<?
						$aVal = explode(",", $val);
						?>
						<?for($j = 0; $j < count($aVal); $j++):?>
							<input type="text" size="<?=$type[2];?>" value="<?=htmlspecialchars($aVal[$j])?>" name="<?=htmlspecialchars($Option[0])."[]";?>"><br>
						<?endfor;?>
						<?for($j = 0; $j < $type[1]; $j++):?>
							<input type="text" size="<?=$type[2];?>" value="" name="<?=htmlspecialchars($Option[0])."[]";?>"><br>
						<?endfor;?>
					<?elseif($type[0] == "selectbox"):?>
						<?
						$arr = $type[1];
						$arr_keys = array_keys($arr);
						?>
						<select name="<?=htmlspecialchars($Option[0]);?>">
							<?for($j = 0; $j < count($arr_keys); $j++):?>
								<option value="<?=$arr_keys[$j];?>"<?if($val==$arr_keys[$j]) echo" selected";?>><?=htmlspecialchars($arr[$arr_keys[$j]]);?></option>
							<?endfor;?>
						</select>
					<?endif;?>
				</td>
			</tr>
		<?endforeach;?>
	
	<!-- Шлюзы -->
	
	<? $tabControl->BeginNextTab() ?>

		<!--redsms.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="redsms.ru"  href="javascript:iwebtoggle('redsms.ru')">redsms.ru</a></td>
		</tr>
		
		<tr class="sites gateShow redsms.ru" id="redsms.ru" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text">
								<p class='iweb-medium'>
									<?=GetMessage("IMAGINWEB_SMS_PROMO_REDSMS")?>
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator_redsms.ru'))?COption::GetOptionString('imaginweb.sms', 'originator_redsms.ru'):'TESTSMS' ?>"
								name="settings[originator_redsms.ru]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username_redsms.ru') ?>"
							    name="settings[username_redsms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password_redsms.ru')?>"
							    name="settings[password_redsms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="redsms.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
		
		<!--am4u.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="am4u"  href="javascript:iwebtoggle('am4u')">am4u.ru</a></td>
		</tr>
		
		<tr class="sites gateShow am4u" id="am4u" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text"><p class='iweb-medium'>
							 <?=GetMessage("IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH3")?>!!! 
    <br />
  <?=GetMessage("IMAGINWEB_SMS_DLA_POLUCENIA_SPEC_C")?><b><?=GetMessage("IMAGINWEB_SMS_PRI")?><a target="_blank" href="http://sms.am4u.ru/#regTab"><?=GetMessage("IMAGINWEB_SMS_REGISTRACII_V_SERVIS")?></a> <?=GetMessage("IMAGINWEB_SMS_UKAJITE_KOD_PRIGLASE")?></b> &quot;imaginweb&quot; <?=GetMessage("IMAGINWEB_SMS_ILI")?> &quot;<?=GetMessage("IMAGINWEB_SMS_IMEYDJIN_VEB")?>&quot; <?=GetMessage("IMAGINWEB_SMS_I_VAS_TARIF_AVTOMATI")?>.
    <br />
   <?=GetMessage("IMAGINWEB_SMS_ESLI_VY_UJE_POLQZUET")?> &quot;imaginweb&quot; <?=GetMessage("IMAGINWEB_SMS_NA_NOMER")?> +7 (902) 114-80-50 <?=GetMessage("IMAGINWEB_SMS_I_MENEDJER_POMENAET")?>.
							 </p>
							 <p class='iweb-medium'>
							<ul>
    <li>15 <?=GetMessage("IMAGINWEB_SMS_KOP_SMS_PO_ROSSII_V")?></li>
  
    <li>20 <?=GetMessage("IMAGINWEB_SMS_SMS_V_PODAROK_PRI_RE")?></li>
							</ul>
							</p>
							<p class='iweb-medium'>
<?=GetMessage("IMAGINWEB_SMS_SMS_RASSYLKI")?> &laquo;<?=GetMessage("IMAGINWEB_SMS_ALEF_MARKETING_SERVI")?>&raquo;
    <br />
  
    <br />
  <?=GetMessage("IMAGINWEB_SMS_TELEFON_PODDERJKI")?> 8 (800) 333-29-50
    <br />
   <?=GetMessage("IMAGINWEB_SMS_V_NASTROYKE_MODULA_U")?>,
    <br />
  <?=GetMessage("IMAGINWEB_SMS_NO_MY_NASTOATELQNO_R")?><a target="_blank" href="http://sms.am4u.ru/config"><?=GetMessage("IMAGINWEB_SMS_NASTROYKAH_BEZOPASNO")?></a> 
    <br />
  <?=GetMessage("IMAGINWEB_SMS_LICNOGO_KABINETA_TR")?> XML <?=GetMessage("IMAGINWEB_SMS_PROTOKOL_I_ISPOLQZ")?>.
							</p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator11'))?COption::GetOptionString('imaginweb.sms', 'originator11'):'AM4U.RUTEST' ?>"
								name="settings[originator11]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username11') ?>"
							    name="settings[username11]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password11')?>"
							    name="settings[password11]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="am4u.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
	
		<!--sms-sending.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="sms-sending.ru"  href="javascript:iwebtoggle('sms-sending.ru')">sms-sending.ru</a></td>
		</tr>
		
		<tr class="sites gateShow sms-sending.ru" id="sms-sending.ru" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text">
								<p class='iweb-medium'>
<?=GetMessage("IMAGINWEB_SMS_UDOBNYY_I_KACESTVENN")?> E-mail, <?=GetMessage("IMAGINWEB_SMS_UVEDOMLENIA_OB")?> Email, <?=GetMessage("IMAGINWEB_SMS_ONLAYN_KONSULQTANT")?>". 50 <?=GetMessage("IMAGINWEB_SMS_SMS_DLA_TESTA")?><br/><br/><?=GetMessage("IMAGINWEB_SMS_DLA_PODKLUCENIA_TARI")?>.
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator_sms-sending.ru'))?COption::GetOptionString('imaginweb.sms', 'originator_sms-sending.ru'):'SMS-TEST' ?>"
								name="settings[originator_sms-sending.ru]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username_sms-sending.ru') ?>"
							    name="settings[username_sms-sending.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password_sms-sending.ru')?>"
							    name="settings[password_sms-sending.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="sms-sending.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
	
		<!--alfa-sms.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="alfa-sms.ru"  href="javascript:iwebtoggle('alfa-sms.ru')">alfa-sms.ru</a></td>
		</tr>
		
		<tr class="sites gateShow alfa-sms.ru" id="alfa-sms.ru" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text">
								<p class='iweb-medium'>
									<?=GetMessage("IMAGINWEB_SMS_V_STADII_TESTIROVANI")?><br/><br/>
									<?=GetMessage("IMAGINWEB_SMS_DLA_PODKLUCENIA_TARI1")?> imaginweb .
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator_alfa-sms.ru'))?COption::GetOptionString('imaginweb.sms', 'originator_alfa-sms.ru'):'SMS-TEST' ?>"
								name="settings[originator_alfa-sms.ru]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username_alfa-sms.ru') ?>"
							    name="settings[username_alfa-sms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password_alfa-sms.ru')?>"
							    name="settings[password_alfa-sms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="alfa-sms.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
	
		<!--mainSMS.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="mainSMS"  href="javascript:iwebtoggle('mainSMS')">mainSMS.ru</a></td>
		</tr>
		
		<tr class="sites gateShow mainSMS" id="mainSMS" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text"><p class='iweb-medium'>
							<?=GetMessage("IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH")?><br/>
							<ul>
								<li>15 <?=GetMessage("IMAGINWEB_SMS_KOP_SMS_PO_ROSSII_V")?></li>
								<li>50 <?=GetMessage("IMAGINWEB_SMS_SMS_V_PODAROK_PRI_RE")?></li>
								<li>100 <?=GetMessage("IMAGINWEB_SMS_SMS_V_PODAROK_KAJDYY")?>.
									<a target="blank" href="http://mainsms.ru/home/benefits#answ2"><?=GetMessage("IMAGINWEB_SMS_PODROBNEE")?></a>
								</li>
								<li><?=GetMessage("IMAGINWEB_SMS_LUBOE_IMA_OTPRAVITEL")?></li>
							</ul>
							</p>
							
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator10'))?COption::GetOptionString('imaginweb.sms', 'originator10'):'' ?>"
								name="settings[originator10]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_NAZVANIE_PROEKTA_BE")?><a href="http://mainsms.ru/office/api_account" name="" target="_blank"><?=GetMessage("IMAGINWEB_SMS_SO_STRANICY")?></a></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username10') ?>"
							    name="settings[username10]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_KLUC_PROEKTA_BERETS")?><a href="http://mainsms.ru/office/api_account" name="" target="_blank"><?=GetMessage("IMAGINWEB_SMS_SO_STRANICY")?></a></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password10')?>"
							    name="settings[password10]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="mainsms.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--kompeito.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="kompeito" href="javascript:iwebtoggle('kompeito')">kompeito.ru</a></td>
		</tr>
		
		<tr class="sites gateShow kompeito" id="kompeito" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text"><p class='iweb-medium'>
							<?=GetMessage("IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH1")?><br/>
<?=GetMessage("IMAGINWEB_SMS_SERVIS")?> Kompeito &mdash; <?=GetMessage("IMAGINWEB_SMS_NE_ODIN_V_SVOEM_RODE")?><a href="http://kompeito.ru/advantage/" name="" target="_blank"><?=GetMessage("IMAGINWEB_SMS_PREIMUSESTVA1")?></a>, <?=GetMessage("IMAGINWEB_SMS_VYGODNO_OTLICAUSIE_O")?><br/><br/>
<?=GetMessage("IMAGINWEB_SMS_TELEFONY")?><br/>
8 (800) 555-36-50 - <?=GetMessage("IMAGINWEB_SMS_BESSLPATNO_PO_ROSSII")?><br/>
+7 (495) 258-39-73 - <?=GetMessage("IMAGINWEB_SMS_DLA_MOSKVY")?><br/><br/>
<?=GetMessage("IMAGINWEB_SMS_PODKLUCENIE_OBYCNOE")?><a href="https://cabinet.kompeito.ru/cabinet/public/register" name="" target="_blank"><?=GetMessage("IMAGINWEB_SMS_TUT")?></a>.
<?=GetMessage("IMAGINWEB_SMS_IMA_OTPRAVITELA_PO_U")?>: sms_test.<br/>
<?=GetMessage("IMAGINWEB_SMS_VVEDITE_V_POLE_NIJE")?>.
							</p>
							
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator9'))?COption::GetOptionString('imaginweb.sms', 'originator9'):'sms_test' ?>"
								name="settings[originator9]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username9') ?>"
							    name="settings[username9]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password9')?>"
							    name="settings[password9]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS")?></strong></td>
						<td width="50%" class="balance" gate="kompeito.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--infosmska.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="infosmska" href="javascript:iwebtoggle('infosmska')">InfoSMSka.ru</a></td>
		</tr>
		
		<tr class="sites gateShow infosmska" id="infosmska" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text"><p class='iweb-medium'><?=GetMessage("IMAGINWEB_SMS_OOO_INFOSMS_PREDLA")?> sms <?=GetMessage("IMAGINWEB_SMS_PAKETAMI_POPOLNAYTE")?><br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_KONTAKTY")?><br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_TEL")?>. 8-800-333-03-04 (<?=GetMessage("IMAGINWEB_SMS_PN_PT_S_DO_CASO")?>!)
			E-mail: help@infosmska.ru
			<br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_INSTRUKCIA")?><br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_DLA_ISPOLQZOVANIA_DA")?><a href="http://infosmska.ru">infosmska.ru</a> <?=GetMessage("IMAGINWEB_SMS_U_VAS_NA_BALANSE_BUD")?><br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_CTOBY_ISPOLQZOVATQ_S")?> infosmska.ru.
			<br/><br/>
			<?=GetMessage("IMAGINWEB_SMS_DLA_ETOGO_VOYDITE_V")?></p></div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator4') ?>"
								name="settings[originator4]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username4') ?>"
							    name="settings[username4]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password4')?>"
							    name="settings[password4]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUBLEY")?></strong></td>
						<td width="50%" class="balance" gate="infosmska.ru"></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_ISPOLQZOVATQ_RAZRESE")?></td>
						<td width="50%"><input type="checkbox" name="settings[tf]" value="<?=(COption::GetOptionString('imaginweb.sms', 'tf'))?'1':'0'?>" <?=(COption::GetOptionString('imaginweb.sms', 'tf'))?'checked="checked"':''?>/> </td>
					</tr>
				</table>
			</td>
		</tr>
		<? /* */?>
		<!--bytehand.com-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="bytehand" href="javascript:iwebtoggle('bytehand')">bytehand.com</a></td>
		</tr>
		
		<tr class="sites gateShow bytehand" id="bytehand" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2"><div class="tab-text"><p class='iweb-medium'>
			<?=GetMessage("IMAGINWEB_SMS_NAKOPITELQNYE_SKIDKI")?><br/>
			<?=GetMessage("IMAGINWEB_SMS_OPLATA_V_RUBLAH")?>, WM, <?=GetMessage("IMAGINWEB_SMS_ANDEKS_DENQGI_PLAST")?><br/>
			<a href="http://www.bytehand.com/" target="_blank">http://www.bytehand.com/</a><br/>
			<?=GetMessage("IMAGINWEB_SMS_POSLE_REGISTRCII_DO")?> 10 sms <?=GetMessage("IMAGINWEB_SMS_DLA_TESTA_NAKOPTELQ")?><br/>
			<?=GetMessage("IMAGINWEB_SMS_UNIKALQNAA_PARTNERSK")?> web-<?=GetMessage("IMAGINWEB_SMS_MASEROV_S_VOZMOJNOST")?><br/>
			<?=GetMessage("IMAGINWEB_SMS_OPERATIVNAA")?> online <?=GetMessage("IMAGINWEB_SMS_PODDERJKA")?></p></div></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator8') ?>"
								name="settings[originator8]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?> <a href="http://www.bytehand.com/secure/settings" name="" target="_blank"><?=GetMessage("IMAGINWEB_SMS_INFO_DLA_NASTROEK")?></a></td>
					</tr>
					<tr>
						<td width="50%">ID</td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username8') ?>"
							    name="settings[username8]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_KLUC")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password8')?>"
							    name="settings[password8]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUBLEY")?></strong></td>
						<td width="50%" class="balance" gate="bytehand.com"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--imobis-->
		<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="imobis" href="javascript:iwebtoggle('imobis')">imobis</a></td>
		</tr>
		
		<tr class="sites gateShow imobis" id="imobis" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator6') ?>"
								name="settings[originator6]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username6') ?>"
							    name="settings[username6]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password6')?>"
							    name="settings[password6]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUBLEY")?></strong></td>
						<td width="50%" class="balance" gate="imobis"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--axtele.com-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="axtele" href="javascript:iwebtoggle('axtele')">axtele.com</a></td>
		</tr>
		
		<tr class="sites gateShow axtele" id="axtele" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2"><div class="tab-text"><p class='iweb-medium'>
							<a target="_blank" href="http://www.axtele.com/">Axtelecom</a> <?=GetMessage("IMAGINWEB_SMS_IMEET_PRAMYE_PODKLUC")?>)
						</p></div></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator5') ?>"
								name="settings[originator5]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username5') ?>"
							    name="settings[username5]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password5')?>"
							    name="settings[password5]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUBLEY")?></strong></td>
						<td width="50%" class="balance" gate="axtele.com"></td>
					</tr>
				</table>
			</td>
		</tr>
		
		<!--nssms.ru-->
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="nssms.ru"  href="javascript:iwebtoggle('nssms.ru')">nssms.ru</a></td>
		</tr>
		
		<tr class="sites gateShow nssms.ru" id="nssms.ru" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tab-text">
								<p class='iweb-medium'>
									NSSMS.RU
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=(COption::GetOptionString('imaginweb.sms', 'originator_nssms.ru'))?COption::GetOptionString('imaginweb.sms', 'originator_nssms.ru'):'' ?>"
								name="settings[originator_nssms.ru]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username_nssms.ru') ?>"
							    name="settings[username_nssms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password_nssms.ru')?>"
							    name="settings[password_nssms.ru]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUB")?></strong></td>
						<td width="50%" class="balance" gate="nssms.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<!--mobilemoney.ru-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="mobilemoney" href="javascript:iwebtoggle('mobilemoney')">mobilemoney.ru</a></td>
		</tr>
		
		<tr class="sites gateShow mobilemoney" id="mobilemoney" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELQ")?></td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator') ?>"
								name="settings[originator]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'username') ?>"
								name="settings[username]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?> </td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password')?>"
							    name="settings[password]" />
						</td>
			
					</tr>
					<tr>
						<td width="50%"></td>
						<td width="50%" class="balance" gate="mobilmoney.ru"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--TurboSMS.ua-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="TurboSMS" href="javascript:iwebtoggle('TurboSMS')">TurboSMS.ua</a></td>
		</tr>
		
		<tr class="sites gateShow TurboSMS" id="TurboSMS" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELQ")?></td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator2') ?>"
								name="settings[originator2]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username2') ?>"
							    name="settings[username2]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password2')?>"
							    name="settings[password2]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_KREDITOV")?></strong></td>
						<td width="50%" class="balance" gate="turbosms.ua"></td>
					</tr>
				</table>
			</td>
		</tr>
	<!--epochtasms-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="epochtasms" href="javascript:iwebtoggle('epochtasms')">EpochtaSms</a></td>
		</tr>
		
		<tr class="sites gateShow epochtasms" id="epochtasms" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr>
						<td colspan="2"><div class="tab-text imaginweb-description-2">
							<p><?=GetMessage("IMAGINWEB_SMS_PREIMUSESTVA")?> ePochta SMS: </p>
							<ul>
							  <li>
							   10 <?=GetMessage("IMAGINWEB_SMS_SMS_DLA_TESTIROVANIA")?>
							   </li>
							  <li>
							    <?=GetMessage("IMAGINWEB_SMS_SAMAA_NIZKAA_CENA_DL")?>
							   </li>
							  <li>
							    <?=GetMessage("IMAGINWEB_SMS_CENA_DLA_ROSSII")?> &ndash; <?=GetMessage("IMAGINWEB_SMS_NE_BOLEE_KOPEEK")?>)
							   </li>
							  <li>
							    <?=GetMessage("IMAGINWEB_SMS_PODDERJIVAET_OTPRAVK")?> CDMA-<?=GetMessage("IMAGINWEB_SMS_OPERATOROV_ROSSII")?>
							   </li>
							  <li>
							    <?=GetMessage("IMAGINWEB_SMS_RASSYLKA")?> SMS <?=GetMessage("IMAGINWEB_SMS_PO_VSEMU_MIRU_BOLEE")?> 200 <?=GetMessage("IMAGINWEB_SMS_STRAN")?>)
							   </li>
							</ul>
							<p><?=GetMessage("IMAGINWEB_SMS_POSLE")?><a href="http://www.atompark.com/members/registration/index/services/sms"><?=GetMessage("IMAGINWEB_SMS_REGISTRACII")?></a> <?=GetMessage("IMAGINWEB_SMS_DOSTUPNY_SMS_DLA")?> &ndash; <?=GetMessage("IMAGINWEB_SMS_BONUS")?> 100%. <?=GetMessage("IMAGINWEB_SMS_OPLACIVATQ_MOJNO_KAK")?>, Paypal, <?=GetMessage("IMAGINWEB_SMS_TAK_I_BEZNALICNYM_RA")?></p>
							 
							<p><?=GetMessage("IMAGINWEB_SMS_OPISANIE_SERVISA")?><a href="http://epochtasms.ru/">http://epochtasms.ru/</a> </p>
							 
							<p><?=GetMessage("IMAGINWEB_SMS_REGISTRACIA")?><a href="http://www.atompark.com/members/registration/index/services/sms">http://www.atompark.com/members/registration/index/services/sms</a> </p>
						</div></td>
					</tr>
					<tr>
						<td><div class="tab-text"><p><?=GetMessage("IMAGINWEB_SMS_DLA_AKTIVACII_SMS_SL")?><a target="_blank" href="https://www.atompark.com/members/settings"><?=GetMessage("IMAGINWEB_SMS_NASTROYKAH_POLQZVATE")?></a> <?=GetMessage("IMAGINWEB_SMS_NA_VKLADKE")?> ePochta SMS <?=GetMessage("IMAGINWEB_SMS_AKTIVIROVATQ_ISPOLQZ")?> API. <?=GetMessage("IMAGINWEB_SMS_DLA_ETOGO_V_PUNKTE")?><strong><?=GetMessage("IMAGINWEB_SMS_VKLUCITQ")?> XML <?=GetMessage("IMAGINWEB_SMS_INTERFEYS")?></strong> <?=GetMessage("IMAGINWEB_SMS_NEOBHODIMO_VYBRATQ_P")?><strong><?=GetMessage("IMAGINWEB_SMS_DA")?></strong>. <?=GetMessage("IMAGINWEB_SMS_V_PUNKTE")?><strong><?=GetMessage("IMAGINWEB_SMS_REJIM")?> XML <?=GetMessage("IMAGINWEB_SMS_INTERFEYSA")?></strong> <?=GetMessage("IMAGINWEB_SMS_VYBRATQ_LIBO")?><strong><?=GetMessage("IMAGINWEB_SMS_REALQNAA_OTPRAVKA")?></strong>, <?=GetMessage("IMAGINWEB_SMS_LIBO")?><strong><?=GetMessage("IMAGINWEB_SMS_TESTOVYY_REJIM")?></strong>.</p><p><?=GetMessage("IMAGINWEB_SMS_V_SLUCAE_VYBORA_REJI")?><strong><?=GetMessage("IMAGINWEB_SMS_REALQNOY_OTPRAVKI")?></strong> <?=GetMessage("IMAGINWEB_SMS_SOOBSENIA_BUDUT_OTPR")?><strong><?=GetMessage("IMAGINWEB_SMS_TESTOVOGO_REJIMA")?></strong> <?=GetMessage("IMAGINWEB_SMS_SOOBSENIA_OTPRAVLENY")?><strong><?=GetMessage("IMAGINWEB_SMS_NE_GOTOVO")?></strong>. <?=GetMessage("IMAGINWEB_SMS_ETO_SVIDETELQSTVUET")?></p></div></td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
			
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELQ_2")?></td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator3') ?>"
								name="settings[originator3]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username3') ?>"
							    name="settings[username3]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password3')?>"
							    name="settings[password3]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_KREDITOV")?></strong></td>
						<td width="50%" class="balance" gate="epochtasms"></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--giper.mobi-->
	<?# $tabControl->BeginNextTab() ?>
		<tr class="heading gateLinkHead">
			<td colspan="2"><a class="toggle-link gateControll-new" ref="giper" ref="epochtasms" href="javascript:iwebtoggle('giper')">giper.mobi</a></td>
		</tr>
		
		<tr class="sites gateShow giper" id="giper" style="display: none;">
			<td colspan="2">
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_PODKLUCENI")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_POLE_OTPRAVITELA_BU")?>:</td>
						<td width="50%">
							<input
								type="text"
								size="30"
								value="<?=COption::GetOptionString('imaginweb.sms', 'originator7') ?>"
								name="settings[originator7]" />
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_NASTROYKI_DOSTUPA")?></td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_LOGIN")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'username7') ?>"
							    name="settings[username7]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><?=GetMessage("IMAGINWEB_SMS_PAROLQ")?></td>
						<td width="50%">
						    <input
							    type="password"
							    size="30"
							    value="<?=COption::GetOptionString('imaginweb.sms', 'password7')?>"
							    name="settings[password7]" />
						</td>
					</tr>
					<tr>
						<td width="50%"><strong><?=GetMessage("IMAGINWEB_SMS_BALANS_RUBLEY")?></strong></td>
						<td width="50%" class="balance" gate="giper.mobi"></td>
					</tr>
				</table>
			</td>
		</tr>
	
	<!--send-->
	<? $tabControl->BeginNextTab() ?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_POZVONITQ")?></td>
		</tr>
		<tr>
			<td width="40%">
				<?=GetMessage("IMAGINWEB_SMS_FROM_FIELD")?>
			</td>
			<td>
				<input type="text" maxlength="15" size="30" value="<?=htmlspecialchars($arRequest["FROM_FIELD_CALL"]);?>" name="FROM_FIELD_CALL">
			</td>
		</tr>
		<tr>
			<td>
				<span class="required">*</span><?=GetMessage("IMAGINWEB_SMS_TO_FIELD")?>
			</td>
			<td>
				<input type="text" maxlength="15" size="30" value="<?=htmlspecialchars($arRequest["TO_FIELD_CALL"]);?>" name="TO_FIELD_CALL">
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2">
				<span class="required">*</span><?=GetMessage("IMAGINWEB_SMS_TEKST_ZVONKA")?></td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea maxlength="210" style="width:100%; height:100px;" name="BODY_CALL"><?=htmlspecialchars($arRequest["BODY_CALL"]);?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<br />
				<input type="submit" title="<?=GetMessage("IMAGINWEB_SMS_POZVONITQ")?>" name="Send_CALL" value="<?=GetMessage("IMAGINWEB_SMS_POZVONITQ")?>">
			</td>
		</tr>
		
		
		<tr class="heading">
			<td colspan="2"><?=GetMessage("IMAGINWEB_SMS_OTPRAVITQ_SMS")?></td>
		</tr>
		<tr>
			<td width="40%">
				<?=GetMessage("IMAGINWEB_SMS_FROM_FIELD")?>
			</td>
			<td>
				<input type="text" maxlength="15" size="30" value="<?=htmlspecialchars($arRequest["FROM_FIELD"]);?>" name="FROM_FIELD">
			</td>
		</tr>
		<tr>
			<td>
				<span class="required">*</span><?=GetMessage("IMAGINWEB_SMS_TO_FIELD")?>
			</td>
			<td>
				<input type="text" maxlength="15" size="30" value="<?=htmlspecialchars($arRequest["TO_FIELD"]);?>" name="TO_FIELD">
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2">
				<span class="required">*</span><?=GetMessage("IMAGINWEB_SMS_TEXT_SMS")?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea maxlength="210" style="width:100%; height:100px;" name="BODY"><?=htmlspecialchars($arRequest["BODY"]);?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<br />
				<input type="submit" title="<?=GetMessage("IMAGINWEB_SMS_BUTTON_SEND")?>" name="Send" value="<?=GetMessage("IMAGINWEB_SMS_BUTTON_SEND")?>">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?=BeginNote();?>
				<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?><br>
				<?=EndNote();?>
			</td>
		</tr>
		<? /* */ ?>
	<? $tabControl->BeginNextTab() ?>
		<tr>
			<td colspan="2">
				<div class="tab-text imaginweb-description">
					<?=GetMessage("IMAGINWEB_SMS_BLAGODARIM")?>
				</div>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2">
				<?=GetMessage("IMAGINWEB_SMS_OBRABATYVAEMYE_SOBYT")?></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="tab-text imaginweb-description">
					<?=GetMessage("IMAGINWEB_SMS_SOBYTIA")?>
				</div>
			</td>
		</tr>
		<?/*
		<tr class="heading">
			<td colspan="2">
				<?=GetMessage("IMAGINWEB_SMS_KOMPONENTY")?></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="tab-text imaginweb-description">
				<strong>imaginweb.sms:auth</strong>  <?=GetMessage("IMAGINWEB_SMS_AVTORIZACIA_REGISTRA")?><br/>
				<strong>imaginweb.sms:validation</strong>  <?=GetMessage("IMAGINWEB_SMS_VALIDACIA_POLQZOVATE")?> sms, <?=GetMessage("IMAGINWEB_SMS_NAPODOBIE")?> webmoney, alfaclick<br/><br/>
				<?=GetMessage("IMAGINWEB_SMS_U_OBOIH_KOMPONETOV_N")?><strong>$_SESSION['IWEB_VALIDATION']</strong> <?=GetMessage("IMAGINWEB_SMS_PISUTSA_REZULQTATY_R")?><br/>
				<?=GetMessage("IMAGINWEB_SMS_OBA_KOMPONENTA_PODDE")?><strong>ajax</strong>
				</div>
			</td>
		</tr>
		*/?>
		<tr class="heading">
			<td colspan="2">
				<?=GetMessage("IMAGINWEB_SMS_OPISANIE_ISPOLQZOVAN")?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="tab-text imaginweb-description">
					<?=GetMessage("IMAGINWEB_SMS_API")?><br/>
<pre class="font-sz100"><code>
if(CModule::IncludeModule("imaginweb.sms")) {
	$sms = new CIWebSMS;
	$phone = "8 (925) 543-81-62";
	$sms->Send($phone,"TEST");
	print_r($sms->return_mess);
}</code></pre>
					<br>
					OR<br><br>
<pre class="font-sz100"><code>
if(CModule::IncludeModule("imaginweb.sms")) { 
	$phone = "8 (925) 543-81-62"; 
	CIWebSMS::Send($phone,"test"); 
} </code></pre><br>
				</div>
				<div class="tab-text imaginweb-description">
					<?=GetMessage("IMAGINWEB_SMS_CIWEBSMS")?>
					<pre class="font-sz100"><code>public function MakePhoneNumber($phone)</code></pre><br>
					<?=GetMessage("IMAGINWEB_SMS_CIWEBSMS_2")?>
					<pre class="font-sz100"><code>public function CheckPhoneNumber($phone)</code></pre><br>
					<?=GetMessage("IMAGINWEB_SMS_CIWEBSMS_3")?>
					<pre class="font-sz100"><code>public function Send($phone, $message, $arParams = array(), $encoding = LANG_CHARSET)</code></pre><br>
					<pre class="font-sz100"><code>
$arParams = array(
	'GATE' 		=> 'epochtasms',
	'LOGIN'		=> 'test',
	'PASSWORD'	=> 'test',
	'ORIGINATOR'	=> 'test'
);
					</code></pre><br>
					<?=GetMessage("IMAGINWEB_SMS_CIWEBSMS_4")?>
					<pre class="font-sz100"><code>public function GetCreditBalance($arParams = array())</code></pre><br/>
					$arParams - <?=GetMessage("IMAGINWEB_SMS_CIWEBSMS_5")?>
				</div>
			</td>
		</tr>
	<? $tabControl->BeginNextTab();?>
		<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?
		if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)> 0 && check_bitrix_sessid()) 
		{
			if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
				LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}
		?>
	
	<?$tabControl->Buttons();?>
	    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE") ?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE") ?>" />
	    <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY") ?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE") ?>" />
	    <?if(strlen($_REQUEST["back_url_settings"])):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL") ?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE") ?>" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'" />
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"]) ?>" />
	    <?endif;?>
	    <?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
	</form>
<? endif;?>