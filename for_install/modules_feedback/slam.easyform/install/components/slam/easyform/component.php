<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array $arParams
 * @var array $arResult
 * @var string $componentPath
 * @var string $componentName
 * @var string $componentTemplate
 *
 * @var CDatabase $DB
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server = $context->getServer();

$arParams['OLD_PARAMS'] = array_merge($arParams);
foreach ($arParams['OLD_PARAMS'] as $key => $val){
    if($key[0] != '~'){
        if(is_array($val)){
            $val[] = false;
            $arParams['OLD_PARAMS'][$key] = implode('-array-', $val);
        }
    } else{
        unset($arParams['OLD_PARAMS'][$key]);
    }
}



$arParams['FORM_ID'] = $arParams['FORM_ID'] ? trim($arParams['FORM_ID']) : $this->GetEditAreaId($this->__currentCounter);

$arParams['HTTP_PROTOCOL'] = $request->isHttps() ? 'https://' : 'http://';
$arParams['HTTP_HOST'] = $arParams['HTTP_PROTOCOL'] . $server->getHttpHost();
$arParams['EVENT_NAME'] = 'SLAM_EASYFORM';
$arParams['USE_FORM_MASK_JS'] = 'N';



if($request['AJAX']){
    $arParams['SEND_AJAX'] = 'Y';
}




TrimArr($arParams['DISPLAY_FIELDS']);
TrimArr($arParams['REQUIRED_FIELDS']);

$arParams['DISPLAY_FIELDS'] = array_diff((array)($arParams['DISPLAY_FIELDS']), array(''));
$arParams['REQUIRED_FIELDS'] = array_diff((array)($arParams['REQUIRED_FIELDS']), array(''));

$arParams['DISPLAY_FIELDS_ARRAY'] = $arParams['DISPLAY_FIELDS'];


$arSortField = explode(',', $arParams['FIELDS_ORDER']);
if (!empty($arSortField) && count($arSortField) == count($arParams['DISPLAY_FIELDS'])) {
    $arParams['DISPLAY_FIELDS_ARRAY'] = $arSortField;
}

$arParams['TEMPLATE_FOLDER'] = $this->GetPath().'/ajax.php';
$arParams['TEMPLATE_NAME'] = $this->GetTemplateName();

