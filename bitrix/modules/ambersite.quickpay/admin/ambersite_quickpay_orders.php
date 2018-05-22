<? 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__); 
global $DB; 

$sTableID = "tbl_ambersite_quickpay";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if($lAdmin->EditAction())
{
	foreach($FIELDS as $id=>$arField) {
		
	}
}

if(($arID = $lAdmin->GroupAction())) {
	foreach($arID as $ID) {
		$ID = IntVal($ID); if($ID <= 0) continue;
		if($_REQUEST['action']=='delete') {
			$DB->Query("DELETE F.* FROM b_ambersite_quickpay F WHERE F.ID = ".$ID, false, $err_mess.__LINE__);
		}
	}
}

$lAdmin->InitFilter(array("find_id", "find_product", "find_fio", "find_phone", "find_email", "find_date_from", "find_date_to", "find_paid", "find_paytype")); 
$arFilter = Array("ID" => $find_id, "PRODUCT" => $find_product, "FIO" => $find_fio, "PHONE" => $find_phone, "EMAIL" => $find_email, "DATEFROM" => $find_date_from, "DATETO" => $find_date_to, "PAID" => $find_paid, "PAYTYPE" => $find_paytype); 

if(CModule::IncludeModuleEx('ambersite.quickpay')) {
	$where = QuickPay::DBWhere($arFilter);
}
$rsData = $DB->Query("SELECT F.* FROM b_ambersite_quickpay F $where ORDER BY F.$by $order", false, $err_mess.__LINE__); 
$rsData = new CAdminResult($rsData, $sTableID); 
$rsData->NavStart("20"); 
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ZAKAZU"))); 

$lAdmin->AddHeaders(array( 
  array("id" => "ID", 
    "content" => "ID", 
    "sort" => "id", 
    "default" => true, 
  ), 
  array("id" => "PRODUCT", 
    "content" => GetMessage("CHTO_OPLACHIVAEM"), 
    "sort" => "product", 
    "default" =>true, 
  ),
  array("id" => "FIO", 
    "content" => GetMessage("FIO"), 
    "sort" => "fio", 
    "default" => true, 
  ),
  array("id" => "PHONE", 
    "content" => GetMessage("TELEFON"), 
    "sort" => "phone", 
    "default" => true, 
  ),
  array("id" => "COUNT", 
    "content" => GetMessage("KOLICHESTVO"), 
    "sort" => "count", 
    "default" => true, 
  ),
  array("id" => "SUM", 
    "content" => GetMessage("STOIMOST"), 
    "sort" => "sum", 
    "default" => true, 
  ), 
  array("id" => "PAID", 
    "content" => GetMessage("OPLATA"), 
    "sort" =>"paid", 
    "default" =>true, 
  ),
  array("id" => "DATE", 
    "content" => GetMessage("DATA_I_VREMYA"), 
    "sort" => "date", 
    "default" => true, 
  ),
  array("id" => "PAYTYPE", 
    "content" => GetMessage("SPOSOB_OPLATU"), 
    "sort" => "paytype", 
    "default" => false, 
  ),
  array("id" => "EMAIL", 
    "content" => "E-mail", 
    "sort" => "email", 
    "default" => false, 
  ),
  array("id" => "KOMM", 
    "content" => GetMessage("KOMMENTARIJ"), 
    "sort" => "komm", 
    "default" => false, 
  ),
)); 
$arActions = Array();
while($arRes = $rsData->NavNext(true, "f_")) { 
	if($arRes['PAID']=='N') $arRes['PAID'] = GetMessage("NEIZVESTNO");
	if($arRes['PAID']=='Y') $arRes['PAID'] = GetMessage("OPLACHENO");
	if($arRes['SUM']) $arRes['SUM'] = $arRes['SUM'].' '.GetMessage("RUB").'.';
	if($arRes['DATE']) $arRes['DATE'] = $DB->FormatDate($arRes['DATE'], 'YYYY-MM-DD HH:MI:SS', 'DD.MM.YYYY HH:MI:SS');
	if($arRes['PAYTYPE']=='PC') $arRes['PAYTYPE'] = GetMessage("OPLATA_IZ_KOSHELKA_V_YANDEX_DENGAH"); 
	if($arRes['PAYTYPE']=='AC') $arRes['PAYTYPE'] = GetMessage("S_BANKOVSKOJ_KARTU"); 
	if($arRes['PAYTYPE']=='MC') $arRes['PAYTYPE'] = GetMessage("S_BALANSA_MOBILNOGO");  
    $row =& $lAdmin->AddRow($f_ID, $arRes); 
	$row->AddField("ID", $f_ID);
	$row->AddViewField("ID", '<a href="ambersite_quickpay_orders_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
	$arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage("PODROBNO"),
		"ACTION" => $lAdmin->ActionRedirect("ambersite_quickpay_orders_edit.php?ID=".$f_ID),
		"DEFAULT" => true
	);
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("UDALIT"),
		"ACTION" => "if(confirm('".GetMessage("DEJSTVITELNO_UDALIT_ZAPIS")."?')) ".$lAdmin->ActionDoGroup($f_ID, "delete"),
		"DEFAULT" => false
    );
	$row->AddActions($arActions); 
