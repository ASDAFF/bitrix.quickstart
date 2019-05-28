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
setLocale(LC_ALL, 'ru_RU.CP1251');
if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_php');

IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_seo2_REDIRECT";

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
   LocalRedirect("/bitrix/admin/asdaff_redirects_list.php?lang=" . LANG . "&filter_site_id=" . UrlEncode($site_id) . "&" . GetFilterParams("filter_", false));
   exit;
 }
//import csv from array
$res = get2DArrayFromCsv($userfile, ";");
foreach ($res as $r){
$OLD_LINK = $r[0];
$NEW_LINK = $r[1];
	if (LANG_CHARSET == "windows-1251"){
	$COMMENT = $r[2];
	} else {
	$COMMENT = iconv('CP1251', 'UTF-8', $r[2]);
	}
$STATUS = 301;
$actv = 'Y';
$WI = 'N';
$UR = 'N';
	$Res__ = seo2RedirectsRulesDB::Add(array(
								'OLD_LINK' => trim($OLD_LINK),
								'NEW_LINK' => trim($NEW_LINK),
								'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
								'STATUS' => $STATUS,
								'ACTIVE' => $actv,
								'COMMENT' => $COMMENT,
								'SITE_ID' => htmlspecialchars($site_id),
                                'WITH_INCLUDES' => htmlspecialchars($WI),
                                'USE_REGEXP' => htmlspecialchars($UR),
							));

}
	LocalRedirect("/bitrix/admin/asdaff_redirects_list.php?lang=" . LANG . "&filter_site_id=" . UrlEncode($site_id) . "&" . GetFilterParams("filter_", false));

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
			"TEXT" => htmlspecialchars("[" . $arRes["LID"] . "] " . $arRes["NAME"]),
			
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
	