<?
$sModuleId = "step2use.redirects";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

global $DBType;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

$statisticIsAvalible = CModule::IncludeModule('statistic');

// @todo HELP FILE
//define("HELP_FILE", "settings/s2u_redirect_list.php");

/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

//$isAdmin = $USER->CanDoOperation('edit_php');
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
$oSort = new CAdminSorting($sTableID, "DATE_TIME_CREATE", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
    'filter_id',
    'filter_url',
	'filter_date_time_create',
    'filter_referer_url',
	//'filter_redirect_status',
    'filter_site_id',
    'filter_date1_period',
    'filter_date2_period',
    'filter_is_redirected'
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
// SETTING THE FILTER CURRENT VALUES
if (StrLen($filter_id) > 0)
	$arFilter["ID"] = $filter_id;
if (StrLen($filter_url) > 0)
	$arFilter["URL"] = $filter_url;
if (strlen($filter_date_time_create) > 0)
	$arFilter["DATE_TIME_CREATE"] = $filter_date_time_create;
if (strlen($filter_referer_url) > 0)
	$arFilter["REFERER_URL"] = $filter_referer_url;
//if (strlen($filter_redirect_status) > 0)
//	$arFilter["REDIRECT_STATUS"] = $filter_redirect_status;
if (strlen($filter_site_id) > 0)
	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_date1_period) > 0)
	$arFilter[">=DATE_TIME_CREATE"] = ConvertDateTime($filter_date1_period,"Y-M-D");
if (strlen($filter_date2_period) > 0)
	$arFilter["<=DATE_TIME_CREATE"] = ConvertDateTime($filter_date2_period,"Y-M-D").' 23:59:59';
if (strlen($filter_is_redirected) > 0)
	$arFilter["IS_REDIRECTED"] = $filter_is_redirected;



// GROUP ACTIONS
if (($arID = $lAdmin->GroupAction()) && $isAdmin) {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = Array();
		$dbResultList = S2uRedirects404DB::GetList($arFilter);
		foreach ($dbResultList as $v)
			$arID[] = $v["ID"];
	}
    
	foreach ($arID as $ID) {
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				S2uRedirects404DB::Delete($ID);
				break;
		}
	}
}
// LOAD DATA


$arResultList = S2uRedirects404DB::GetList($arFilter, array($by => $order), true);
$dbResultList = new CDBResult;
$dbResultList->InitFromArray($arResultList);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

// NAV PARAMS
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SAA_NAV")));

// THE LIST HEADER
$lAdminHeader = array(
    array("id" => "IS_REDIRECTED", "content" => GetMessage('S2U_IS_REDRECTED'), "sort" => "IS_REDIRECTED", "default" => true),
    array("id" => "SITE_ID", "content" => GetMessage('S2U_SITE'), "sort" => "SITE_ID", "default" => true),
	array("id" => "URL", "content" => GetMessage('S2U_URL'), "sort" => "URL", "default" => true),
	array("id" => "REFERER_URL", "content" => GetMessage('S2U_REFERER_URL'), "sort" => "REFERER_URL", "default" => true),
    //array("id" => "REDIRECT_STATUS", "content" => GetMessage('S2U_REDIRECT_STATUS'), "sort" => "REDIRECT_STATUS", "default" => true),
	array("id" => "DATE_TIME_CREATE", "content" => GetMessage('S2U_DATE_TIME_GENERATE'), "sort" => "DATE_TIME_CREATE", "default" => true),
);
// show guest if statistic module is enabled
if($statisticIsAvalible) $lAdminHeader[] = array("id" => "GUEST_ID", "content" => GetMessage('S2U_GUEST'), 'default'=>true);
$lAdmin->AddHeaders($lAdminHeader);

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

