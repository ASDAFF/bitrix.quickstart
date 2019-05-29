<?
$sModuleId = "step2use.redirects";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

global $DBType;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

// @todo HELP FILE
//define("HELP_FILE", "settings/s2u_redirect_list.php");

/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
$isAdmin = $USER->CanDoOperation('edit_php');*/

$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');
if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_s2u_REDIRECT";

// sites list
$ref = $ref_id = array();
$rs = CSite::GetList(($v1="sort"), ($v2="asc"));
while ($ar = $rs->Fetch()) {
	$ref[] = "[".$ar["ID"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arSiteDropdown = array("reference" => $ref, "reference_id" => $ref_id);

//-----------MAKE THE FILTER---------------------------------------------
$oSort = new CAdminSorting($sTableID, "DATE_TIME_CREATE", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
    'filter_id',
    'filter_old_link',
	'filter_date_time_create',
	'filter_active',
	'filter_comment',
    'filter_site_id',
	'filter_with_includes'
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
// SETTING THE FILTER CURRENT VALUES
if (StrLen($filter_id) > 0)
	$arFilter["ID"] = $filter_id;
if (StrLen($filter_old_link) > 0)
	$arFilter["OLD_LINK"] = $filter_old_link;
if (strlen($filter_date_time_create) > 0)
	$arFilter["DATE_TIME_CREATE"] = $filter_date_time_create;
if (strlen($filter_active) > 0)
	$arFilter["ACTIVE"] = $filter_active;
if (strlen($filter_site_id) > 0)
	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_with_includes) > 0)
	$arFilter["SITE_ID"] = $filter_with_includes;

// GROUP ACTIONS
if (($arID = $lAdmin->GroupAction()) && $isAdmin) {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = Array();
		$dbResultList = S2uRedirects404IgnoreDB::GetList($arFilter);
		foreach ($dbResultList as $v)
			$arID[] = $v["ID"];
	}
    
	foreach ($arID as $ID) {
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				S2uRedirects404IgnoreDB::Delete($ID);
				break;
		}
	}
}
// LOAD DATA

$arResultList = S2uRedirects404IgnoreDB::GetList($arFilter, array($by => $order));
$dbResultList = new CDBResult;
$dbResultList->InitFromArray($arResultList);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

// NAV PARAMS
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SAA_NAV")));

// THE LIST HEADER
$lAdmin->AddHeaders(array(
        //array("id" => "ID", "content" => '#', "sort" => "ID", "default" => true),
        array("id" => "SITE_ID", "content" => GetMessage('MURL_FILTER_SITE'), "sort" => "SITE_ID", "default" => true),
		array("id" => "OLD_LINK", "content" => GetMessage('OLD_LINK'), "sort" => "OLD_LINK", "default" => true),
		array("id" => "ACTIVE", "content" => GetMessage('ACTIVE'), "sort" => "ACTIVE", "default" => true),
		array("id" => "DATE_TIME_CREATE", "content" => GetMessage('DATE_TIME_GENERATE'), "sort" => "DATE_TIME_CREATE", "default" => true),
		array("id" => "COMMENT", "content" => GetMessage('COMMENT'), "sort" => "COMMENT", "default" => true),
		array("id" => "WITH_INCLUDES", "content" => GetMessage('WITH_INCLUDES'), "sort" => "WITH_INCLUDES", "default" => true),

));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// MAKE THE LIST
while ($arResult = $dbResultList->NavNext(true, "f_")) {
	$row = & $lAdmin->AddRow($f_ID, $arResult, "step2use_redirects_404_ignore_edit.php?ID=" . UrlEncode($arResult["ID"]) . "&lang=" . LANG, GetMessage("MURL_EDIT"));

    //$row->AddField("ID", $f_ID);
    $row->AddField("SITE_ID", $f_SITE_ID);
	$row->AddField("OLD_LINK", $f_OLD_LINK);
	$row->AddField("DATE_TIME_CREATE", $f_DATE_TIME_CREATE);
	$row->AddField("ACTIVE", ($f_ACTIVE=='Y')? GetMessage('MAIN_YES'): GetMessage('MAIN_NO'));
	$row->AddField("COMMENT", $f_COMMENT);
	$row->AddField("WITH_INCLUDES", ($f_WITH_INCLUDES=='Y')? GetMessage('MAIN_YES'): GetMessage('MAIN_NO'));

	//CONTEXT MENU
	$arActions = Array();
	$arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("MURL_EDIT"), "ACTION" => $lAdmin->ActionRedirect("step2use_redirects_404_ignore_edit.php?ID=" . UrlEncode($arResult["ID"]) . "&lang=" . LANG), "DEFAULT" => true);
	if ($isAdmin)
		$arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MURL_DELETE"), "ACTION" => "if(confirm('" . GetMessage("MURL_DELETE_CONF") . "')) " . $lAdmin->ActionDoGroup(UrlEncode($arResult["ID"]), "delete"));

	$row->AddActions($arActions);
}

// FOOTER
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

$lAdmin->AddGroupActionTable(array(
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

$arDDMenu = array();

$dbRes = CLang::GetList(($b = "sort"), ($o = "asc"));
while (($arRes = $dbRes->Fetch())) {
	$arDDMenu[] = array(
			"TEXT" => htmlspecialcharsbx("[" . $arRes["LID"] . "] " . $arRes["NAME"]),
			"ACTION" => "window.location = 'step2use_redirects_404_ignore_edit.php?ADD=Y&lang=" . urlencode(LANG) . "&site_id=" . $arRes["LID"] . "';"
	);
}

$aContext = array(
		array(
				"TEXT" => GetMessage("MURL_NEW"),
				"TITLE" => GetMessage("MURL_NEW_TITLE"),
				"ICON" => "btn_new",
				"MENU" => $arDDMenu
		),
);

$lAdmin->AddAdminContextMenu($aContext);

// IF SHOW LIST ONLY
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("MURL_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

echo S2uRedirects::getLicenseRenewalBanner();
?>
<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
<?
$oFilter = new CAdminFilter(
								$sTableID . "_filter",
								array(
										GetMessage('NEW_LINK'),
										GetMessage('ACTV'),
                                        GetMessage('MURL_FILTER_SITE'),
								)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage('TABL_OLD_LINK') ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_old_link" size="50" value="<?= htmlspecialcharsEx($filter_old_link) ?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage('TABLE_ACTIVE') ?>:</td>
		<td>
			<input type="text" name="filter_active" size="50" value="<?= htmlspecialcharsEx($filter_active) ?>">
		</td>
	</tr>
    <tr>
        <td><?echo GetMessage("MURL_FILTER_SITE")?>:</td>
        <td><?echo SelectBoxFromArray("filter_site_id", $arSiteDropdown, $find_site_id, GetMessage("MAIN_ALL"));?></td>
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

    <? echo BeginNote(); ?>
    <? echo GetMessage("ABOUT_IGNORE") ?>
    <? echo EndNote(); ?>

	<?
    
// DISPLAY LIST
	$lAdmin->DisplayList();

	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	?>
