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

$listTableId = "tbl_mlife_asz_discount";

$oSort = new CAdminSorting($listTableId, "ID", "ASC");
$arOrder = (strtoupper($by) === "ID"? array($by => $order): array($by => $order, "ID" => "ASC"));

$adminList = new CAdminList($listTableId, $oSort);

// обработка одиночных и групповых действий
if(($arID = $adminList->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = Asz\DiscountTable::getList(
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
				
			$res = Asz\DiscountTable::delete(array("ID"=>$ID));
		}
	}
	
}
$arFilter = array();
if($FilterSiteId) {
	$arFilter["SITEID"] = $FilterSiteId;
}

$ASZDiscount = Asz\DiscountTable::getList(
	array(
		'order' => $arOrder,
		'filter' => $arFilter,
	)
);

$ASZDiscount = new CAdminResult($ASZDiscount, $listTableId);
$ASZDiscount->NavStart();

$adminList->NavText($ASZDiscount->GetNavPrint(Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_NAV")));

$cols = Asz\DiscountTable::getEntity()->getFields();
$colHeaders = array();

foreach ($cols as $colId => $col)
{
	$tmpAr = array(
		"id" => $col->getName(),
		"content" => $col->getTitle(),
		"sort" => $col->getName(),
		"default" => true,
	);
	$colHeaders[] = $tmpAr;
}
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $ASZDiscount->GetNext())
{
	$row =& $adminList->AddRow($arRes["ID"], $arRes);
$row->AddCheckField("ACTIVE", false);
	$arActions = array();
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_DELETE"),
		"TITLE" => Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS("MLIFE_ASZ_DISCOUNTLIST_MENU_DELETE_CONF")."')) ".$adminList->ActionDoGroup($arRes["ID"], "delete"),
	);
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_EDIT"),
		"TITLE"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_EDIT"),
		"ACTION"=>$adminList->ActionRedirect('mlife_asz_discount_edit.php?ID='.$arRes["ID"].'&lang='.LANG)
		);
	$row->AddActions($arActions);
}

// actions buttins
$adminList->AddGroupActionTable(array(
	"delete" => Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_DELETE"),
));

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $ASZDiscount->SelectedRowsCount()
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
    "TEXT"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_ADD"),
    "LINK"=>"mlife_asz_discount_edit.php?lang=".LANG,
    "TITLE"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_MENU_ADD"),
    "ICON"=>"btn_new",
  ),
);

$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("MLIFE_ASZ_DISCOUNTLIST_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<?
$adminList->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>