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

$listTableId = "tbl_mlife_asz_quant";

$oSort = new CAdminSorting($listTableId, "PRODID", "ASC");
$arOrder = (strtoupper($by) === "PRODID"? array($by => $order): array($by => $order, "PRODID" => "ASC"));

$adminList = new CAdminList($listTableId, $oSort);

// обработка одиночных и групповых действий
if(($arID = $adminList->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = Asz\QuantTable::getList(
			array(
				'order' => $arOrder,
				'select' => array('PRODID'),
			)
		);
		while($arRes = $rsData->Fetch())
		  $arID[] = $arRes['PRODID'];
	}
	
	if($_REQUEST['action']=="delete") {
		foreach($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;
				$ID = IntVal($ID);
				
			$res = Asz\QuantTable::delete(array("PRODID"=>$ID));
		}
	}
	
}
/*
$arFilter = array();
if($FilterSiteId) {
	$arFilter["SITEID"] = $FilterSiteId;
}*/

$ASZCountry = Asz\QuantTable::getList(
	array(
		'select' => array("*","NAME"=>"EL.NAME"),
		'order' => $arOrder,
		//'filter' => $arFilter,
	)
);

$ASZCountry = new CAdminResult($ASZCountry, $listTableId);
$ASZCountry->NavStart();

$adminList->NavText($ASZCountry->GetNavPrint(Loc::getMessage("MLIFE_ASZ_QUANTLIST_NAV")));

$cols = Asz\QuantTable::getMap();
$colHeaders = array();

foreach ($cols as $colId => $col)
{
	if($colId!="EL"){
	$tmpAr = array(
		"id" => $colId,
		"content" => $col["title"],
		"sort" => $colId,
		"default" => true,
	);
	$colHeaders[] = $tmpAr;
	}
}
$colHeaders[] = array(
	"id" => "EL.NAME",
	"content" => "Наименование",
	"sort" => "EL.NAME",
	"default" => true,
);
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $ASZCountry->GetNext())
{
	$row =& $adminList->AddRow($arRes["PRODID"], $arRes);
	//print_r($arRes);
		
	$row->AddViewField("EL.NAME", $arRes["NAME"]);
	
	$arActions = array();
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>Loc::getMessage("MLIFE_ASZ_QUANTLIST_MENU_EDIT"),
		"TITLE"=>Loc::getMessage("MLIFE_ASZ_QUANTLIST_MENU_EDIT"),
		"ACTION"=>$adminList->ActionRedirect('mlife_asz_country_edit.php?ID='.$arRes["ID"].'&lang='.LANG)
		);
	$row->AddActions($arActions);
}

// actions buttins
$adminList->AddGroupActionTable(array(
	"edit" => Loc::getMessage("MLIFE_ASZ_QUANTLIST_MENU_DELETE"),
));

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $ASZCountry->SelectedRowsCount()
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
    "TEXT"=>Loc::getMessage("MLIFE_ASZ_QUANTLIST_MENU_ADD"),
    "LINK"=>"mlife_asz_country_edit.php?lang=".LANG,
    "TITLE"=>Loc::getMessage("MLIFE_ASZ_QUANTLIST_MENU_ADD"),
    "ICON"=>"btn_new",
  ),
);

$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("MLIFE_ASZ_QUANTLIST_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<?
$adminList->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>