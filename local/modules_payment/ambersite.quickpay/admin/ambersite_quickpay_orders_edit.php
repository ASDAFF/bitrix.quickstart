<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__); 
global $DB; 

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ZAKAZ"), "ICON" => "main_user_edit", "TITLE" => GetMessage("INFORMACIJA_O_ZAKAZE"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);
$bVarsFromForm = false;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() && CModule::IncludeModuleEx('ambersite.quickpay')) {
	$request = array('PRODUCT' => $PRODUCT, 'FIO' => $FIO, 'PHONE' => $PHONE, 'EMAIL' => $EMAIL, 'KOMM' => $KOMM, 'PAYTYPE' => $PAYTYPE, 'SUM' => $SUM, 'COUNT' => $COUNT, 'PAID' => $PAID?$PAID:'N');
	
	if(!$ID) $quickpayaddid = QuickPay::Add($request); else $quickpayupdatecount = QuickPay::Update($ID, $request);
	if($quickpayaddid || $quickpayupdatecount) {
		if ($apply != "") {
			$locid = $ID?$ID:$quickpayaddid;
			LocalRedirect("/bitrix/admin/ambersite_quickpay_orders_edit.php?ID=".$locid."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		} else LocalRedirect("/bitrix/admin/ambersite_quickpay_orders.php?lang=".LANG);
	} else {
		if($e = $APPLICATION->GetException()) {
			$message = new CAdminMessage(GetMessage("OSHIBKA_SOHRANENIJA"), $e);
			$bVarsFromForm = true;
		}
	}
}

if($ID>0) {
	$arFilter = Array("ID" => $ID);
	if(CModule::IncludeModuleEx('ambersite.quickpay')) {
		$where = QuickPay::DBWhere($arFilter);
	}
	$rsData = $DB->Query("SELECT F.* FROM b_ambersite_quickpay F $where", false, $err_mess.__LINE__);
	$rsData = new CAdminResult($rsData, "tbl_ambersite_quickpay");  
	$rsData->ExtractFields("str_");
}

if($bVarsFromForm) $DB->InitTableVarsForEdit("b_ambersite_quickpay", "", "str_");

$APPLICATION->SetTitle(($ID>0?GetMessage("ZAKAZ_N").$ID:GetMessage("NOVUJ_ZAKAZ")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
  array(
    "TEXT" => GetMessage("SPISOK_ZAKAZOV"),
    "TITLE" => GetMessage("SPISOK_ZAKAZOV"),
    "LINK" => "ambersite_quickpay_orders.php?lang=".LANG,
    "ICON" => "btn_list",
  )
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0) CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("ZAKAZ_SOHRANEN"), "TYPE"=>"OK"));
if($message) echo $message->Show(); elseif($rsData->LAST_ERROR!="") CAdminMessage::ShowMessage($rsData->LAST_ERROR);
?>

<form method="POST" Action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?=bitrix_sessid_post();?>
<? $tabControl->Begin();?>

<? $tabControl->BeginNextTab();?>
	<? if($str_CODE):?>
	<tr>
    	<td><?=GetMessage("KOD_ZAKAZA")?>:</td>
        <td><?=$str_CODE;?></td>
    </tr>
    <? endif;?>
    <tr>
    	<td><?=GetMessage("CHTO_OPLACHIVAEM")?>:</td>
        <td><input type="text" name="PRODUCT" value="<?=$str_PRODUCT;?>" size="50" maxlength="255"></td>
    </tr>
    <tr>
    	<td><?=GetMessage("FIO")?>:</td>
        <td><input type="text" name="FIO" value="<?=$str_FIO;?>" size="50" maxlength="255"></td>
    </tr>
    <tr>
    	<td><?=GetMessage("TELEFON")?>:</td>
        <td><input type="text" name="PHONE" value="<?=$str_PHONE;?>" size="50" maxlength="255"></td>
    </tr>
    <tr>
    	<td>E-mail:</td>
        <td><input type="text" name="EMAIL" value="<?=$str_EMAIL;?>" size="50" maxlength="255"></td>
    </tr>
    <tr>
    	<td><?=GetMessage("KOMMENTARIJ")?>:</td>
        <td><textarea name="KOMM" cols="52" rows="3"><?=$str_KOMM;?></textarea></td>
    </tr>
    <tr>
    	<td><?=GetMessage("KOLICHESTVO")?>:</td>
        <td><input type="text" name="COUNT" value="<?=$str_COUNT;?>" size="7" maxlength="10"></td>
    </tr>
    <tr>
    	<td><?=GetMessage("SUMMA_RUB")?>:</td>
        <td><input type="text" name="SUM" value="<?=$str_SUM;?>" size="7" maxlength="10"></td>
    </tr>
    <tr>
    	<td><?=GetMessage("SPOSOB_OPLATU")?>:</td>
        <td><select name="PAYTYPE"><option value="AC" <? if($str_PAYTYPE=='AC'):?>selected="selected"<? endif;?>><?=GetMessage("S_BANKOVSKOJ_KARTU")?></option><option value="PC" <? if($str_PAYTYPE=='PC'):?>selected="selected"<? endif;?>><?=GetMessage("OPLATA_IZ_KOSHELKA_V_YANDEX_DENGAH")?></option><option value="MC" <? if($str_PAYTYPE=='MC'):?>selected="selected"<? endif;?>><?=GetMessage("S_BALANSA_MOBILNOGO")?></option></select></td>
    </tr>
    <tr>
    	<td><?=GetMessage("OPLATA")?>:</td>
        <td><input type="checkbox" name="PAID" value="Y" <? if($str_PAID=='Y'):?>checked="checked"<? endif;?>></td>
    </tr>
    <? if($ID>0):?><input type="hidden" name="ID" value="<?=$ID?>"><? endif;?>
<? 
$tabControl->Buttons(array("back_url" => "ambersite_quickpay_orders.php?lang=".LANG));
$tabControl->End();
$tabControl->ShowWarnings("post_form", $message);
?>
</form>

<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>