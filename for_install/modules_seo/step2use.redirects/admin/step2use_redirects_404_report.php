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
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');
if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_s2u_redirects_404";

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

$arResultList = S2uRedirects404DB::GetReport($arFilter, array($by => $order), true);
$dbResultList = new CDBResult;
$dbResultList->InitFromArray($arResultList);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

// NAV PARAMS
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SAA_NAV")));

// THE LIST HEADER
$lAdmin->AddHeaders(array(
        array("id" => "IS_REDIRECTED", "content" => GetMessage('S2U_IS_REDRECTED'), "sort" => "IS_REDIRECTED", "default" => true),
        array("id" => "SITE_ID", "content" => GetMessage('S2U_SITE'), "sort" => "SITE_ID", "default" => true),
		array("id" => "URL", "content" => GetMessage('S2U_URL'), "sort" => "URL", "default" => true),
        array("id" => "CNT", "content" => GetMessage('S2U_CNT'), "sort" => "CNT", "default" => true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// MAKE THE LIST
while ($arResult = $dbResultList->NavNext(true, "f_")) {
    $createRuleURL = "step2use_redirects_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL)."&backurl=".urlencode($APPLICATION->GetCurUri());
    $ignoreURL = "step2use_redirects_404_ignore_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL);
	$row = & $lAdmin->AddRow($f_ID, $arResult, $createRuleURL, GetMessage("S2U_CREATE_RULE"));

    //$row->AddField("ID", $f_ID);
    $row->AddField("IS_REDIRECTED", '<img src="/bitrix/images/s2u_redirects/'.(($f_IS_REDIRECTED=='Y')? 'green.gif': 'red.gif').'" title="'.(($f_IS_REDIRECTED=='Y')? GetMessage('S2U_IS_REDRECTED_TITLE_Y'): GetMessage('S2U_IS_REDRECTED_TITLE_N')).'"/>' );
    $row->AddField("SITE_ID", $f_SITE_ID);
	$row->AddField("URL", $f_URL);
    $row->AddField("CNT", $f_CNT);

	//CONTEXT MENU
	$arActions = Array();
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("S2U_CREATE_RULE"), "ACTION" => $lAdmin->ActionRedirect($createRuleURL), "DEFAULT" => true);
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("S2U_ADD_TO_IGNORE"), "ACTION" => $lAdmin->ActionRedirect($ignoreURL), "DEFAULT" => false);
    $arActions[] = array("ICON" => "list", "TEXT" => GetMessage("S2U_SHOW_LIST"), "ACTION" => $lAdmin->ActionRedirect("step2use_redirects_404_list.php?lang=".LANG."&set_filter=Y&filter_url=".  urlencode($f_URL)), "DEFAULT" => false);

	$row->AddActions($arActions);
}

//CSV EXPORT
if($_REQUEST["export_csv"]){
	
	header('Content-Type: text/csv; charset=windows-1251');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=export.csv");
	
	$array = array (
		array(GetMessage("FILE_FORMAT")),
		array(GetMessage("FROM"), GetMessage("TO"), GetMessage("COMMENT"), GetMessage("ALL_ENTRY"))
	);
	
	foreach ($arResultList as $url){
		array_push($array, array($url["URL"]));
	}	
	
	foreach($array as &$arrStr){		
		foreach ($arrStr as &$str){
			$str = iconv('utf-8','windows-1251',$str);					
		}
	}	
	
	$df = fopen("php://output", 'w');
	foreach ($array as $row) {
		fputcsv($df, $row, ';');
	}
	fclose($df);
	
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
	
	die();
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
				"TEXT" => GetMessage("S2U_LIST"),
				"TITLE" => GetMessage("S2U_LIST_TITLE"),
				"ICON" => "",
				"LINK" => 'step2use_redirects_404_list.php?lang='.urlencode(LANG)
		),
		array(
				"TEXT" => GetMessage("CSV_EXPORT"),
				"TITLE" => GetMessage("CSV_EXPORT"),
				"LINK" => 'step2use_redirects_404_report.php?lang='.urlencode(LANG)."&export_csv=y"
		)
);

$lAdmin->AddAdminContextMenu($aContext);

// IF SHOW LIST ONLY
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("S2U_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
echo S2uRedirects::getLicenseRenewalBanner();
?>
<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
<?
$oFilter = new CAdminFilter(
								$sTableID . "_filter",
								array(
                                        GetMessage('S2U_SITE'),
                                        GetMessage('S2U_IS_REDRECTED_EXISTS'),
								)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage('S2U_URL') ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_url" size="50" value="<?= htmlspecialcharsEx($filter_url) ?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
    <tr>
        <td><?echo GetMessage("S2U_SITE")?>:</td>
        <td><?echo SelectBoxFromArray("filter_site_id", $arSiteDropdown, $find_site_id, GetMessage("MAIN_ALL"));?></td>
    </tr>
    <tr>
        <td><?= GetMessage('S2U_IS_REDRECTED_EXISTS') ?>:</td>
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