unset($arActions);}

$lAdmin->AddGroupActionTable(Array(
  "delete" => true,
)); 

$aContext = array(
  array(
    "TEXT" => GetMessage("DOBAVIT_ZAKAZ"),
    "LINK" => "/bitrix/admin/ambersite_quickpay_orders_edit.php?lang=".LANG,
    "TITLE" => GetMessage("DOBAVIT_ZAKAZ"),
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode(); 
$APPLICATION->SetTitle(GetMessage("SPISOK_ZAKAZOV")); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

$oFilter = new CAdminFilter($sTableID."_filter", array("ID", GetMessage("CHTO_OPLACHIVAEM"), GetMessage("FIO"), GetMessage("TELEFON"), "E-mail", GetMessage("DATA"), GetMessage("OPLATA"), GetMessage("SPOSOB_OPLATU"))); 
?> 

<form name="find_form" method="get" action="<? $APPLICATION->GetCurPage();?>"> 
<? $oFilter->Begin();?> 
<tr> 
  <td>ID:</td> 
  <td> 
    <input type="text" name="find_id" size="47" value="<? htmlspecialcharsex($find_id)?>"> 
  </td> 
</tr>
<tr> 
  <td><?=GetMessage("CHTO_OPLACHIVAEM")?>:</td> 
  <td> 
    <input type="text" name="find_product" size="47" value="<? htmlspecialcharsex($find_product)?>"> 
  </td> 
</tr> 
<tr> 
  <td><?=GetMessage("FIO")?>:</td> 
  <td><input type="text" name="find_fio" size="47" value="<? htmlspecialcharsex($find_fio)?>"></td> 
</tr>
<tr> 
  <td><?=GetMessage("TELEFON")?>:</td> 
  <td><input type="text" name="find_phone" size="47" value="<? htmlspecialcharsex($find_phone)?>"></td> 
</tr>
<tr> 
  <td>E-mail:</td> 
  <td><input type="text" name="find_email" size="47" value="<? htmlspecialcharsex($find_email)?>"></td> 
</tr> 
<tr>
	<td nowrap><?=GetMessage("DATA")?>:</td>
	<td nowrap><? echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form")?></td>
</tr>
<tr>
    <td nowrap><?=GetMessage("OPLATA")?>:</td>
    <td nowrap>
        <select name="find_paid">
        	<option value="">--<?=GetMessage("VUBARITE")?>--</option>
            <option value="N"><?=GetMessage("NEIZVESTNO")?></option>
            <option value="Y"><?=GetMessage("OPLACHENO")?></option>
        </select>
    </td>
</tr>
<tr>
    <td nowrap><?=GetMessage("SPOSOB_OPLATU")?>:</td>
    <td nowrap>
        <select name="find_paytype">
        	<option value="">--<?=GetMessage("VUBARITE")?>--</option>
            <option value="AC"><?=GetMessage("S_BANKOVSKOJ_KARTU")?></option>
            <option value="MC"><?=GetMessage("S_BALANSA_MOBILNOGO")?></option>
            <option value="PC"><?=GetMessage("OPLATA_IZ_KOSHELKA_V_YANDEX_DENGAH")?></option>
        </select>
    </td>
</tr>
<? 
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form")); 
$oFilter->End(); 
?> 
</form> 

<? $lAdmin->DisplayList();

echo BeginNote(); echo GetMessage("PODROBNO_O_MODULE_PRIEMA_PLATEGEJ").': <a href="http://marketplace.1c-bitrix.ru/solutions/ambersite.quickpay/" target="_blank">http://marketplace.1c-bitrix.ru/solutions/ambersite.quickpay/</a>'; echo EndNote(); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); 
?>