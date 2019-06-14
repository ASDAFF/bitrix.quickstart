<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

$sModuleId = "asdaff.redirects";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

global $DBType;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

// @todo HELP FILE
//define("HELP_FILE", "settings/seo2_redirect_list.php");

if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_php');

IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_seo2_redirects_404";

// sites list
$ref = $ref_id = array();
$rs = CSite::GetList(($v1="sort"), ($v2="asc"));
while ($ar = $rs->Fetch()) {
	$ref[] = "[".$ar["ID"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arSiteDropdown = array("reference" => $ref, "reference_id" => $ref_id);

//-----------MAKE THE FILTER---------------------------------------------
$oSort = new CAdminSorting($sTableID, "CNT", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
    'filter_url',
    'filter_site_id',
    'filter_is_redirected',
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
// SETTING THE FILTER CURRENT VALUES
if (StrLen($filter_id) > 0)
	$arFilter["ID"] = $filter_id;
if (StrLen($filter_url) > 0)
	$arFilter["URL"] = $filter_url;
if (strlen($filter_site_id) > 0)
	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_is_redirected) > 0)
	$arFilter["IS_REDIRECTED"] = $filter_is_redirected;

// LOAD DATA

$arResultList = seo2Redirects404DB::GetReport($arFilter, array($by => $order), true);
$dbResultList = new CDBResult;
$dbResultList->InitFromArray($arResultList);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

// NAV PARAMS
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SAA_NAV")));

// THE LIST HEADER
$lAdmin->AddHeaders(array(
        array("id" => "IS_REDIRECTED", "content" => GetMessage('SEO2_IS_REDRECTED'), "sort" => "IS_REDIRECTED", "default" => true),
        array("id" => "SITE_ID", "content" => GetMessage('SEO2_SITE'), "sort" => "SITE_ID", "default" => true),
		array("id" => "URL", "content" => GetMessage('SEO2_URL'), "sort" => "URL", "default" => true),
        array("id" => "CNT", "content" => GetMessage('SEO2_CNT'), "sort" => "CNT", "default" => true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// MAKE THE LIST
while ($arResult = $dbResultList->NavNext(true, "f_")) {
    $createRuleURL = "asdaff_redirects_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL);
    $ignoreURL = "asdaff_redirects_404_ignore_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL);
	$row = & $lAdmin->AddRow($f_ID, $arResult, $createRuleURL, GetMessage("SEO2_CREATE_RULE"));

    //$row->AddField("ID", $f_ID);
    $row->AddField("IS_REDIRECTED", '<img src="/bitrix/images/seo2_redirects/'.(($f_IS_REDIRECTED=='Y')? 'green.gif': 'red.gif').'" title="'.(($f_IS_REDIRECTED=='Y')? GetMessage('SEO2_IS_REDRECTED_TITLE_Y'): GetMessage('SEO2_IS_REDRECTED_TITLE_N')).'"/>' );
    $row->AddField("SITE_ID", $f_SITE_ID);
	$row->AddField("URL", $f_URL);
    $row->AddField("CNT", $f_CNT);

	//CONTEXT MENU
	$arActions = Array();
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("SEO2_CREATE_RULE"), "ACTION" => $lAdmin->ActionRedirect($createRuleURL), "DEFAULT" => true);
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("SEO2_ADD_TO_IGNORE"), "ACTION" => $lAdmin->ActionRedirect($ignoreURL), "DEFAULT" => false);
    $arActions[] = array("ICON" => "list", "TEXT" => GetMessage("SEO2_SHOW_LIST"), "ACTION" => $lAdmin->ActionRedirect("asdaff_redirects_404_list.php?lang=".LANG."&set_filter=Y&filter_url=".  urlencode($f_URL)), "DEFAULT" => false);

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

$aContext = array(
		array(
				"TEXT" => GetMessage("SEO2_LIST"),
				"TITLE" => GetMessage("SEO2_LIST_TITLE"),
				"ICON" => "",
				"LINK" => 'asdaff_redirects_404_list.php?lang='.urlencode(LANG)
		),
);

$lAdmin->AddAdminContextMenu($aContext);

// IF SHOW LIST ONLY
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("SEO2_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

?>
<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
<?
$oFilter = new CAdminFilter(
								$sTableID . "_filter",
								array(
                                        GetMessage('SEO2_SITE'),
                                        GetMessage('SEO2_IS_REDRECTED_EXISTS'),
								)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage('SEO2_URL') ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_url" size="50" value="<?= htmlspecialcharsEx($filter_url) ?>">
		</td>
	</tr>
    <tr>
        <td><?echo GetMessage("SEO2_SITE")?>:</td>
        <td><?echo SelectBoxFromArray("filter_site_id", $arSiteDropdown, $find_site_id, GetMessage("MAIN_ALL"));?></td>
    </tr>
    <tr>
        <td><?= GetMessage('SEO2_IS_REDRECTED_EXISTS') ?>:</td>
		<td><?echo SelectBoxFromArray("filter_is_redirected", array(
                'REFERENCE' => array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), 
                'REFERENCE_ID' => array('Y', 'N')
              ), $filter_is_redirected, GetMessage("MAIN_ALL"));?></td>
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