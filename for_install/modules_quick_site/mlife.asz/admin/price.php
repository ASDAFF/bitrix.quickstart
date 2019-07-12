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
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$listTableId = "tbl_asz_price";

$oSort = new CAdminSorting($listTableId, "ID", "ASC");
$arOrder = (strtoupper($by) === "ID"? array($by => $order): array($by => $order, "ID" => "ASC"));

$adminList = new CAdminList($listTableId, $oSort);

// обработка одиночных и групповых действий
if(($arID = $adminList->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = \Mlife\Asz\PricetipTable::getList(
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
			$res2 = \Mlife\Asz\PricetiprightTable::deleteright($ID);
			$res = \Mlife\Asz\PricetipTable::delete(array("ID"=>$ID));
		}
	}
	
}
$arFilter = array();
if($FilterSiteId) {
	$arFilter["SITE_ID"] = $FilterSiteId;
}
//\Bitrix\Main\Application::getInstance()->getConnectionPool()->getConnection()->startTracker(true); 
$ASZPrice = \Mlife\Asz\PricetipTable::getList(
	array(
		'select' => array("ID","CODE","NAME","BASE","GROUP","SITE_ID"),
		'order' => $arOrder,
		'filter' => $arFilter,
	)
);

//print_r($ASZPrice->getTrackerQuery()->getSql());
$ASZPrice = new CAdminResult($ASZPrice, $listTableId);
//\Bitrix\Main\Application::getInstance()->getConnectionPool()->getConnection()->stopTracker();
$ASZPrice->NavStart();

$adminList->NavText($ASZPrice->GetNavPrint(Loc::getMessage("MLIFE_ASZ_PRICELIST_NAV")));

$cols = \Mlife\Asz\PricetipTable::getEntity()->getFields();
$colHeaders = array();

foreach ($cols as $colId => $col)
{
	$tmpAr = array(
		"id" => $col->getName(),
		"content" => $col->getTitle(),
		"sort" => $col->getName(),
		"default" => true,
	);
	if($tmpAr['id']!="PRICETIPRIGHT"){
		$colHeaders[] = $tmpAr;
	}
}
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $ASZPrice->GetNext())
{
	//print_r($arRes);
	$row =& $adminList->AddRow($arRes["ID"], $arRes);
	$row->AddViewField("GROUP",implode(" / ",$arRes["GROUP"]));
	$row->AddCheckField("BASE", false);

	$arActions = array();
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_DELETE"),
		"TITLE" => Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS("MLIFE_ASZ_PRICELIST_MENU_DELETE_CONF")."')) ".$adminList->ActionDoGroup($arRes["ID"], "delete"),
	);
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_EDIT"),
		"TITLE"=>Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_EDIT"),
		"ACTION"=>$adminList->ActionRedirect('mlife_asz_price_edit.php?ID='.$arRes["ID"].'&lang='.LANG)
		);
	$row->AddActions($arActions);
}

// actions buttins
$adminList->AddGroupActionTable(array(
	"delete" => Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_DELETE"),
));

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $ASZPrice->SelectedRowsCount()
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
  array(
    "TEXT"=>Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_ADD"),
    "LINK"=>"mlife_asz_price_edit.php?lang=".LANG,
    "TITLE"=>Loc::getMessage("MLIFE_ASZ_PRICELIST_MENU_ADD"),
    "ICON"=>"btn_new",
  ),
);

$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("MLIFE_ASZ_PRICELIST_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<?
$adminList->DisplayList();
?>
<?echo BeginNote();?>
<?echo Loc::getMessage("MLIFE_ASZ_PRICELIST_NOTE")?>
<?echo EndNote();?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>