<?
$sModuleId = "step2use.redirects";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");
 
global $DBType;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId); 

// @todo HELP FILE
//define("HELP_FILE", "settings/s2u_redirect_list.php");
setLocale(LC_ALL, 'ru_RU.CP1251');
/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

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

// get array from csv
$site_id = $_POST['site_id'];
function get2DArrayFromCsv($file,$delimiter) {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                for ($j=0; $j<count($lineArray); $j++) {
                    if ($i>=2){
                    $data2DArray[$i][$j] = $lineArray[$j];
                	}
				}
                $i++;
            }
            fclose($handle);
        }
        return $data2DArray;
    } 
//check demo
if(CModule::IncludeModuleEx($sModuleId)==MODULE_DEMO or CModule::IncludeModuleEx($sModuleId)==MODULE_DEMO_EXPIRED) {    
    $message = new CAdminMessage(array(
        'MESSAGE' => GetMessage('ERROR_DEMO_CSV'),
    	'TYPE' => 'ERROR',
    	'DETAILS' => '',
    	'HTML' => true
    ));
	} else {
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['csv'] == 1)){
	    
    

copy($_FILES['userfile']['tmp_name'],"".basename($_FILES['userfile']['name']));
$userfile = $_FILES['userfile']['name'];
$aFile = explode('.', $userfile); //check csv
$sExtension = end($aFile);
if($sExtension != "csv") {
   LocalRedirect("/bitrix/admin/step2use_redirects_list.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
   exit;
 }
//import csv from array
$res = get2DArrayFromCsv($userfile, ";");
foreach ($res as $r){

	if (LANG_CHARSET == "windows-1251"){
		$COMMENT = $r[2];
		$OLD_LINK = $r[0];
		$NEW_LINK = $r[1];
		$STATUS = $r[4];
	} else {
		$COMMENT = iconv('CP1251', 'UTF-8', $r[2]);
		$OLD_LINK = iconv('CP1251', 'UTF-8', $r[0]);
		$NEW_LINK = iconv('CP1251', 'UTF-8', $r[1]);
		$STATUS = iconv('CP1251', 'UTF-8', $r[4]);
	}
	if(!$STATUS){
		$STATUS = 301;
	}


$actv = 'Y';
if($r[3]=="Y"): $WI = $r[3]; else: $WI = 'N'; endif;
$UR = 'N';
	$Res__ = S2uRedirectsRulesDB::Add(array(
			'OLD_LINK' => trim($OLD_LINK),
			'NEW_LINK' => trim($NEW_LINK),
			'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
			'STATUS' => $STATUS,
			'ACTIVE' => $actv,
			'COMMENT' => $COMMENT,
			'SITE_ID' => htmlspecialcharsbx($site_id),
            'WITH_INCLUDES' => htmlspecialcharsbx($WI),
            'USE_REGEXP' => htmlspecialcharsbx($UR),
	));

}
	//LocalRedirect("/bitrix/admin/step2use_redirects_list.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
	$message = new CAdminMessage(array(
		'MESSAGE' => GetMessage('SUCCESS_IMPORTED_CSV'),
		"TYPE" => "OK",
		'DETAILS' => '',
		'HTML' => true
	));
}

}

?> 
<?
$lAdmin = new CAdminList($sTableID, $oSort);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();
$APPLICATION->SetTitle(GetMessage("MURL_TITLE"));
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
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
echo S2uRedirects::getLicenseRenewalBanner();
if($message) echo $message->Show();
?>
<h4> <?=GetMessage('IMPORT_CSV')?></h4>
<script type="text/javascript">
	function browse()
    {
        var button = document.getElementById("browse1");
                
        return button.click();

    }
</script>
<FORM ENCTYPE="multipart/form-data" ACTION="" METHOD=POST>
<INPUT TYPE="hidden" name="csv" value="1">
<SELECT NAME="site_id">
<?
$dbRes = CLang::GetList(($b = "sort"), ($o = "asc"));
while (($arRes = $dbRes->Fetch())) {
	$arDDMenu[] = array(
			"TEXT" => htmlspecialcharsbx("[" . $arRes["LID"] . "] " . $arRes["NAME"]),
			
	);
echo "<OPTION VALUE=".$arRes['LID'].">".$arRes['NAME']." - ".$arRes['LID'];
}
?>
</SELECT>
<a href="#" onclick="browse(); return false;" id="new"><?=GetMessage('FILE')?></a> <INPUT NAME="userfile" TYPE="file" id="browse1" style="width: 0px!important; height:0px!important">
<INPUT TYPE="submit" VALUE="<?=GetMessage('IMPORT')?>">
</FORM> <br>
	<?
    
// DISPLAY LIST
	$lAdmin->DisplayList();

	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	?>
	
