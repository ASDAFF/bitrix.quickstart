<?
$sModuleId = "step2use.redirects";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

global $DBType;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);?>
<?// @todo HELP FILE
//define("HELP_FILE", "settings/s2u_redirect_list.php");

/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

//$MODULE_RIGHT = $APPLICATION->GetUserRight($sModuleId);
//var_dump($MODULE_RIGHT);

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
	'filter_new_link',
	'filter_date_time_create_from',
	'filter_date_time_create_to',
	'filter_status',
	'filter_active',
	'filter_comment',
    'filter_site_id'
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
// SETTING THE FILTER CURRENT VALUES
if (StrLen($filter_id) > 0)
	$arFilter["ID"] = $filter_id;
if (StrLen($filter_old_link) > 0)
	$arFilter["OLD_LINK"] = $filter_old_link;
if (strlen($filter_new_link) > 0)
	$arFilter["NEW_LINK"] = $filter_new_link;
if (strlen($filter_date_time_create_to) > 0)
	$arFilter["DATE_TIME_CREATE_TO"] = $filter_date_time_create_to;
if (strlen($filter_date_time_create_from) > 0)
	$arFilter["DATE_TIME_CREATE_FROM"] = $filter_date_time_create_from;
if (strlen($filter_status) > 0)
	$arFilter["STATUS"] = $filter_status;
if (strlen($filter_active) > 0)
	$arFilter["ACTIVE"] = $filter_active;
if (strlen($filter_site_id) > 0)
	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_comment) > 0)
	$arFilter["COMMENT"] = $filter_comment;

$arStatusDropdown = array();
$arStatusDropdown["REFERENCE_ID"] = array("301", "302", "303", "410");
$arStatusDropdown["REFERENCE"] = array(GetMessage("STATUS_301"), GetMessage("STATUS_302"), GetMessage("STATUS_303"), GetMessage("STATUS_410"));
$arActiveDropdown = array();
$arActiveDropdown["REFERENCE_ID"] = array("Y", "N");
$arActiveDropdown["REFERENCE"] = array(GetMessage("S2U_Y"), GetMessage("S2U_N"));


// GROUP ACTIONS
if (($arID = $lAdmin->GroupAction()) && $isAdmin) {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = Array();
		$dbResultList = S2uRedirectsRulesDB::GetList($arFilter);
		foreach ($dbResultList as $v)
			$arID[] = $v["ID"];
	}
    
	foreach ($arID as $ID) {
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				S2uRedirectsRulesDB::Delete($ID);
				break;
			case "activate":
			case "deactivate":
				$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
				S2uRedirectsRulesDB::Update($ID, $arFields);
		}
	}
}

// LOAD DATA

$arResultList = S2uRedirectsRulesDB::GetList($arFilter, array($by => $order));
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
		array("id" => "NEW_LINK", "content" => GetMessage('NEW_LINK'), "sort" => "NEW_LINK", "default" => true),
        array("id" => "STATUS", "content" => GetMessage('STATUS'), "sort" => "STATUS", "default" => true),
		array("id" => "ACTIVE", "content" => GetMessage('ACTIVE'), "sort" => "ACTIVE", "default" => true),
		array("id" => "DATE_TIME_CREATE", "content" => GetMessage('DATE_TIME_GENERATE'), "sort" => "DATE_TIME_CREATE", "default" => true),
		array("id" => "COMMENT", "content" => GetMessage('COMMENT'), "sort" => "COMMENT", "default" => true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// MAKE THE LIST
while ($arResult = $dbResultList->NavNext(true, "f_")) {
	$row = & $lAdmin->AddRow($f_ID, $arResult, "step2use_redirects_edit.php?ID=" . UrlEncode($arResult["ID"]) . "&lang=" . LANG, GetMessage("MURL_EDIT"));

    //$row->AddField("ID", $f_ID);
    $row->AddField("SITE_ID", $f_SITE_ID);
	$row->AddField("OLD_LINK", $f_OLD_LINK);
	$row->AddField("NEW_LINK", $f_NEW_LINK);
	$row->AddField("DATE_TIME_CREATE", $f_DATE_TIME_CREATE);
	$row->AddField("STATUS", (GetMessage("STATUS_$f_STATUS")? GetMessage("STATUS_$f_STATUS"): $f_STATUS));
	$row->AddField("ACTIVE", ($f_ACTIVE=='Y')? GetMessage('MAIN_YES'): GetMessage('MAIN_NO'));
	$row->AddField("COMMENT", $f_COMMENT);

	//CONTEXT MENU
	$arActions = Array();
	$arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("MURL_EDIT"), "ACTION" => $lAdmin->ActionRedirect("step2use_redirects_edit.php?ID=" . UrlEncode($arResult["ID"]) . "&lang=" . LANG), "DEFAULT" => true);
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
	"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE")
));
 
$arDDMenu = array();

$dbRes = CLang::GetList(($b = "sort"), ($o = "asc"));
while (($arRes = $dbRes->Fetch())) {
	$arDDMenu[] = array(
			"TEXT" => htmlspecialcharsbx("[" . $arRes["LID"] . "] " . $arRes["NAME"]),
			"ACTION" => "window.location = 'step2use_redirects_edit.php?ADD=Y&lang=" . urlencode(LANG) . "&site_id=" . $arRes["LID"] . "';"
	);
}

$aContext = array(
		array(
				"TEXT" => GetMessage("MURL_NEW"),
				"TITLE" => GetMessage("MURL_NEW_TITLE"),
				"ICON" => "btn_new",
				"MENU" => $arDDMenu
		)
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
			<input type="text" name="filter_old_link" size="50" value="<?= htmlspecialcharsEx($filter_old_link)?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage('TABL_NEW_LINK') ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_new_link" size="50" value="<?= htmlspecialcharsEx($filter_new_link) ?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage('TABLE_ACTIVE') ?>:</td>
		<td>
			<?echo SelectBoxFromArray("filter_active", $arActiveDropdown, $filter_active, GetMessage("MAIN_ALL"));?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage('TABL_COMMENT') ?>:</td>
		<td>
			<input type="text" name="filter_comment" size="50" value="<?= htmlspecialcharsEx($filter_comment) ?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	
	
	<tr>
		<td><?echo GetMessage("TABL_DATE")?>:</td>
		<td><?echo CalendarPeriod("filter_date_time_create_from", "", "filter_date_time_create_to", "", "find_form")?></td>
	</tr>
	
	
	<tr>
        <td><?echo GetMessage("TABL_STATUS")?>:</td>
        <td><?echo SelectBoxFromArray("filter_status", $arStatusDropdown, $filter_status, GetMessage("MAIN_ALL"));?></td>
    </tr>
    <tr>
        <td><?echo GetMessage("MURL_FILTER_SITE")?>:</td>
        <td><?echo SelectBoxFromArray("filter_site_id", $arSiteDropdown, $filter_site_id, GetMessage("MAIN_ALL"));?></td>
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
    
// DISPLAY LIST
	$lAdmin->DisplayList();

	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	?>
