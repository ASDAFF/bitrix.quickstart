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
$WD_Reviews2_FieldID = IntVal($_GET['field']);
$GetList = false;

/* Popup-edit */
if ($_GET['action']=='edit') {
	require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/fields_edit.php');
}

/* Get settings */
if ($_GET['action']=='type') {
	$arSavedValues = array();
	if ($WD_Reviews2_FieldID>0) {
		$resField = CWD_Reviews2_Fields::GetList(false,array('ID'=>$WD_Reviews2_FieldID,'INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
		if ($arField = $resField->GetNext()) {
			$arSavedValues = unserialize($arField['~PARAMS']);
		}
	}
	if (!is_array($arSavedValues)) {
		$arSavedValues = array();
	}
	$Type = htmlspecialchars($_GET['type']);
	print CWD_Reviews2::ShowSettings($Type, $arSavedValues);
}

/* Saving */
if ($_GET['action']=='save') {
	$FieldMode = 'add';
	if ($WD_Reviews2_FieldID>0) {
		$resField = CWD_Reviews2_Fields::GetList(false,array('ID'=>$WD_Reviews2_FieldID,'INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
		if ($arField = $resField->GetNext(false,false)) {
			$FieldMode = 'edit';
		}
	}
	$arFields = array(
		'INTERFACE_ID' => $WD_Reviews2_InterfaceID,
		'NAME' => $_POST['fields']['NAME'],
		'CODE' => ToUpper($_POST['fields']['CODE']),
		'SORT' => $_POST['fields']['SORT'],
		'DESCRIPTION' => $_POST['fields']['DESCRIPTION'],
		'REQUIRED' => $_POST['fields']['REQUIRED']=='Y' ? 'Y' : 'N',
		'HIDDEN' => $_POST['fields']['HIDDEN']=='Y' ? 'Y' : 'N',
		'TYPE' => $_POST['fields']['TYPE'],
		'PARAMS' => serialize($_POST['data']),
	);
	switch($FieldMode) {
		case 'add':
			$bSuccess = CWD_Reviews2_Fields::Add($arFields);
			break;
		case 'edit':
			$bSuccess = CWD_Reviews2_Fields::Update($WD_Reviews2_FieldID, $arFields);
			break;
	}
	if ($bSuccess) {
		CWD_Reviews2_Interface::CreateEventType($WD_Reviews2_InterfaceID);
		print '<div style="display:none">#WD_REVIEWS2_FIELD_SAVE_SUCCESS#</div>';
	} else {
		print '<div style="display:none">#WD_REVIEWS2_FIELD_SAVE_ERROR#</div>';
	}
	$GetList = true;
}

/* Deleting */
if ($_GET['action']=='delete') {
	if ($WD_Reviews2_FieldID>0) {
		$resField = CWD_Reviews2_Fields::GetList(false,array('ID'=>$WD_Reviews2_FieldID,'INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
		if ($arField = $resField->GetNext(false,false)) {
			CWD_Reviews2_Fields::Delete($WD_Reviews2_FieldID);
		}
	}
	$GetList = true;
}

/* Get fields */
if ($_GET['action']=='list' || $GetList) {
	require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/fields_list.php');
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>