// MAKE THE LIST
while ($arResult = $dbResultList->NavNext(true, "f_")) {
    $createRuleURL = "step2use_redirects_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL);
    $ignoreURL = "step2use_redirects_404_ignore_edit.php?ADD=Y&lang=".urlencode(LANG)."&site_id=$f_SITE_ID&OLD_LINK=".urlencode($f_URL);
	$row = & $lAdmin->AddRow($f_ID, $arResult, $createRuleURL, GetMessage("S2U_CREATE_RULE"));

    //$row->AddField("ID", $f_ID);
    $row->AddField("IS_REDIRECTED", '<img src="/bitrix/images/s2u_redirects/'.(($f_IS_REDIRECTED=='Y')? 'green.gif': 'red.gif').'" title="'.(($f_IS_REDIRECTED=='Y')? GetMessage('S2U_IS_REDRECTED_TITLE_Y'): GetMessage('S2U_IS_REDRECTED_TITLE_N')).'"/>' );
    $row->AddField("SITE_ID", $f_SITE_ID);
	$row->AddField("URL", $f_URL);
	$row->AddField("REFERER_URL", ($f_REFERER_URL)? '<a href="'.$f_REFERER_URL.'" target="_blank">'.$f_REFERER_URL.'</a>': '');
	//$row->AddField("REDIRECT_STATUS", $f_REDIRECT_STATUS);
    $row->AddField("DATE_TIME_CREATE", $f_DATE_TIME_CREATE);
    
    // guest
    $guestUrl = ($statisticIsAvalible && $f_GUEST_ID)? '<a href="#" class="popupitem" onclick="javascript:CloseWaitWindow(); jsUtils.OpenWindow(\'guest_detail.php?lang=ru&amp;ID='.$f_GUEST_ID.'\', \'700\', \'550\'); return false;">'.GetMessage('S2U_GUEST').'</a>': '';
    $row->AddField("GUEST_ID", $guestUrl);

	//CONTEXT MENU
	$arActions = Array();
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("S2U_CREATE_RULE"), "ACTION" => $lAdmin->ActionRedirect($createRuleURL), "DEFAULT" => true);
    $arActions[] = array("ICON" => "new", "TEXT" => GetMessage("S2U_ADD_TO_IGNORE"), "ACTION" => $lAdmin->ActionRedirect($ignoreURL), "DEFAULT" => false);
	if ($isAdmin)
		$arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_DELETE"), "ACTION" => "if(confirm('" . GetMessage("S2U_DELETE_MESS") . "')) " . $lAdmin->ActionDoGroup(UrlEncode($arResult["ID"]), "delete"));

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

$aContext = array(
		array(
				"TEXT" => GetMessage("S2U_REPORT"),
				"TITLE" => GetMessage("S2U_REPORT_TITLE"),
				"ICON" => "",
				"LINK" => 'step2use_redirects_404_report.php?lang='.urlencode(LANG)
		),
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
										GetMessage('S2U_REFERER_URL'),
                                        //GetMessage('S2U_REDIRECT_STATUS'),
                                        GetMessage('S2U_SITE'),
                                        GetMessage('S2U_IS_REDRECTED_EXISTS'),
                                        GetMessage('S2U_DATE_TIME_GENERATE'),
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
		<td><?= GetMessage('S2U_REFERER_URL') ?>:</td>
		<td align="left" nowrap>
			<input type="text" name="filter_referer_url" size="50" value="<?= htmlspecialcharsEx($filter_referer_url) ?>">
			&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
    <tr>
        <td><?echo GetMessage("S2U_SITE")?>:</td>
        <td><?echo SelectBoxFromArray("filter_site_id", $arSiteDropdown, $filter_site_id, GetMessage("MAIN_ALL"));?></td>
    </tr>
    <tr>
        <td><?= GetMessage('S2U_IS_REDRECTED_EXISTS') ?>:</td>
		<td><?echo SelectBoxFromArray("filter_is_redirected", array(
                'REFERENCE' => array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), 
                'REFERENCE_ID' => array('Y', 'N')
              ), $filter_is_redirected, GetMessage("MAIN_ALL"));?></td>
    </tr>
    <tr>
        <td><?echo GetMessage("S2U_DATE_TIME_GENERATE")." (".FORMAT_DATE."):"?></td>
        <td><?echo CalendarPeriod("filter_date1_period", $filter_date1_period, "filter_date2_period", $filter_date2_period, "find_form")?></td>
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
