<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("mlife.asz");
use Bitrix\Main\Localization\Loc;
use Mlife\Asz;
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$listTableId = "tbl_mlife_asz_order";

$oSort = new CAdminSorting($listTableId, "ID", "ASC");
$arOrder = (strtoupper($by) === "ID"? array($by => $order): array($by => $order, "ID" => "ASC"));

$adminList = new CAdminList($listTableId, $oSort);

// обработка одиночных и групповых действий
if(($arID = $adminList->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = Asz\OrderTable::getList(
			array(
				'order' => $arOrder,
				'select' => array('ID'),
			)
		);
		while($arRes = $rsData->Fetch())
		  $arID[] = $arRes['ID'];
	}
	
	if($_REQUEST['action']=="delete") {
		foreach($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;
				$ID = IntVal($ID);
				
			$res = Asz\OrderTable::delete(array("ID"=>$ID));
		}
	}
	
}
$arFilter = array();
if($FilterSiteId) {
	$arFilter["SITEID"] = $FilterSiteId;
}
$ASZOrder = Asz\OrderTable::getList(
	array(
		'order' => $arOrder,
		'select' => array("*","ADDSTAT.NAME","ADDPAY.NAME","ADDDELIVERY.NAME"),
		'filter' => $arFilter,
	)
);

$ASZOrder = new CAdminResult($ASZOrder, $listTableId);
$ASZOrder->NavStart();

$adminList->NavText($ASZOrder->GetNavPrint(Loc::getMessage("MLIFE_ASZ_ORDERLIST_NAV")));

$cols = Asz\OrderTable::getEntity()->getFields();
$colsAdmin = Asz\OrderTable::getMapAdmin();
$colHeaders = array();

foreach ($cols as $colId => $col)
{
	if(in_array($col->getName(),$colsAdmin)){
		$tmpAr = array(
			"id" => $col->getName(),
			"content" => $col->getTitle(),
			"sort" => $col->getName(),
			"default" => true,
		);
		$colHeaders[] = $tmpAr;
	}
}
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $ASZOrder->GetNext())
{
	$row =& $adminList->AddRow($arRes["ID"], $arRes);
	
	//echo'<pre>';print_r( $arRes);echo'</pre>';
	
	$row->AddViewField("STATUS", $arRes["MLIFE_ASZ_ORDER_ADDSTAT_NAME"]);
	$row->AddViewField("PAY_ID", $arRes["MLIFE_ASZ_ORDER_ADDPAY_NAME"]);
	$row->AddViewField("DELIVERY_ID", $arRes["MLIFE_ASZ_ORDER_ADDDELIVERY_NAME"]);
	
	$row->AddViewField("PRICE", ASZ\CurencyFunc::priceFormat($arRes["PRICE"],$arRes["CURRENCY"],$arRes["SITEID"]));
	$row->AddViewField("DELIVERY_PRICE", ASZ\CurencyFunc::priceFormat($arRes["DELIVERY_PRICE"],$arRes["CURRENCY"],$arRes["SITEID"]));
	$row->AddViewField("PAYMENT_PRICE", ASZ\CurencyFunc::priceFormat($arRes["PAYMENT_PRICE"],$arRes["CURRENCY"],$arRes["SITEID"]));
	$row->AddViewField("TAX", ASZ\CurencyFunc::priceFormat($arRes["TAX"],$arRes["CURRENCY"],$arRes["SITEID"]));
	$row->AddViewField("DISCOUNT", ASZ\CurencyFunc::priceFormat($arRes["DISCOUNT"],$arRes["CURRENCY"],$arRes["SITEID"]));
	$row->AddViewField("DATE", ConvertTimeStamp($arRes["DATE"],"FULL"));

	$arActions = array();
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_DELETE"),
		"TITLE" => Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS("MLIFE_ASZ_ORDERLIST_MENU_DELETE_CONF")."')) ".$adminList->ActionDoGroup($arRes["ID"], "delete"),
	);
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_EDIT"),
		"TITLE"=>Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_EDIT"),
		"ACTION"=>$adminList->ActionRedirect('mlife_asz_order_edit.php?ID='.$arRes["ID"].'&lang='.LANG)
		);
	$row->AddActions($arActions);
}

// actions buttins
$adminList->AddGroupActionTable(array(
	"delete" => Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_DELETE"),
));

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $ASZOrder->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

//кнопка на панели
$aContext = array(
  /*array(
    "TEXT"=>Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_ADD"),
    "LINK"=>"mlife_asz_order_edit.php?lang=".LANG,
    "TITLE"=>Loc::getMessage("MLIFE_ASZ_ORDERLIST_MENU_ADD"),
    "ICON"=>"btn_new",
  ),*/
);

$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("MLIFE_ASZ_ORDERLIST_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<?
$adminList->DisplayList();
?>
<?//echo BeginNote();?>
<?//echo Loc::getMessage("MLIFE_ASZ_ORDERLIST_NOTE")?>
<?//echo EndNote();?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>