$arResult['FORM_FIELDS'] = array();
foreach ($arParams['DISPLAY_FIELDS_ARRAY'] as $fieldCode) {
    $isReq = in_array($fieldCode, $arParams['REQUIRED_FIELDS']) ? true : false;
    $placeHolder = !empty($arParams['CATEGORY_' . $fieldCode . '_PLACEHOLDER']) ? $arParams['CATEGORY_' . $fieldCode . '_PLACEHOLDER'] : false;
    $typeInput = !empty($arParams['CATEGORY_' . $fieldCode . '_TYPE']) ? $arParams['CATEGORY_' . $fieldCode . '_TYPE'] : 'text';
    $isMultiple = $typeInput == 'checkbox' ? true : false;

    $arResult['FORM_FIELDS'][$fieldCode] = array(
        'ID' => trim($arParams['FORM_ID']) . '_FIELD_' . $fieldCode,
        'TITLE' => !empty($arParams['CATEGORY_' . $fieldCode . '_TITLE']) ? htmlspecialcharsBack($arParams['CATEGORY_' . $fieldCode . '_TITLE']) : $fieldCode,
        'TYPE' => $typeInput,
        'NAME' => 'FIELDS['.$fieldCode.']',
        'CODE' => $fieldCode,
        'REQUIRED' => $isReq,
        'REQ_STR' => $isReq ? ($arParams['USE_FORMVALIDATION_JS'] == 'Y'  ?  (' required data-bv-message="'.$arParams['CATEGORY_'.$fieldCode.'_VALIDATION_MESSAGE'].'"').htmlspecialcharsBack($arParams['CATEGORY_'.$fieldCode.'_VALIDATION_ADDITIONALLY_MESSAGE']) : ' required') : ' '.htmlspecialcharsBack($arParams['CATEGORY_'.$fieldCode.'_VALIDATION_ADDITIONALLY_MESSAGE']),
        'VALUE' => !empty($arParams['CATEGORY_' . $fieldCode . '_VALUE']) ? $arParams['CATEGORY_' . $fieldCode . '_VALUE'] : '',
        'PLACEHOLDER' => $placeHolder,
        'PLACEHOLDER_STR' => $placeHolder ? 'placeholder="'.$arParams['CATEGORY_' . $fieldCode . '_PLACEHOLDER'].'"':'',
    );

    if($arParams['CATEGORY_'.$fieldCode.'_INPUTMASK_TEMP']){
        $arResult['FORM_FIELDS'][$fieldCode]['MASK_STR'] = 'data-inputmask-mask="'.$arParams['CATEGORY_'.$fieldCode.'_INPUTMASK_TEMP'].'" data-mask="'.$arParams['CATEGORY_'.$fieldCode.'_INPUTMASK_TEMP'].'"';
    }

    if($typeInput == 'file'){
        $arResult['FORM_FIELDS'][$fieldCode]['DROPZONE_INCLUDE'] = $arParams["CATEGORY_".$fieldCode."_DROPZONE_INCLUDE"] == 'Y';
        $arResult['FORM_FIELDS'][$fieldCode]['FILE_MAX_SIZE'] = $arParams["CATEGORY_".$fieldCode."_FILE_MAX_SIZE"];
        $arResult['FORM_FIELDS'][$fieldCode]['FILE_EXTENSION'] = $arParams["CATEGORY_".$fieldCode."_FILE_EXTENSION"];
    }

    if($typeInput == 'radio' || $typeInput == 'checkbox'){
        $arResult['FORM_FIELDS'][$fieldCode]['SHOW_INLINE'] = $arParams["CATEGORY_".$fieldCode."_SHOW_INLINE"] == "Y";
    }

    if($typeInput == 'select'){
        $isMultiSelect = $arParams['CATEGORY_' . $fieldCode . '_MULTISELECT'] == 'Y';
        $arResult['FORM_FIELDS'][$fieldCode]['MULTISELECT'] = $isMultiSelect ? 'Y' : 'N';
        if($isMultiSelect){
            $isMultiple = true;
        }else{
            if (strlen(trim($arParams['CATEGORY_' . $fieldCode . "_ADD_VAL"])) > 0) {
                $arResult['FORM_FIELDS'][$fieldCode]['SET_ADDITION_SELECT_VAL'] = true;
                $arResult['FORM_FIELDS'][$fieldCode]['SET_ADDITION_SELECT_ID'] = $arResult['FORM_FIELDS'][$fieldCode]['ID'] . '_add';
                $arResult['FORM_FIELDS'][$fieldCode]['ADDITION_SELECT_VAL'] = $arParams['CATEGORY_' . $fieldCode . "_ADD_VAL"];
                $arResult['FORM_FIELDS'][$fieldCode]['ADDITION_SELECT_NAME'] = 'FIELDS[' . $fieldCode . '_ADD]';
            }
        }
    }

    if($isMultiple){
        $arResult['FORM_FIELDS'][$fieldCode]['NAME'] .= '[]';
    }

    if ($arParams['USE_IBLOCK_WRITE'] == 'Y') {
        $arParams['WRITE_FIELDS'][$fieldCode] = !empty($arParams['CATEGORY_' . $fieldCode . '_IBLOCK_FIELD']) ? $arParams['CATEGORY_' . $fieldCode . '_IBLOCK_FIELD'] : '';
    }

    if (strlen(trim($arParams['CATEGORY_' . $fieldCode . "_INPUTMASK_TEMP"])) > 0) {
        $arParams['USE_FORM_MASK_JS'] = 'Y';
    }
}

$captchaKey = "";
$captchaSecretKey = "";
if (Loader::IncludeModule("slam.easyform")) {
    $captchaKey = Option::get("slam.easyform", "CAPTCHA_KEY", "", SITE_ID);
    $captchaSecretKey = Option::get("slam.easyform", "CAPTCHA_SECRET_KEY", "", SITE_ID);
}

if(strlen($captchaKey) > 0 && strlen($captchaSecretKey) > 0){
    $arParams["CAPTCHA_KEY"] = trim($captchaKey);
    $arResult["CAPTCHA_SECRET_KEY"] = trim($captchaSecretKey);
}else{
    $arParams["CAPTCHA_KEY"] = trim($arParams["CAPTCHA_KEY"]);
    $arResult["CAPTCHA_SECRET_KEY"] = $arParams["CAPTCHA_SECRET_KEY"];
}

