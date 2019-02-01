<?
$ModuleID = 'webdebug.reviews';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);
$WD_Reviews2_InterfaceID = IntVal($_GET['interface']);
$WD_Reviews2_RatingID = IntVal($_GET['rating']);
$GetList = false;

/* Popup-edit */
if ($_GET['action']=='edit') {
	require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/ratings_edit.php');
}

/* Saving */
if ($_GET['action']=='save') {
	$RatingMode = 'add';
	if ($WD_Reviews2_RatingID>0) {
		$resRating = CWD_Reviews2_Ratings::GetList(false,array('ID'=>$WD_Reviews2_RatingID,'INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
		if ($arRating = $resRating->GetNext(false,false)) {
			$RatingMode = 'edit';
		}
	}
	$arFields = array(
		'INTERFACE_ID' => $WD_Reviews2_InterfaceID,
		'NAME' => $_POST['fields']['NAME'],
		'SORT' => $_POST['fields']['SORT'],
		'DESCRIPTION' => $_POST['fields']['DESCRIPTION'],
		'PARTICIPATES' => $_POST['fields']['PARTICIPATES'],
	);
	switch($RatingMode) {
		case 'add':
			$bSuccess = CWD_Reviews2_Ratings::Add($arFields);
			break;
		case 'edit':
			$bSuccess = CWD_Reviews2_Ratings::Update($WD_Reviews2_RatingID, $arFields);
			break;
	}
	if ($bSuccess) {
		print '<div style="display:none">#WD_REVIEWS2_RATING_SAVE_SUCCESS#</div>';
	} else {
		print '<div style="display:none">#WD_REVIEWS2_RATING_SAVE_ERROR#</div>';
	}
	$GetList = true;
}

/* Deleting */
if ($_GET['action']=='delete') {
	if ($WD_Reviews2_RatingID>0) {
		$resRating = CWD_Reviews2_Ratings::GetList(false,array('ID'=>$WD_Reviews2_RatingID,'INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
		if ($arRating = $resRating->GetNext(false,false)) {
			CWD_Reviews2_Ratings::Delete($WD_Reviews2_RatingID);
		}
	}
	$GetList = true;
}

/* Get ratings */
if ($_GET['action']=='list' || $GetList) {
	require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/ratings_list.php');
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>