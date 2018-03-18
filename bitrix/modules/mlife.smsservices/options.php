<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$module_id = "mlife.smsservices";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
$zr = "";
if (! ($MODULE_RIGHT >= "R"))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(Loc::getMessage("MLIFESS_OPT_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

\Bitrix\Main\Loader::includeModule($module_id);

$bollModuleSale = \Bitrix\Main\Loader::includeModule("sale");

	if($bollModuleSale) {
		
		$arMakros = array();
		$arOrderProps = array();
		$arStatus = array();
		$arPerson = array();
		
		//свойства
		//$db_props = \CSaleOrderProps::GetList(array("SORT" => "ASC"),array("TYPE" => array('TEXT','TEXTAREA'))); //old module sale
		$db_props = \CSaleOrderProps::GetList(array("SORT" => "ASC"),array("TYPE" => array('STRING')));
		while($arProperty = $db_props->Fetch()) {
			$arOrderProps[$arProperty["PERSON_TYPE_ID"]][$arProperty['CODE']] = $arProperty['NAME'];
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['PROPERTY_'.$arProperty['CODE']] = $arProperty['NAME'];
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['USER_PHONE'] = Loc::getMessage("MLIFESS_IM_MACROS1");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['ORDER_NUM'] = Loc::getMessage("MLIFESS_IM_MACROS2");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['ORDER_PRICE'] = Loc::getMessage("MLIFESS_IM_MACROS3");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['DELIVERY_PRICE'] = Loc::getMessage("MLIFESS_IM_MACROS4");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['STATUS_NAME'] = Loc::getMessage("MLIFESS_IM_MACROS5");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['DELIVERY_NAME'] = Loc::getMessage("MLIFESS_IM_MACROS6");
			$arMakros[$arProperty["PERSON_TYPE_ID"]]['ORDER_SUM'] = Loc::getMessage("MLIFESS_IM_MACROS7");
		}
		
		//статусы
		$obStatus = \CSaleStatus::GetList();
		while($ar = $obStatus->Fetch()) {
			$arStatus[$ar['ID']][$ar['LID']] = $ar['NAME'];
		}
		
		//типы плательщиков
		$db_ptype = \CSalePersonType::GetList(Array("SORT" => "ASC"), Array());
		$bFirst = True;
		while ($ptype = $db_ptype->Fetch())
		{
			$arPerson[$ptype["ID"]] = $ptype["NAME"];
		}
	
	}

$arSites = array();
$obSite = \CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	$arSites[$arResult['ID']] = $arResult['NAME'];
}
	
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MODULE_RIGHT == "W" && strlen($_REQUEST["Update"]) > 0 && check_bitrix_sessid())
{

\Bitrix\Main\Config\Option::set($module_id, "transport", $_REQUEST["transport"]);
\Bitrix\Main\Config\Option::set($module_id, "login", $_REQUEST["login"]);
\Bitrix\Main\Config\Option::set($module_id, "passw", $_REQUEST["passw"]);
\Bitrix\Main\Config\Option::set($module_id, "sender", $_REQUEST["sender"]);
\Bitrix\Main\Config\Option::set($module_id, "charset", $_REQUEST["charset"]);
\Bitrix\Main\Config\Option::set($module_id, "cacheotp", $_REQUEST["cacheotp"]);
\Bitrix\Main\Config\Option::set($module_id, "listotp", $_REQUEST["listotp"]);
\Bitrix\Main\Config\Option::set($module_id, "cachebalance", $_REQUEST["cachebalance"]);

\Bitrix\Main\Config\Option::set($module_id, "transport_r", $_REQUEST["transport_r"]);
\Bitrix\Main\Config\Option::set($module_id, "login_r", $_REQUEST["login_r"]);
\Bitrix\Main\Config\Option::set($module_id, "passw_r", $_REQUEST["passw_r"]);
\Bitrix\Main\Config\Option::set($module_id, "sender_r", $_REQUEST["sender_r"]);
\Bitrix\Main\Config\Option::set($module_id, "charset_r", $_REQUEST["charset_r"]);
\Bitrix\Main\Config\Option::set($module_id, "cacheotp_r", $_REQUEST["cacheotp_r"]);
\Bitrix\Main\Config\Option::set($module_id, "listotp_r", $_REQUEST["listotp_r"]);
\Bitrix\Main\Config\Option::set($module_id, "cachebalance_r", $_REQUEST["cachebalance_r"]);

\Bitrix\Main\Config\Option::set($module_id, "transport_app", $_REQUEST["transport_app"]);
\Bitrix\Main\Config\Option::set($module_id, "login_app", $_REQUEST["login_app"]);
\Bitrix\Main\Config\Option::set($module_id, "passw_app", $_REQUEST["passw_app"]);
\Bitrix\Main\Config\Option::set($module_id, "sender_app", $_REQUEST["sender_app"]);
\Bitrix\Main\Config\Option::set($module_id, "charset_app", $_REQUEST["charset_app"]);
\Bitrix\Main\Config\Option::set($module_id, "cacheotp_app", $_REQUEST["cacheotp_app"]);
\Bitrix\Main\Config\Option::set($module_id, "listotp_app", $_REQUEST["listotp_app"]);
\Bitrix\Main\Config\Option::set($module_id, "cachebalance_app", $_REQUEST["cachebalance_app"]);

\Bitrix\Main\Config\Option::set($module_id, "activesale", $_REQUEST["activesale"]);
\Bitrix\Main\Config\Option::set($module_id, "limitsms", $_REQUEST["limitsms"]);
\Bitrix\Main\Config\Option::set($module_id, "limittimesms", $_REQUEST["limittimesms"]);
\Bitrix\Main\Config\Option::set($module_id, "translit", $_REQUEST["translit"]);

	foreach($arSites as $siteId=>$siteName){
		foreach($arPerson as $persid=>$persName){
			\Bitrix\Main\Config\Option::set($module_id, "property_phone_".$siteId."_".$persid, $_REQUEST["property_phone_".$siteId."_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "admin_phone_".$siteId."_".$persid, $_REQUEST["admin_phone_".$siteId."_".$persid]);
			
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_cancelY_".$persid, $_REQUEST["mess_status_".$siteId."_cancelY_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_cancelN_".$persid, $_REQUEST["mess_status_".$siteId."_cancelN_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_deliveryN_".$persid, $_REQUEST["mess_status_".$siteId."_deliveryN_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_deliveryY_".$persid, $_REQUEST["mess_status_".$siteId."_deliveryY_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_payN_".$persid, $_REQUEST["mess_status_".$siteId."_payN_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_payY_".$persid, $_REQUEST["mess_status_".$siteId."_payY_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_new_".$persid, $_REQUEST["mess_status_".$siteId."_new_".$persid]);
			\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_new2_".$persid, $_REQUEST["mess_status_".$siteId."_new2_".$persid]);
			
			foreach($arStatus as $statusid=>$statusname) {
				
				\Bitrix\Main\Config\Option::set($module_id, "mess_status_".$siteId."_".$statusid."_".$persid, $_REQUEST["mess_status_".$siteId."_".$statusid."_".$persid]);
			
			}
		}
	}

}

$actSale = \Bitrix\Main\Config\Option::get($module_id, "activesale", "N","");
$actSale = ($actSale=="Y") ? true : false;

$smsServices = new \Mlife\Smsservices\Sender();

try{
$opt = $smsServices->getAllSenderOptions();
$opt_r = $smsServices->getAllSenderOptions(false);
$opt_app = $smsServices->getAllSenderOptions('app');
}catch(\Exception $ex){
$opt = array();
$opt_r = array();
$opt_app = array();
}

$aTabs = array();
$aTabs[] = array("DIV" => "edit3", "TAB" => Loc::getMessage("MLIFESS_OPT_TAB1"), "ICON" => "vote_settings", "TITLE" => Loc::getMessage("MLIFESS_OPT_TAB1_T"));
if($bollModuleSale && $actSale){

	foreach($arSites as $siteId=>$siteName){
		$aTabs[] = array("DIV" => "edit".$siteId, "TAB" => "[".$siteId."] ".$siteName, "ICON" => "vote_settings5", "TITLE" => "[".$siteId."] ".$siteName);
	}
	
}
$aTabs[] = array("DIV" => "edit4", "TAB" => Loc::getMessage("MLIFESS_OPT_TAB2"), "ICON" => "vote_settings2", "TITLE" => Loc::getMessage("MLIFESS_OPT_TAB2_T"));

$tabControl = new \CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($module_id)?>&lang=<?=LANGUAGE_ID?>&mid_menu=1" id="FORMACTION">
<?
$tabControl->BeginNextTab();
?>
	<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_MAIN_SHARE")?><a href="http://mlife-media.by/tpages/kod-na-500-sms.php?utm_source=mlife_smsservices&utm_medium=bxadmin&utm_term=domain&utm_content=<?=$_SERVER['HTTP_HOST']?>&utm_campaign=smsasistent" target="_blank"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_MAIN_SHARE_2")?></a> | <a href="http://sms-assistent.by/rekvizity/" target="_blank"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_MAIN_SHARE_3")?></a></td></tr>
	<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_MAIN")?></td></tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_SHLUZ")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "transport", "","");
			$boolTransport = ($val) ? true : false;
			?>
			<select name="transport" id="transport">
			<?
			$selected = '';
			if($val=='') $selected = ' selected';
			echo '<option value=""'.$selected.'>'.Loc::getMessage("SERVIS_NONE").'</option>';
			$smslist = glob($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.smsservices/lib/transport/*.php");
			foreach ($smslist as $value) {
				
				$name = str_replace($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mlife.smsservices/lib/transport/','',$value);
				if(strpos($name,'app')!==false) continue;
				$selected = '';
				if($val==$name) $selected = ' selected';
				echo '<option value="'.$name.'"'.$selected.'>'.((Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name)))) ? Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name))) : str_replace('.php','',$name)).'</option>';
				
			}
			?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_LOGIN")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "login", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="login"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_PASSW")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "passw", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="passw"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTP")?>:</td>
		<td>
		<?
		if(\Bitrix\Main\Config\Option::get($module_id, "listotp","","")=='Y' && $opt!=''){
		?>
			<select name="sender" id="sender">
			<?=$opt;?>
			</select>
		<?} else {
		$val = \Bitrix\Main\Config\Option::get($module_id, "sender", ".","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="sender">
		<?}?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPLIST")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "listotp", "N","");?>
			<input type="checkbox" value="Y" name="listotp" id="listotp" <?if ($val=="Y") echo "checked";?>></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPCACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cacheotp", "86400","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cacheotp"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_BALANCECACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cachebalance", "3600","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cachebalance"></td>
	</tr>

	<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_RESERVE")?></td></tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_SHLUZ")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "transport_r", "","");
			$boolTransport = ($val) ? true : false;
			?>
			<select name="transport_r" id="transport_r">
			<?
			$selected = '';
			if($val=='') $selected = ' selected';
			echo '<option value=""'.$selected.'>'.Loc::getMessage("SERVIS_NONE").'</option>';
			$smslist = glob($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.smsservices/lib/transport/*.php");
			foreach ($smslist as $value) {
				$name = str_replace($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mlife.smsservices/lib/transport/','',$value);
				if(strpos($name,'app')!==false) continue;
				$selected = '';
				if($val==$name) $selected = ' selected';
				echo '<option value="'.$name.'"'.$selected.'>'.((Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name)))) ? Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name))) : str_replace('.php','',$name)).'</option>';
			}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_LOGIN")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "login_r", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="login_r"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_PASSW")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "passw_r", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="passw_r"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTP")?>:</td>
		<td>
		<?
		if(\Bitrix\Main\Config\Option::get($module_id, "listotp_r","","")=='Y' && $opt_r!=''){
		?>
			<select name="sender_r" id="sender_r">
			<?=$opt_r;?>
			</select>
		<?} else {
		$val = \Bitrix\Main\Config\Option::get($module_id, "sender_r", ".","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="sender_r">
		<?}?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPLIST")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "listotp_r", "N","");?>
			<input type="checkbox" value="Y" name="listotp_r" id="listotp_r" <?if ($val=="Y") echo "checked";?>></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPCACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cacheotp_r", "86400","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cacheotp_r"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_BALANCECACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cachebalance_r", "3600","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cachebalance_r"></td>
	</tr>
	
	
	
	
	<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFESS_OPT_SHLUZ_APP")?></td></tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_SHLUZ")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "transport_app", "","");
			$boolTransport = ($val) ? true : false;
			?>
			<select name="transport_app" id="transport_app">
			<?
			$selected = '';
			if($val=='') $selected = ' selected';
			echo '<option value=""'.$selected.'>'.Loc::getMessage("SERVIS_NONE").'</option>';
			$smslist = glob($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.smsservices/lib/transport/*.php");
			foreach ($smslist as $value) {
				$name = str_replace($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mlife.smsservices/lib/transport/','',$value);
				if(strpos($name,'app')!==false) {
				$selected = '';
				if($val==$name) $selected = ' selected';
				echo '<option value="'.$name.'"'.$selected.'>'.((Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name)))) ? Loc::getMessage("SERVIS_".strtoupper(str_replace('.php','',$name))) : str_replace('.php','',$name)).'</option>';
				}
			}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_LOGIN")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "login_app", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="login_app"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_PASSW")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "passw_app", "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="passw_app"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTP")?>:</td>
		<td>
		<?
		if(\Bitrix\Main\Config\Option::get($module_id, "listotp_app","","")=='Y' && $opt_app!=''){
		?>
			<select name="sender_app" id="sender_app">
			<?=$opt_app;?>
			</select>
		<?} else {
		$val = \Bitrix\Main\Config\Option::get($module_id, "sender_app", ".","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="sender_app">
		<?}?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPLIST")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "listotp_app", "N","");?>
			<input type="checkbox" value="Y" name="listotp_app" id="listotp_app" <?if ($val=="Y") echo "checked";?>></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_OTPCACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cacheotp_app", "86400","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cacheotp_app"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_BALANCECACHE")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "cachebalance_app", "3600","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="cachebalance_app"></td>
	</tr>
	
	
	
	

	<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFESS_OPT_TITLEDOP")?></td></tr>
	<?
	if($bollModuleSale){
	?>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_IM_ON")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "activesale", "N","");?>
			<input type="checkbox" value="Y" name="activesale" id="activesale" <?if ($val=="Y") echo "checked";?>></td>
	</tr>
	<?}?>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_LIMITSMS")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "limitsms", "10","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="limitsms"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_LIMITSMS2")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "limittimesms", "600","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="limittimesms"></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_OPT_TRANSLIT")?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "translit", "N","");?>
			<input type="checkbox" value="Y" name="translit" id="translit" <?if ($val=="Y") echo "checked";?>></td>
	</tr>
	
<?
if($bollModuleSale && $actSale){
?>
<?
foreach($arSites as $siteId=>$siteName){
	$tabControl->BeginNextTab();
	?>
	<?
	foreach($arPerson as $persid=>$persName){
	
	?>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_PHONE")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "property_phone_".$siteId."_".$persid, "","");?>
			<?echo \CMlifeSmsServicesHtml::getSelect("property_phone_".$siteId."_".$persid,$arOrderProps[$persid],$val,false,false,true);?>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_PHONEADMIN")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "admin_phone_".$siteId."_".$persid, "","");?>
			<input type="text" size="35" maxlength="255" value="<?=$val?>" name="admin_phone_<?=$siteId?>_<?=$persid?>"></td>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_IM_PROP_MACROS")?>:</td>
		<td>
		<?foreach($arMakros[$persid] as $macros=>$nacrosName){
			?>
			#<?=$macros?># - <?=$nacrosName?>; 
			<?
		}?>
		</td>
	</tr>
	<?
	
	foreach($arStatus as $statusid=>$statusname) {
	?>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS")?> <?=$statusname[LANGUAGE_ID]?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_".$statusid."_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_<?=$statusid?>_<?=$persid?>" id="mess_status_<?=$siteId?>_<?=$statusid?>_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<?
	}
	?>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_OTMY")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_cancelY_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_cancelY_<?=$persid?>" id="mess_status_<?=$siteId?>_cancelY_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_OTMN")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_cancelN_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_cancelN_<?=$persid?>" id="mess_status_<?=$siteId?>_cancelN_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_DOSTY")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_deliveryY_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_deliveryY_<?=$persid?>" id="mess_status_<?=$siteId?>_deliveryY_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_DOSTN")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_deliveryN_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_deliveryN_<?=$persid?>" id="mess_status_<?=$siteId?>_deliveryN_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_OPLY")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_payY_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_payY_<?=$persid?>" id="mess_status_<?=$siteId?>_payY_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_OPLN")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_payN_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_payN_<?=$persid?>" id="mess_status_<?=$siteId?>_payN_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_NEW")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_new_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_new_<?=$persid?>" id="mess_status_<?=$siteId?>_new_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<tr>
		<td style="width:50%;"><?=Loc::getMessage("MLIFESS_IM_PROP_STATUS_NEWADMIN")?>, <?=Loc::getMessage("MLIFESS_IM_PROP_DLYA")?> <?=$persName?>:</td>
		<td>
			<?$val = \Bitrix\Main\Config\Option::get($module_id, "mess_status_".$siteId."_new2_".$persid, "","");?>
			<textarea style="width:90%;" name="mess_status_<?=$siteId?>_new2_<?=$persid?>" id="mess_status_<?=$siteId?>_new2_<?=$persid?>"><?=$val?></textarea>
		</td>
	</tr>
	<?
	}?>
	<?
}
?>
<?
}
?>
	
	<?
$tabControl->BeginNextTab();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
$tabControl->Buttons();
?>
	<input <?if ($MODULE_RIGHT<"W") echo "disabled" ?> type="submit" class="adm-btn-green" name="Update" value="<?=Loc::getMessage("MLIFESS_OPT_SEND")?>" />
	<input type="hidden" name="Update" value="Y" />
<?$tabControl->End();
?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?> 