$arParams["USE_CAPTCHA"] = $arParams["USE_CAPTCHA"] == 'Y' && strlen($arParams["CAPTCHA_KEY"]) > 1 && strlen($arResult["CAPTCHA_SECRET_KEY"]) > 1;



$arParams['USER_EMAIL'] = '';
$arParams['EMAIL_TO'] = trim($arParams['EMAIL_TO']) ? trim($arParams['EMAIL_TO']) : Option::get("slam.easyform", "EMAIL", "", SITE_ID);
$arParams['BCC'] = trim($arParams['BCC']);

$arParams['OK_TEXT'] = $arParams['OK_TEXT'] ? htmlspecialcharsback(trim($arParams['OK_TEXT'])) : Loc::getMessage('SLAM_EASYFORM_MESS_OK_TEXT');
$arParams['ERROR_TEXT'] = $arParams['ERROR_TEXT'] ? htmlspecialcharsback(trim($arParams['ERROR_TEXT'])) : Loc::getMessage('SLAM_EASYFORM_MESS_ERROR_TEXT');



$arParams['MAIL_SUBJECT_ADMIN'] = trim($arParams['MAIL_SUBJECT_ADMIN']);
$arParams['MAIL_SUBJECT_SENDER'] = trim($arParams['MAIL_SUBJECT_SENDER']);
$arParams['MAIL_SEND_USER'] = $arParams['MAIL_SEND_USER'] == 'Y';


$arParams['ENABLE_SEND_MAIL'] = $arParams['ENABLE_SEND_MAIL'] === 'Y';
$arParams['EMAIL_FILE'] = $arParams['EMAIL_FILE'] === 'Y';



$arParams['USE_JQUERY'] = $arParams['USE_JQUERY'] === 'Y';


$arParams['HIDE_FIELD_NAME'] = $arParams['HIDE_FIELD_NAME'] === 'Y';
$arParams['HIDE_ASTERISK'] = $arParams['HIDE_ASTERISK'] === 'Y';
$arParams['FORM_AUTOCOMPLETE'] = $arParams['FORM_AUTOCOMPLETE'] === 'Y';
$arParams['FORM_SUBMIT_VALUE'] = htmlspecialcharsback($arParams['FORM_SUBMIT_VALUE']);
$arParams['SEND_AJAX'] = $arParams['SEND_AJAX'] === 'Y';
$arParams['FORM_SUBMIT'] = false;

if ($arParams['USE_MODULE_VARNING'] != 'N' && Loader::includeModule("slam.easyform")) {
    if (Option::get("slam.easyform", "SHOW_MESSAGE", "", SITE_ID) == 'Y') {
        $mess = Option::get("slam.easyform", "MESSAGE_TEXT", "", SITE_ID);
        if (strlen($mess) > 0) {
            $arResult['WARNING_MSG'] = str_replace('#BUTTON#', $arParams['FORM_SUBMIT_VALUE'], $mess);
        }
    }
} elseif (strlen($arParams['FORM_SUBMIT_VARNING']) > 0) {
    $arResult['WARNING_MSG'] = str_replace('#BUTTON#', $arParams['FORM_SUBMIT_VALUE'], $arParams['~FORM_SUBMIT_VARNING']);
}


$arMessageError = array();
$arFields = array();

$isAjax = $request['AJAX'];

