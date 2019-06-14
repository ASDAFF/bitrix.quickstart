<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(dirname(__FILE__)."/../include.php");
include(dirname(__FILE__)."/../constants.php");
include(dirname(__FILE__)."/../js_alerts.php");
$sModuleID = $obModule->sModuleID;
$obModule->ShowScripts();
IncludeModuleLangFile(dirname(__FILE__)."/status.php");
$ST_RIGHT = $obModule->GetGroupRight();

if ($ST_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js'); 
$APPLICATION->AddHeadScript('/bitrix/js/main/popup_menu.js');
$APPLICATION->AddHeadScript('/bitrix/js/main/admin_tools.js');
$sTableID = "tbl_tcs_orders";	
$arFilter = Array();
$lAdmin = $obModule->GetOrderTable($arFilter);

$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");

?>
<script>
	var sCloseText="<?=GetMessage("TCS_CLOSE")?>";
</script>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$arFilterFieldsTmp = array(
	GetMessage("TCS_ORDER_NUMBER"),
	GetMessage("TCS_BUYER"),
	//GetMessage("TCS_BANK_STATUS"),
	GetMessage("TCS_ORDER_STATUS"),

);

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFilterFieldsTmp
);

$oFilter->Begin();
?>

	<tr>
		<td><b><?=GetMessage("TCS_DATE_CREATE")?>:</b></td>
		<td>
			<?echo CalendarPeriod("filter_date_from", $filter_date_from, "filter_date_to", $filter_date_to, "find_form", "Y")?>
		</td>
	</tr>

	<tr>
		<td><?=GetMessage("TCS_ORDER_NUMBER")?>:</td>
		<td>
			<script language="JavaScript">
				function filter_id_from_Change()
				{
					if (document.find_form.filter_id_to.value.length<=0)
					{
						document.find_form.filter_id_to.value = document.find_form.filter_id_from.value;
					}
				}
			</script>
			<?=GetMessage("TCS_DATE_FROM")?>
			<input type="text" name="filter_id_from" autocomplete = "off" OnChange="filter_id_from_Change()" value="<?echo (IntVal($filter_id_from)>0)?IntVal($filter_id_from):""?>" size="10">
			<?=GetMessage("TCS_DATE_TO")?>
			<input type="text" name="filter_id_to" autocomplete = "off" value="<?echo (IntVal($filter_id_to)>0)?IntVal($filter_id_to):""?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("TCS_BUYER")?>:</td>
		<td>
			<input type="text" name="filter_buyer" value="<?echo htmlspecialchars($filter_buyer)?>" size="40">
		</td>
	</tr>	
	<?/*<tr>
		<td><?=GetMessage("TCS_BANK_STATUS")?>:</td>
		<td>
			<select name = "filter_bank_status">
				<option></option>
				<?foreach($arTCSBankStatuses as $iKey=>$arStatus):?>
					<option value = "<?=$iKey?>"><?=$arStatus["NAME"]?></option>
				<?endforeach;?>
			</select>
		</td>
	</tr>*/?>	
	<tr>
		<td><?=GetMessage("TCS_ORDER_STATUS")?>:</td>
		<td>
			<select name = "filter_order_status">
				<option></option>
				<?foreach($arTCSOrderStatuses as $iKey=>$arStatus):?>
					<option value = "<?=$iKey?>"><?=$arStatus["NAME"]?></option>
				<?endforeach;?>
			</select>
		</td>
	</tr>	
	<?
		$oFilter->Buttons(
			array(
				"table_id" => $sTableID,
				"url" => $APPLICATION->GetCurPage(),
				"form" => "find_form"
			)
		);
		$oFilter->End();
	?>
</form>
<?$lAdmin->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>