<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$module_id = 'unisender.integration';
$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');

if (!empty($API_KEY) && CModule::IncludeModule($module_id)) {
    AddEventHandler('form', 'onBeforeResultAdd', 'uni_subscribe_onBeforeResultAdd');
    if (!empty($_REQUEST['WEB_FORM_ID']) && $_REQUEST['WEB_FORM_ID'] === $arParams['WEB_FORM_ID']) {
        $_SESSION['UNISENDER']['LIST_ID'] = $arParams['LIST_ID'];
    }
    $this->IncludeComponentTemplate();
} else {
    echo ShowError(GetMessage('UNISENDER_MODULE_NOT_AVAILABLE'));
}

function uni_subscribe_onBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
{
    global $APPLICATION;

    $params = array();
    if (empty($_SESSION['UNISENDER']['LIST_ID'])) {
        return $APPLICATION->ThrowException(GetMessage('UNISENDER_ERROR_NO_LIST'));
    }
    $params['list_ids'] = $_SESSION['UNISENDER']['LIST_ID'];
    unset($_SESSION['UNISENDER']['LIST_ID']);

    foreach ($arrVALUES as $name => $value) {
        $names = explode('_', $name);
        if ($names[0] !== 'form') {
            continue;
        }
        $type = $names[1];
        $id = $names[2];
        switch ($type) {
            case 'text':
            case 'email':
            case 'www':
            case 'textarea':
            case 'password':
            case 'date':
            case 'hidden':
            case 'file':
            case 'image':
                $rsAnswer = CFormAnswer::GetByID($id);
                $arAnswer = $rsAnswer->Fetch();
                $rsField = CFormField::GetByID($arAnswer['FIELD_ID']);
                $arField = $rsField->Fetch();
                $params['fields[' . $arField['VARNAME'] . ']'] = $value;
                break;
            case 'radio':
            case 'dropdown':
            case 'checkbox':
            case 'multiselect':
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $rsAnswer = CFormAnswer::GetByID($v);
                        $arAnswer = $rsAnswer->Fetch();
                        $params['fields[' . $id . ']'][] = $arAnswer['MESSAGE'];
                    }
                    $params['fields[' . $id . ']'] = implode(',', $params['fields[' . $id . ']']);
                } else {
                    $rsAnswer = CFormAnswer::GetByID($value);
                    $arAnswer = $rsAnswer->Fetch();
                    $params['fields[' . $id . ']'] = $arAnswer['MESSAGE'];
                }
                break;
        }

    }

    require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/unisender.integration/classes/general/unisenderAPI.php';
    $API_KEY = COption::GetOptionString('unisender.integration', 'UNISENDER_API_KEY');
    $API = new UniAPI($API_KEY);

    $isSubscribe = $API->subscribe($params);
    if ($isSubscribe === false) {
        $error = $API->getError();
        $APPLICATION->ThrowException('(' . $error[1] . ') ' . $error[0]);
    }
}

?>