if ($request->isPost() && $arParams['FORM_ID'] == $request['FORM_ID']) {

	$arParams['FORM_SUBMIT'] = true;

    if (isset($_REQUEST['ANTIBOT']) && is_array($_REQUEST['ANTIBOT'])) {
        foreach ($_REQUEST['ANTIBOT'] as $k => $v)
            if (empty($v))
                unset($_REQUEST['ANTIBOT'][$k]);
    }

    if ($_REQUEST['ANTIBOT'] || !isset($_REQUEST['ANTIBOT'])) {
        $APPLICATION->RestartBuffer();
        die();
    }

    if ($arParams["USE_CAPTCHA"]) {
        require_once(dirname(__FILE__) . '/lib/recaptcha/autoload.php');
        if (isset($_REQUEST['g-recaptcha-response'])) {
            $recaptcha = new \ReCaptcha\ReCaptcha($arResult["CAPTCHA_SECRET_KEY"]);
            $resp = $recaptcha->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if ($resp->isSuccess()) {

            } else {
                $errors = $resp->getErrorCodes();
                $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_CAPTCHA_ERROR');
            }
        }
    }

    $arPostFieldsTmp = (array)$request['FIELDS'];

    foreach ($arResult['FORM_FIELDS'] as $code => $val) {
        if ($val['TYPE'] == 'file' && !empty($request[$code])) {

            $arPostFieldsTmp[$code] = (array)$request[$code];

        } elseif ($val['TYPE'] == 'file') {

            $files =  $request->getFile("FIELDS");

            if($files['name'][$code]) {
                $file = array();
                $file['name'] = $files['name'][$code];
                $file['type'] = $files['type'][$code];
                $file['tmp_name'] = $files['tmp_name'][$code];
                $file['error'] = $files['error'][$code];
                $file['size'] = $files['size'][$code];
                $arPostFieldsTmp[$code] = $file;
            }
        }
    }

    $arPostFields = array();
    $emptyForm = true;
    $idFilesAll = array();
    foreach ($arPostFieldsTmp as $key => $field) {
        if(!array_key_exists($key, $arResult['FORM_FIELDS'])){
            continue;
        }

        $fieldInfo = $arResult['FORM_FIELDS'][$key];

        if (is_array($field)) {
            $arPostFields[$key] = array();
            if ($fieldInfo['TYPE'] == 'file') {
                $fieldFiles = array();
                if (!empty($field['tmp_name'])) {

                    $res = CFile::CheckFile($field, $fieldInfo["FILE_MAX_SIZE"], false, $fieldInfo["FILE_EXTENSION"]);
                    if (strlen($res) > 0) {
                        $arMessageError[] = $res;
                        continue;
                    }
                    $f_id = \CFile::SaveFile($field, 'slam.easyform');
                    if ($f_id)
                        $fieldFiles[] = $f_id;
                } else {
                    $fieldFiles = $field;
                }

           
                
                foreach ($fieldFiles as $strVal) {
                    if($strVal){
                        $idFilesAll[] = $strVal;
                        $arPostFields[$key][] = $arParams['HTTP_HOST'] . CFile::GetPath($strVal);
                    }
                }
            } else {
                foreach ($field as $strVal){
                    if($strVal){
                        $arPostFields[$key][] = nl2br(htmlspecialcharsbx($strVal));
                    }
                }
            }
            $emptyForm = false;
        } else {
            if ($fieldInfo['SET_ADDITION_SELECT_VAL'] && $arPostFieldsTmp[$key . "_ADD"]) {
                if (strlen(trim($arPostFieldsTmp[$key . "_ADD"])) > 0) {
                    $arPostFields[$key] = nl2br(htmlspecialcharsbx($arPostFieldsTmp[$key . "_ADD"]));
                }
            } else {
                $arPostFields[$key] = nl2br(htmlspecialcharsbx($field));

            }
            if(strlen(trim($field)) > 0){
                $emptyForm = false;
            }
        }
    }

    if ((!empty($arPostFields['EMAIL']) || in_array('EMAIL', $arParams['REQUIRED_FIELDS'])) && !check_email($arPostFields['EMAIL']))
        $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_EMAIL_ERROR');
	
    if(!$emptyForm){
        foreach($arPostFields as &$element){
            if(is_array($element) && !empty($element)){
                $element = implode("\n", $element);
            }else{
                $element = !empty($element) ? $element : '-';
            }
        }
    }

    if ($emptyForm) {
        $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_EMPTY_FORM_ERROR');
    }
	
    $_REQUEST['FIELDS']['TITLE'] = 0;
    if (!in_array('NONE', $arParams['REQUIRED_FIELDS'])) {
        foreach ($arParams['REQUIRED_FIELDS'] as $FIELD) {
            $message_field = $arResult['FORM_FIELDS'][$FIELD]['TITLE'];

            if ((empty($arParams['REQUIRED_FIELDS']) || in_array($FIELD, $arParams['REQUIRED_FIELDS'])) && strlen($arPostFields[$FIELD]) == 0) {
                $arMessageError[] = Loc::getMessage("SLAM_EASYFORM_FIELD_ERROR_MESS", array("#FIELD_NAME#" => $message_field));
            }
        }
        unset($FIELD);
    }


    if ($isAjax && !Main\Application::isUtfMode()) {
        $arPostFields = Main\Text\Encoding::convertEncoding($arPostFields, 'UTF-8', $context->getCulture()->getCharset());
    }

    if (empty($arMessageError) && $arParams['ENABLE_SEND_MAIL']) {
        $arServiceFields = Array(
            'FORM_FIELDS' => '',
            'AUTHOR_NAME' => '',
            'AUTHOR_EMAIL' => '',
            'SUBJECT' => '',
            'EMAIL_FROM' => '',
            'EMAIL_TO' => $arParams['EMAIL_TO'],
            'EMAIL_BCC' => $arParams['EMAIL_BCC'],
            'DEFAULT_EMAIL_FROM' => Option::get("main", "email_from", ""),
            'FORM_TITLE' => $arParams['FORM_TITLE'],
            'SITE_NAME' =>  Option::get("main", "site_name",  $GLOBALS['SERVER_NAME']),
            'SERVER_NAME' => $request->getHttpHost(),
            'IP' => $request->getRemoteAddress(),

        );


        if ($isAjax && !Main\Application::isUtfMode()) {
            $arServiceFields = Main\Text\Encoding::convertEncoding($arServiceFields, 'UTF-8', $context->getCulture()->getCharset());
        }


     
            $arServiceFields['EMAIL_FROM'] = $arServiceFields['DEFAULT_EMAIL_FROM'];
        

        if ($arPostFields) {

           
            $nameStyle = 'font-weight:bold;';
            $defaultStyle = "padding:10px;border-bottom:1px dashed #dadada;";
            foreach ($arPostFields as $code => $val) {
                
                
                foreach (GetModuleEvents("slam.easyform", "OnBeforeFieldsValue", true) as $arEvent)
                    ExecuteModuleEventEx($arEvent, array($code, &$val));
                
                $name = $arResult['FORM_FIELDS'][$code]['TITLE'] ? $arResult['FORM_FIELDS'][$code]['TITLE'] : $code;
                $curVal = is_array($val) ? implode('<br>', $val) : $val;

                $strFieldsNames .= "\n<div style=\"" . $defaultStyle . "\">";
                $strFieldsNames .= "\n\t<div style=\"" . $nameStyle . "\">" . $name . "</div>";
                $strFieldsNames .= "\n\t<div>" . $curVal . "</div>";
                $strFieldsNames .= "\n</div>";
            }
            

            $arServiceFields['FORM_FIELDS'] = $strFieldsNames;
        }


        foreach ($arPostFields as $code => $val) {
			$curVal = is_array($val) ? implode(', ', $val) : $val;
			$arServiceFields[$code] = $curVal;
            if ($code == 'EMAIL') {
                $arServiceFields['AUTHOR_EMAIL'] = $val;
            }
        }

        $arServiceFields['SUBJECT'] = str_replace('#SITE_NAME#', $arServiceFields['SERVER_NAME'], $arParams['MAIL_SUBJECT_ADMIN']);
       
  
    }

	$file_id = array();
	$idElement = false;
    if (empty($arMessageError) && $arParams['USE_IBLOCK_WRITE'] == 'Y' && intval($arParams['IBLOCK_ID']) > 0 && Loader::IncludeModule("iblock")) {

        $arLoadFields = array(
            'NAME',
            'DETAIL_TEXT',
            'PREVIEW_TEXT'
        );

        $PROP = array();
        foreach ($arPostFields as $key => $val) {
            $fieldCode = $arParams['WRITE_FIELDS'][$key];

            if($arResult['FORM_FIELDS'][$key]['TYPE'] != 'file' && strpos($val, ";\n") !== false){
                $val = explode(";\n", $val);
            }


            if ($val && $fieldCode) {
                if (in_array($fieldCode, $arLoadFields)) {
                    $arFields[$fieldCode] = $val;					
                } else {
                    if ($fieldCode != 'NO_WRITE') {
                        $PROP[$fieldCode] = $val;
					    if($arFormField = $arResult['FORM_FIELDS'][$key]['TYPE'] == 'file' && $val){
                            $arFiles = array();
                            $files = explode(";\n",  $val );
                            foreach($files as $file){
                                $arFiles[] = CFile::MakeFileArray(str_replace($arParams['HTTP_HOST'], $_SERVER['DOCUMENT_ROOT'], $file));
                            }
                            $PROP[$fieldCode] = $arFiles;
                        }
                    }
                }
            }
        }

        if (!empty($PROP)) {
            $properties = CIBlockProperty::GetList(Array(), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams['IBLOCK_ID']));
            while ($prop_fields = $properties->GetNext()) {
                $arPropsCode[] = $prop_fields['CODE'];
            }

            foreach ($PROP as $code => $propValue) {
                if (strpos($code, 'FORM_') !== false && !in_array($code, $arPropsCode)) {
                    $arFormField = $arResult['FORM_FIELDS'][str_replace("FORM_", "", $code)];

                    if ($arFormField) {
                        $arLoadPropFields = Array(
                            "NAME" => $arFormField['TITLE'],
                            "ACTIVE" => "Y",
                            "SORT" => "700",
                            "CODE" => $code,
							"MULTIPLE" => in_array($arFormField['TYPE'],  array('file', 'checkbox', 'select')) ? "Y" : "N",
                            "PROPERTY_TYPE" => $arFormField['TYPE'] == 'file' ? "F" : "S",
                            "IBLOCK_ID" => $arParams['IBLOCK_ID']
                        );
                        $ibp = new CIBlockProperty;
                        $PropID = $ibp->Add($arLoadPropFields);
                    }
                }
            }
        }

        $arLoadProductArray = Array(
            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $arFields['NAME'] ? $arFields['NAME'] : date("d.m.Y H:i:s"),
            "ACTIVE" => $arParams['ACTIVE_ELEMENT'] == 'Y' ? "N" : "Y",
            "DETAIL_TEXT" => $arFields['DETAIL_TEXT'] ? $arFields['DETAIL_TEXT'] : strip_tags($strFieldsNames),
            "DETAIL_TEXT_TYPE" => 'text',
            "PREVIEW_TEXT" => $arFields['PREVIEW_TEXT'],
            "PREVIEW_TEXT_TYPE" => 'text',
            "DATE_ACTIVE_FROM" => date("d.m.Y H:i:s"),
        );

        foreach (GetModuleEvents("slam.easyform", "OnBeforeIBlockElementAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arLoadProductArray, &$arParams, &$arPostFields, &$arServiceFields, &$file_id));


        $el = new CIBlockElement;
        if ($idElement = $el->Add($arLoadProductArray)) {

            foreach (GetModuleEvents("slam.easyform", "OnAfterIBlockElementAdd", true) as $arEvent)
			   ExecuteModuleEventEx($arEvent, array($idElement, &$arParams, &$arPostFields, &$arServiceFields, &$file_id));

        }
    }

	
	if (Loader::IncludeModule("slam.easyform") && empty($arMessageError)) {
        $fields = array();
        foreach ($arResult['FORM_FIELDS'] as $key => $val):
            $fields[!empty($arParams['CATEGORY_' . $key . '_TITLE']) ? $arParams['CATEGORY_' . $key . '_TITLE'] : $key] = $arPostFields[$key];
        endforeach;
        $tblObj = new Slam\Easyform\EasyformTable();
        $resultSave = $tblObj->Add(
            array(
                "NAME" => $arParams['FORM_NAME'] ? $arParams['FORM_NAME'] : 'forms',
                'FIELDS' => $fields
            )
        );
    }

    if($arParams['EMAIL_FILE'] && !empty($idFilesAll)) {
        $file_id = $idFilesAll;
    }

	
	if (empty($arMessageError) && $arParams['ENABLE_SEND_MAIL']) {
		if (is_array($arParams['EVENT_MESSAGE_ID']) && !empty($arParams['EVENT_MESSAGE_ID'])) {
            foreach ($arParams['EVENT_MESSAGE_ID'] as $arMessID) {
                if (!CEvent::Send($arParams['EVENT_NAME'], SITE_ID, $arServiceFields, 'N', $arMessID, $file_id)) {
                    $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_SEND_ERROR_MESS', array("#MESS_ID#" => $arMessID));
                }
            }
        } else {
            if (!CEvent::Send($arParams['EVENT_NAME'], SITE_ID, $arServiceFields, 'N', "",  $file_id)) {
                $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_SEND_ERROR');
            }
        }

        if($arParams['EMAIL_SEND_FROM'] == 'Y') {
            $arServiceFields['EMAIL_TO'] = $arServiceFields['EMAIL'];
            $arServiceFields['EMAIL_BCC'] = $arServiceFields['EMAIL_BCC_SENDER'];
            $arServiceFields['SUBJECT'] = str_replace('#SITE_NAME#', $arServiceFields['SERVER_NAME'], $arParams['MAIL_SUBJECT_SENDER']);
            if (is_array($arParams['EVENT_MESSAGE_ID_SENDER']) && !empty($arParams['EVENT_MESSAGE_ID_SENDER'])) {
                foreach ($arParams['EVENT_MESSAGE_ID_SENDER'] as $arMessID) {
                    if (!CEvent::Send($arParams['EVENT_NAME'], SITE_ID, $arServiceFields, 'N', $arMessID, $file_id)) {
                        $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_SEND_ERROR_MESS', array("#MESS_ID#" => $arMessID));
                    }
                }
            } else {
                if (!CEvent::Send($arParams['EVENT_NAME'], SITE_ID, $arServiceFields, 'N', "", $file_id)) {
                    $arMessageError[] = Loc::getMessage('SLAM_EASYFORM_SEND_ERROR');
                }
            }
        }
        
	}
   
    $arResult['FIELDS'] = $arPostFields;

	$arAdditionalMessage = false;
    foreach (GetModuleEvents("slam.easyform", "OnAfterAdditionalMessage", true) as $arEvent)
		ExecuteModuleEventEx($arEvent, array(&$arAdditionalMessage, &$arParams, $idElement, $arPostFields));


    if (!empty($arMessageError)) {
        $result = array(
            'result' => 'error',
            'message' => implode("</br>", $arMessageError),
			'additiona_msg' => $arAdditionalMessage
        );
    } else {
        $result = array('result' => 'ok', 'additiona_msg' => $arAdditionalMessage); 
    }

    if ($isAjax) {
        ob_end_clean();
        $GLOBALS['APPLICATION']->RestartBuffer();
        echo Json::encode($result);
        die();
    } else {
        $arResult['STATUS'] = empty($arMessageError) ? 'ok' : 'error';
        $arResult['ERROR_MSG'] = $arMessageError;
		$arResult['ADDITIONAL_MSG'] = $arAdditionalMessage;
    }

}



$arResult['ANTIBOT'] = $_REQUEST['ANTIBOT'];

if ($arParams["USE_CAPTCHA"] && !defined("SLAM_RE_CAPTCHA_INCLUDE")) {
    $APPLICATION->AddHeadScript('https://www.google.com/recaptcha/api.js');
    define("SLAM_RE_CAPTCHA_INCLUDE", "Y");
}

if ($arParams["USE_JQUERY"] && !defined("SLAM_JQUERY_INCLUDE")) {
    $APPLICATION->AddHeadScript($componentPath . '/lib/js/jquery-1.12.4.min.js');
    define("SLAM_JQUERY_INCLUDE", "Y");
}

if ($arParams["USE_BOOTSRAP_JS"] == 'Y' && !defined("SLAM_BOOTSTRAP_JS_INCLUDE")) {
    $APPLICATION->AddHeadScript($componentPath . '/lib/js/bootstrap.min.js');
    define("SLAM_BOOTSTRAP_JS_INCLUDE", "Y");
}

if ($arParams["USE_BOOTSRAP_CSS"] == 'Y' && !defined("SLAM_BOOTSTRAP_CSS_INCLUDE")) {
    $APPLICATION->SetAdditionalCSS($componentPath . '/lib/css/bootstrap.min.css');
    define("SLAM_BOOTSTRAP_CSS_INCLUDE", "Y");
}

if ($arParams['USE_FORMVALIDATION_JS'] == "Y" && $arParams['INCLUDE_BOOTSRAP_JS'] != "N" && !defined("SLAM_BOOTSTRAP_VALIDATOR_INCLUDE")) {
    $APPLICATION->SetAdditionalCSS($componentPath . '/lib/css/bootstrapValidator.min.css');
    $APPLICATION->AddHeadScript($componentPath . '/lib/js/bootstrapValidator.min.js');
    define("SLAM_BOOTSTRAP_VALIDATOR_INCLUDE", "Y");
}

if ($arParams["USE_INPUTMASK_JS"] == 'Y' && !defined("SLAM_FORM_INPUTMASK_INCLUDE")) {
    $APPLICATION->AddHeadScript($componentPath . '/lib/js/inputmask.js');
    define("SLAM_FORM_INPUTMASK_INCLUDE", "Y");
}

if (!defined("SLAM_FORM_JS_INCLUDE")) {
    $APPLICATION->AddHeadScript($componentPath . '/script.js');
    define("SLAM_FORM_JS_INCLUDE", "Y");
}


$this->IncludeComponentTemplate();