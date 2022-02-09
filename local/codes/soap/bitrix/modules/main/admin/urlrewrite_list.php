<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/urlrewrite_list.php");

if(!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_php');

IncludeModuleLangFile(__FILE__);

// ������������� �������
$sTableID = "tbl_urlrewrite";

// ������������� ����������
$oSort = new CAdminSorting($sTableID, "CONDITION", "asc");
// ������������� ������
$lAdmin = new CAdminList($sTableID, $oSort);

// ������������� ���������� ������ - �������
$arFilterFields = array(
	"filter_path",
	"filter_site_id",
	"filter_condition",
	"filter_id",
);

$lAdmin->InitFilter($arFilterFields);

if (StrLen($filter_site_id) <= 0)
{
	$set_filter = "Y";
	$filter_site_id = SITE_ID;
	$lAdmin->InitFilter($arFilterFields);
}

$arFilter = array();

if (StrLen($filter_site_id) > 0)	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_condition) > 0) $arFilter["CONDITION"] = $filter_condition;
if (strlen($filter_id) > 0) $arFilter["ID"] = $filter_id;
if (strlen($filter_path) > 0) $arFilter["PATH"] = $filter_path;

// ��������� �������� ��������� � ���������
if (($arID = $lAdmin->GroupAction()) && $isAdmin)
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CUrlRewriter::GetList(
			$arFilter
		);
		while ($arResult = $dbResultList->Fetch())
			$arID[] = $arResult["CONDITION"];
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);

				CUrlRewriter::Delete(array("SITE_ID" => $filter_site_id, "CONDITION" => $ID));

				break;
		}
	}
}

// ������������� ������ - ������� ������
$arResultList = CUrlRewriter::GetList($arFilter, array($by => $order));

$dbResultList = new CDBResult;
$dbResultList->InitFromArray($arResultList);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

// ��������� ���������� ������
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SAA_NAV")));

// ��������� ������
$lAdmin->AddHeaders(array(
	array("id"=>"CONDITION", "content"=>GetMessage("MURL_USL"), "sort"=>"CONDITION", "default"=>true),
	array("id"=>"ID","content"=>GetMessage("MURL_COMPONENT"), "sort"=>"ID", "default"=>true),
	array("id"=>"PATH", "content"=>GetMessage("MURL_FILE"),	"sort"=>"PATH", "default"=>true),
	array("id"=>"RULE", "content"=>GetMessage("MURL_RULE"), "sort"=>"RULE", "default"=>true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// ���������� ������
while ($arResult = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_CONDITION, $arResult, "urlrewrite_edit.php?CONDITION=".UrlEncode($arResult["CONDITION"])."&lang=".LANG."&site_id=".UrlEncode($filter_site_id), GetMessage("MURL_EDIT"));

	$row->AddField("CONDITION", $f_CONDITION);
	$row->AddField("ID", $f_ID);
	$row->AddField("PATH", $f_PATH);
	$row->AddField("RULE", $f_RULE);

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MURL_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("urlrewrite_edit.php?CONDITION=".UrlEncode($arResult["CONDITION"])."&lang=".LANG."&site_id=".UrlEncode($filter_site_id)), "DEFAULT"=>true);
	if($isAdmin)
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MURL_DELETE"), "ACTION"=>"if(confirm('".GetMessage("MURL_DELETE_CONF")."')) ".$lAdmin->ActionDoGroup(UrlEncode($arResult["CONDITION"]), "delete"));

	$row->AddActions($arActions);
}

// "������" ������
$arFooterArray = array(
	array(
		"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
		"value" => $dbResultList->SelectedRowsCount()
	),
	array(
		"counter" => true,
		"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
		"value" => "0"
	),
);

$lAdmin->AddFooter($arFooterArray);

// ����� ����� � �������� ����������, ...
$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	)
);

$arDDMenu = array();

$dbRes = CLang::GetList(($b="sort"), ($o="asc"));
while(($arRes = $dbRes->Fetch()))
{
	$arDDMenu[] = array(
		"TEXT" => htmlspecialcharsbx("[".$arRes["LID"]."] ".$arRes["NAME"]),
		"ACTION" => "window.location = 'urlrewrite_edit.php?lang=".urlencode(LANG)."&site_id=".urlencode($arRes["LID"])."';"
	);
}

$aContext = array(
	array(
		"TEXT" => GetMessage("MURL_NEW"),
		"TITLE" => GetMessage("MURL_NEW_TITLE"),
		"ICON" => "btn_new",
		"MENU" => $arDDMenu
	),
	array(
		"TEXT" => GetMessage("MURL_REINDEX"),
		"TITLE" => GetMessage("MURL_REINDEX_TITLE"),
		"LINK" => "urlrewrite_reindex.php?lang=".LANG.""
	),
);

$lAdmin->AddAdminContextMenu($aContext);

// �������� �� ����� ������ ������ (� ������ ������, ������ ������ ����������� �� �����)
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("MURL_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("MURL_FILTER_SITE"),
		GetMessage("MURL_USL"),
		GetMessage("MURL_COMPONENT"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage("MURL_FILTER_PATH") ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_path" size="50" value="<?= htmlspecialcharsEx($filter_path) ?>">
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("MURL_FILTER_SITE") ?>:</td>
		<td>
			<?echo CLang::SelectBox("filter_site_id", $filter_site_id) ?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("MURL_USL") ?>:</td>
		<td>
			<input type="text" name="filter_condition" size="50" value="<?= htmlspecialcharsEx($filter_condition) ?>">
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("MURL_COMPONENT") ?>:</td>
		<td>
			<input type="text" name="filter_id" size="50" value="<?= htmlspecialcharsEx($filter_id) ?>">
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
<?
// ����� ��� ������ ������
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>