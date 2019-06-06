<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

global $arComponentParameters;

//GET SITE IDs
$arSiteId = array();
$rsSite = CSite::GetList($by = 'sort', $order = 'asc', Array('ACTIVE' => 'Y'));
while ($arSite = $rsSite->fetch()) {
    $arSiteId[] = $arSite['LID'];
}

//CREATE POST EVENT
$arEventTypeFields = array(
    0 => array(
        'LID' => 'ru',
        'EVENT_NAME' => 'SLAM_EASYFORM',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_RU_NAME'),
        'DESCRIPTION' => Loc::getMessage('SLAM_EASYFORM_RU_DESCRIPTION'),
    ),
    1 => array(
        'LID' => 'en',
        'EVENT_NAME' => 'SLAM_EASYFORM',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_RU_NAME'),
        'DESCRIPTION' => Loc::getMessage('SLAM_EASYFORM_RU_DESCRIPTION'),
    ),
);
$eventType = new CEventType;
foreach ($arEventTypeFields as $arField) {
    $rsET = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
    $arET = $rsET->Fetch();

    if (!$arET) {
        $eventType->Add($arField);
        $installSendTemplates = true;
    }
}
unset($rsET, $arET, $arField);

//CREATE POST TEMPLATE
if (($installSendTemplates) && ($arCurrentValues['ENABLE_SEND_MAIL'] != 'N')) {
    $arEventMessFields = array(
        0 => array(
            'ACTIVE' => 'Y',
            'EVENT_NAME' => 'SLAM_EASYFORM',
            'LID' => $arSiteId,
            'EMAIL_FROM' => Loc::getMessage('SLAM_EASYFORM_EMAIL_FROM'),
            'EMAIL_TO' => Loc::getMessage('SLAM_EASYFORM_EVEN_EMAIL_TO'),
            'BCC' => Loc::getMessage('SLAM_EASYFORM_EVEN_BCC'),
            'SUBJECT' => Loc::getMessage('SLAM_EASYFORM_SUBJECT'),
            'BODY_TYPE' => 'html',
            'MESSAGE' => Loc::getMessage('SLAM_EASYFORM_MESSAGE'),
        )
    );

    $eventM = new CEventMessage;
    foreach ($arEventMessFields as $arField) {
        $eventM->Add($arField);
    }

}

//FIELDS FOR EXAMPLE
$arFields = array(
    'TITLE' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_TITLE'),
        'TYPE' => 'text',
    ),

    'EMAIL' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_EMAIL'),
        'TYPE' => 'email',
    ),
    'PHONE' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_PHONE'),
        'TYPE' => 'tel',
    ),
    'MALE' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_MALE'),
        'TYPE' => 'radio',
        'DEFAULT_VALUE' => array(Loc::getMessage('SLAM_EASYFORM_FIELD_MALE_VAL_1'), Loc::getMessage('SLAM_EASYFORM_FIELD_MALE_VAL_2')),
    ),
    'BUDGET' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_BUDGET'),
        'TYPE' => 'select',
        'DEFAULT_VALUE' => array(Loc::getMessage('SLAM_EASYFORM_FIELD_BUDGET_VAL_1'), Loc::getMessage('SLAM_EASYFORM_FIELD_BUDGET_VAL_2')),
    ),
    'SERVICES' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_SERVICES'),
        'TYPE' => 'checkbox',
        'DEFAULT_VALUE' => array(Loc::getMessage('SLAM_EASYFORM_FIELD_SERVICES_VAL_1'), Loc::getMessage('SLAM_EASYFORM_FIELD_SERVICES_VAL_2')),
    ),
    'MESSAGE' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_MESSAGE'),
        'TYPE' => 'textarea',
    ),
    'DOCS' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_DOCS'),
        'TYPE' => 'file',
    ),
    'ACCEPT' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_ACCEPT'),
        'TYPE' => 'accept',
        'DEFAULT_VALUE' => Loc::getMessage('SLAM_EASYFORM_FIELD_ACCEPT_VAL'),
    ),
    'HIDDEN' => array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_HIDDEN'),
        'TYPE' => 'hidden',
    )
);


$defaultFields = array('TITLE', 'EMAIL', 'PHONE', 'MESSAGE');
$defaultFieldsForReq = array();
$arFieldsResult = array();
$arFieldsResultType = array();
foreach ($arFields as $key => $arField) {
    $arFieldsResult[$key] = $arField['NAME'];
    $arFieldsResultType[$key] = $arField['NAME'].' ['.$arField['TYPE'].']';
    if (in_array($key, $defaultFields)) {
        $defaultFieldsForReq[$key] = $arField['NAME'];
    }
}


if (is_array($arCurrentValues['DISPLAY_FIELDS'])) {
    foreach ($arCurrentValues['DISPLAY_FIELDS'] as $key => $arVal) {
        if (!empty($arVal)) {
            if ($arFieldsResult[$arVal]) {
                $fieldName = $arFieldsResult[$arVal];
            } else {
                $fieldName = $arVal;
            }
            $arReqFields[$arVal] = $fieldName;
        }
    }
}

$curFields = !empty($arReqFields) ? $arReqFields : $defaultFieldsForReq;

$arComponentParameters = array(
    'GROUPS' => array(
        'SUBMIT' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_SUBMIT'),
            'SORT' => 200,
        ),
        'MAIL' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_MAIL'),
            'SORT' => 300,
        ),
        'CAPTCHA' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_CAPTCHA'),
            'SORT' => 1350,
        ),
        'IBLOCK_WRITE' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_WRITE_IB'),
            'SORT' => 450,
        ),
        'PERSONAL_DATA' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_PERSONAL_DATA'),
            'SORT' => 1350,
        ),
        'JS_VALIDATE_SETTINGS' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUPS_JS_VALIDATE_SETTINGS'),
            'SORT' => 1370,
        ),
        'JS_LIB_SETTINGS' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUPS_JS_LIB_SETTINGS'),
            'SORT' => 1400,
        )

    ),

    'PARAMETERS' => array(
        'FORM_ID' => Array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_UNIQUE_FORM_ID'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'FORM' . mt_rand(1, 10),
            'PARENT' => 'BASE',
        ),
        'FORM_NAME' => Array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_FORM_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_FORM_NAME_DEFAULT') . mt_rand(1, 10),
            'PARENT' => 'BASE',
        ),
        'WIDTH_FORM' => Array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_WIDTH_FORM'),
            'TYPE' => 'STRING',
            'DEFAULT' => '500px',
            'PARENT' => 'BASE',
        ),
        'DISPLAY_FIELDS' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SLAM_EASYFORM_DISPLAY_FIELDS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'REFRESH' => 'Y',
            'VALUES' => $arFieldsResultType,
            'ADDITIONAL_VALUES' => 'Y',
            'SIZE' => 8,
            'DEFAULT' => $defaultFields,
        ),
        'REQUIRED_FIELDS' => Array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_REQUIRED_FIELDS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $curFields,
            'SIZE' => count($curFields),
            'REFRESH' => 'Y',
            'DEFAULT' => array('EMAIL'),
            'PARENT' => 'BASE',
        ),
        'FIELDS_ORDER' => Array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELDS_ORDER'),
            'PARENT' => 'BASE',
            'TYPE' => 'CUSTOM',
            'JS_FILE' => $componentPath."/lib/settings/dragdrop_order/script.js",
            'JS_EVENT' => 'initDraggableOrderControl',
            'JS_DATA' => Json::encode($curFields),
            'TEXT_FIELD_ADD' => Loc::getMessage('SLAM_EASYFORM_FIELD_ADD'),
            'DEFAULT' => 'TITLE,EMAIL,PHONE,MESSAGE'
        ),
        'USE_FORMVALIDATION_JS' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_FORMVALIDATION_JS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
            'PARENT' => 'JS_VALIDATE_SETTINGS',
        ),
        'FORM_AUTOCOMPLETE' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_FORM_AUTOCOMPLETE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'BASE',
        ),
        'HIDE_FIELD_NAME' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_HIDE_FIELD_NAME'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
        'HIDE_ASTERISK' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_HIDE_ASTERISK'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
        'FORM_SUBMIT_VALUE' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_FORM_SUBMIT_VALUE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_FORM_SUBMIT_VALUE_DEFAULT'),
            'COLS' => 50,
            'PARENT' => 'BASE',
        ),
        'ENABLE_SEND_MAIL' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_ENABLE_SEND_MAIL'),
            'TYPE' => 'CHECKBOX',
            'REFRESH' => 'Y',
            'DEFAULT' => 'Y',
            'PARENT' => 'MAIL',
        ),
        'USE_CAPTCHA' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_CAPTCHA'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'CAPTCHA',
            'REFRESH' => 'Y',
        ),
        'USE_IBLOCK_WRITE' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_IBLOCK_WRITE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'IBLOCK_WRITE',
            'REFRESH' => 'Y',
        ),

        'USE_JQUERY' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_INCLUDE_JQUERY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'JS_LIB_SETTINGS',
        ),
        'USE_BOOTSRAP_CSS' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_BOOTSRAP_CSS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'JS_LIB_SETTINGS',
        ),
        'USE_BOOTSRAP_JS' => array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_BOOTSRAP_JS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'JS_LIB_SETTINGS',
        ),
    ),
);

if ($arCurrentValues['USE_FORMVALIDATION_JS'] != 'N') {
    $arComponentParameters['PARAMETERS']['HIDE_FORMVALIDATION_TEXT'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_HIDE_FORMVALIDATION_TEXT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
        'PARENT' => 'JS_VALIDATE_SETTINGS',
    );
    $arComponentParameters['PARAMETERS']['INCLUDE_BOOTSRAP_JS'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_INCLUDE_FORMVALIDATION_LIBS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'N',
        'PARENT' => 'JS_VALIDATE_SETTINGS',
    );
}


//PERSONAL DATA PARAMS
$includeModuleSlam = Loader::includeModule("slam.easyform");
if ($includeModuleSlam) {
    $arComponentParameters['PARAMETERS']['USE_MODULE_VARNING'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_MODULE_VARNING'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
        'PARENT' => 'PERSONAL_DATA',
    );
}
if ($arCurrentValues['USE_MODULE_VARNING'] != 'Y' || !$includeModuleSlam) {
    $arComponentParameters['PARAMETERS']['FORM_SUBMIT_VARNING'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FORM_SUBMIT_VARNING'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_FORM_SUBMIT_VARNING_DEFAULT'),
        'COLS' => 50,
        'ROWS' => 7,
        'PARENT' => 'PERSONAL_DATA',
    );
}

//SEND AJAX PARAMS
$arComponentParameters['PARAMETERS']['SEND_AJAX'] = array(
    'NAME' => Loc::getMessage('SLAM_EASYFORM_SEND_AJAX'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'PARENT' => 'SUBMIT',
    'REFRESH' => 'Y',
);
if ($arCurrentValues['SEND_AJAX'] != 'N') {
    $arComponentParameters['PARAMETERS']['SHOW_MODAL'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_SHOW_MODAL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'SUBMIT',
        'REFRESH' => 'Y',
    );
    $arComponentParameters['PARAMETERS']['_CALLBACKS'] = array(
        'PARENT' => 'SUBMIT',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FUNCTION_CALLBACKS_SUCCESS'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    );
}

//MODAL PARAMS
if ($arCurrentValues['SHOW_MODAL'] != 'N') {
	$arComponentParameters['PARAMETERS']['TITLE_SHOW_MODAL'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_TITLE_SHOW_MODAL'),
        'TYPE' => 'STRING',
        'DEFAULT' =>  Loc::getMessage('SLAM_EASYFORM_DEFAULT_TITLE_SHOW_MODAL'),
        'PARENT' => 'SUBMIT',
    );
}
	

$arComponentParameters['PARAMETERS']['OK_TEXT'] = array(
    'NAME' => Loc::getMessage('SLAM_EASYFORM_OK_MESSAGE'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_OK_TEXT'),
    'PARENT' => 'SUBMIT',
    'COLS' => 47,
    'ROWS' => 4,
);
$arComponentParameters['PARAMETERS']['ERROR_TEXT'] = array(
    'NAME' => Loc::getMessage('SLAM_EASYFORM_ERROR_MESSAGE'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_ERROR_TEXT'),
    'PARENT' => 'SUBMIT',
    'COLS' => 47,
    'ROWS' => 4,
);


//IBLOCK_WRITE
if ($arCurrentValues['USE_IBLOCK_WRITE'] == 'Y' && CModule::IncludeModule('iblock')) {

    $arTypesEx = CIBlockParameters::GetIBlockTypes(Array('-' => ' '));

    $arIBlocks = Array();
    $res_iblock = CIBlock::GetList(Array('SORT' => 'ASC'), Array('TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')));
    while ($arRes = $res_iblock->Fetch()) {
        $arIBlocks[$arRes['ID']] = $arRes['NAME'];
    }

    if ($arCurrentValues['CREATE_IBLOCK'] == 'Y' && !array_key_exists('formresult', $arTypesEx)) {

        $arNewTypeIBFields = Array(
            'ID' => 'formresult',
            'SECTIONS' => 'N',
            'IN_RSS' => 'N',
            'SORT' => 1000,
            'LANG' => Array(
                'ru' => Array(
                    'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_RU_NAME'),
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => ''
                ),
                'en' => Array(
                    'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_EN_NAME'),
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => ''
                )
            )
        );
        $obBlocktype = new CIBlockType;
        $res = $obBlocktype->Add($arNewTypeIBFields);

        $ib = new CIBlock;
        $arNewIBFields = Array(
            'ACTIVE' => 'Y',
            'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_RU_NAME'),
            'CODE' => 'form-result',
            'LIST_PAGE_URL' => '',
            'DETAIL_PAGE_URL' => '',
            'IBLOCK_TYPE_ID' => 'formresult',
            'SITE_ID' => $arSiteId,
            'SORT' => '500',
            'VERSION' => '2',
            'GROUP_ID' => Array('2' => 'R')
        );
        $ID = $ib->Add($arNewIBFields);

        $arTypesEx = CIBlockParameters::GetIBlockTypes(Array('-' => ' '));

        $arIBlocks = Array();
        $res_iblock = CIBlock::GetList(Array('SORT' => 'ASC'), Array('TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')));
        while ($arRes = $res_iblock->Fetch()) {
            $arIBlocks[$arRes['ID']] = $arRes['NAME'];
        }
    }




    $arComponentParameters['PARAMETERS']['CREATE_IBLOCK'] = array(
        'PARENT' => 'IBLOCK_WRITE',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_PROP_ADD_NAME'),
        'TYPE' => 'CUSTOM',
        'DEFAULT' => '',
        'JS_FILE' => '/bitrix/components/slam/easyform/lib/settings/button_send_script.js',
        'JS_EVENT' => 'initCreateSendIblock',
        "JS_DATA" => Loc::getMessage('SLAM_EASYFORM_CREATE_SEND_IBLOCK_BUTTON'),
    );


    $arComponentParameters['PARAMETERS']['IBLOCK_TYPE'] = array(
        'PARENT' => 'IBLOCK_WRITE',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_DESC_LIST_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTypesEx,
        'DEFAULT' => 'news',
        'REFRESH' => 'Y',
    );
    $arComponentParameters['PARAMETERS']['IBLOCK_ID'] = array(
        'PARENT' => 'IBLOCK_WRITE',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_DESC_LIST_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks,
        'DEFAULT' => '',
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y',
    );

    if ($arCurrentValues['IBLOCK_ID'] > 0) {
        $arComponentParameters['PARAMETERS']['ACTIVE_ELEMENT'] = Array(
            'PARENT' => 'IBLOCK_WRITE',
            'NAME' => Loc::getMessage('SLAM_EASYFORM_ACTIVE_ELEMENT'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y',
        );
    }
}

//CAPTCHA
if ($arCurrentValues['USE_CAPTCHA'] == 'Y') {

    if($includeModuleSlam){
        $arComponentParameters["PARAMETERS"]['CUSTOM_FORM'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_CAPTCHA_BUTTON_NAME'),
            "PARENT" => "CAPTCHA",
            'TYPE' => 'CUSTOM',
            'JS_FILE' => $componentPath.'/lib/settings/captcha_btn.js',
            'JS_EVENT' => 'JCCustomFormOpen',
            'JS_DATA' => Json::encode(array(
                'LANG' => LANGUAGE_ID,
                'BUTTON_NAME' => Loc::getMessage('SLAM_EASYFORM_CAPTCHA_BUTTON_NAME'),
                'COMPONENT_PATH' => $componentPath
            )),
        );
    }else{
        $arComponentParameters['PARAMETERS']['CAPTCHA_KEY'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_CAPTCHA_KEY'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
            'PARENT' => 'CAPTCHA',
        );
        $arComponentParameters['PARAMETERS']['CAPTCHA_SECRET_KEY'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_CAPTCHA_SECRET_KEY'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
            'PARENT' => 'CAPTCHA',
        );
    }



  
    $arComponentParameters['PARAMETERS']['CAPTCHA_TITLE'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_CAPTCHA_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
        'PARENT' => 'CAPTCHA',
    );
}

//SEND EMAIL PARAMS
if (empty($arCurrentValues['ENABLE_SEND_MAIL']) || $arCurrentValues['ENABLE_SEND_MAIL'] != 'N') {
    $arEvent = Array();
    $arFilter = Array('TYPE_ID' => 'SLAM_EASYFORM', 'ACTIVE' => 'Y');
    $dbType = CEventMessage::GetList($by = 'ID', $order = 'DESC', $arFilter);
    while ($arType = $dbType->GetNext()) {
        $arEvent[$arType['ID']] = '[' . $arType['ID'] . '] ' . $arType['SUBJECT'];
    }


    $arComponentParameters['PARAMETERS']['CREATE_SEND_MAIL'] = array(
        'PARENT' => 'MAIL',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_CREATE_SEND_MAIL_BUTTON'),
        'TYPE' => 'CUSTOM',
        'DEFAULT' => '',
        'JS_FILE' => '/bitrix/components/slam/easyform/lib/settings/button_send_script.js',
        'JS_EVENT' => 'initCreateSendMail',
        "JS_DATA" => Loc::getMessage('SLAM_EASYFORM_CREATE_SEND_MAIL'),
    );


    $arComponentParameters['PARAMETERS']['EVENT_MESSAGE_ID'] = array(
        'PARENT' => 'MAIL',
        'NAME' => Loc::getMessage('SLAM_EASYFORM_EMAIL_TEMPLATES'),
        'TYPE' => 'LIST',
        'VALUES' => $arEvent,
        'DEFAULT' => '',
        'MULTIPLE' => 'Y',
        'COLS' => 15,
    );
    
    $arComponentParameters['PARAMETERS']['EMAIL_TO'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_EMAIL_TO'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
        'PARENT' => 'MAIL',
    );
    $arComponentParameters['PARAMETERS']['EMAIL_BCC'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_BCC'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
        'PARENT' => 'MAIL',
    );

    $arComponentParameters['PARAMETERS']['MAIL_SUBJECT_ADMIN'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_MAIL_SUBJECT_ADMIN'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_MAIL_SUBJECT_ADMIN_DEFAULT'),
        'COLS' => 50,
        'PARENT' => 'MAIL',
    );


    $arComponentParameters['PARAMETERS']['EMAIL_FILE'] = array(
        'NAME' => Loc::getMessage('SLAM_EASYFORM_SEND_EMAIL_FILE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'N',
        'PARENT' => 'MAIL',
    );


    if(!empty($arCurrentValues['DISPLAY_FIELDS']) && in_array('EMAIL', $arCurrentValues['DISPLAY_FIELDS'])) {
        $arComponentParameters['PARAMETERS']['EMAIL_SEND_FROM'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_SEND_FROM'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y',
            'PARENT' => 'MAIL',
        );

        if($arCurrentValues['EMAIL_SEND_FROM'] == 'Y') {
            $arComponentParameters['PARAMETERS']['CREATE_SEND_MAIL_SENDER'] = array(
                'PARENT' => 'MAIL',
                'NAME' => Loc::getMessage('SLAM_EASYFORM_CREATE_SEND_MAIL_BUTTON'),
                'TYPE' => 'CUSTOM',
                'DEFAULT' => '',
                'JS_FILE' => '/bitrix/components/slam/easyform/lib/settings/button_send_script.js',
                'JS_EVENT' => 'initCreateSendMail',
                "JS_DATA" => Loc::getMessage('SLAM_EASYFORM_CREATE_SEND_MAIL'),
            );


            $arComponentParameters['PARAMETERS']['EVENT_MESSAGE_ID_SENDER'] = array(
                'PARENT' => 'MAIL',
                'NAME' => Loc::getMessage('SLAM_EASYFORM_EMAIL_TEMPLATES_SENDER'),
                'TYPE' => 'LIST',
                'VALUES' => $arEvent,
                'DEFAULT' => '',
                'MULTIPLE' => 'Y',
                'COLS' => 15,
            );
        
        $arComponentParameters['PARAMETERS']['EMAIL_BCC_SENDER'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_BCC'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
            'PARENT' => 'MAIL',
        );
        
        $arComponentParameters['PARAMETERS']['MAIL_SUBJECT_SENDER'] = array(
            'NAME' => Loc::getMessage('SLAM_EASYFORM_MAIL_SUBJECT_SENDER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_MAIL_SUBJECT_SENDER_DEFAULT'),
            'COLS' => 50,
            'PARENT' => 'MAIL',
        );
        }
    }

}

if (!empty($arCurrentValues['DISPLAY_FIELDS'])) {
    $arDisplayFields = array_diff((array)$arCurrentValues['DISPLAY_FIELDS'], array(''));
    if (strlen($arCurrentValues['FIELDS_ORDER']) > 0) {
        $arSortField = explode(',', $arCurrentValues['FIELDS_ORDER']);
    }
    if (false) {
        $arFormFields = $arSortField;
    } else {
        $arFormFields = $arDisplayFields;
    }
} else {
    $arFormFields = $defaultFields;
}



if (is_array($arFormFields)) {
    $sort = 480;

    if (($arCurrentValues['USE_IBLOCK_WRITE'] == 'Y' || $arCurrentValues['IBLOCK_ID'] > 0) && CModule::IncludeModule('iblock')) {
        $arProperty_LINK = array();
        $rsProp = CIBlockProperty::GetList(array('sort' => 'asc', 'name' => 'asc'), array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y'));

        while ($arr = $rsProp->Fetch()) {
            if (in_array($arr['PROPERTY_TYPE'], array('L', 'N', 'S', 'F'))) {
                $arProperty_LINK[$arr['CODE']] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
            }
        }
        $useWriteIntoIblock = true;
    }
	
	if(empty($arCurrentValues['REQUIRED_FIELDS'])){
        $arCurrentValues['REQUIRED_FIELDS'] = array();
    }
    if(empty($arComponentParameters['PARAMETERS']['REQUIRED_FIELDS']['DEFAULT'])){
        $arComponentParameters['PARAMETERS']['REQUIRED_FIELDS']['DEFAULT']= array();
    }
	
	$mask = false;
	
    foreach ($arFormFields as $arVal) {

        $fieldName = $arFieldsResult[$arVal] ? $arFieldsResult[$arVal] : $arVal;
        $defaultFieldType = $arFields[$arVal]['TYPE'] ? $arFields[$arVal]['TYPE'] : 'text';
        $defaultValue = $arFields[$arVal]['DEFAULT_VALUE'] ? $arFields[$arVal]['DEFAULT_VALUE'] : false;
        $isFieldReq = in_array($arVal, $arCurrentValues['REQUIRED_FIELDS']) ? 'Y' :  ( !empty( $arReqFields ) ? 'N' : (in_array($arVal, $arComponentParameters['PARAMETERS']['REQUIRED_FIELDS']['DEFAULT']) ? 'Y' : 'N'));
        $typeField = $arCurrentValues['CATEGORY_' . $arVal . '_TYPE'] ? $arCurrentValues['CATEGORY_' . $arVal . '_TYPE'] : $arFields[$arVal]['TYPE'];

        $isMultipleVal = 'N';
        if (in_array($typeField, array('select', 'checkbox', 'radio'))) {
            $isMultipleVal = 'Y';
        }


        $arComponentParameters['GROUPS']['CATEGORY_' . $arVal] = array(
            'NAME' => $fieldName . Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_TITLE'),
            'SORT' => $sort,
        );
        $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_TITLE'] = array(
            'PARENT' => 'CATEGORY_' . $arVal,
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => $fieldName,
        );

        $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_TYPE'] = array(
            'PARENT' => 'CATEGORY_' . $arVal,
            'NAME' => Loc::getMessage('SLAM_EASYFORM_TYPE_FIELD'),
            'TYPE' => 'LIST',
            'VALUES' => array(
                'text' => 'text',
                'email' => 'email',
                'tel' => 'tel',
                'textarea' => 'textarea',
                'select' => 'select',
                'file' => 'file',
                'checkbox' => 'checkbox',
                'accept' => 'checkbox '.Loc::getMessage('SLAM_EASYFORM_TYPE_FIELD_ACCEPT'),
                'radio' => 'radio',
                'url' => 'url',
                'number' => 'number',
                'date' => 'date',
                'datetime-local' => 'datetime-local',
                'month' => 'month',
                'week' => 'week',
                'time' => 'time',
                'hidden' => 'hidden',
                //'iblock_select' => 'iblock_select',
                //'stars' => 'stars',
            ),
            'REFRESH' => 'Y',
            'DEFAULT' => $defaultFieldType,
            'ADDITIONAL_VALUES' => 'N',
        );

        //PLACEHOLDER
        if (!in_array($typeField, array('select', 'checkbox', 'radio', 'hidden', 'accept', 'file'))) {
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_PLACEHOLDER'] = array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => 'Placeholder',
                'TYPE' => 'STRING',
            );
        }

        //VALUE
        $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_VALUE'] = array(
            'PARENT' => 'CATEGORY_' . $arVal,
            'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_VALUE'),
            'TYPE' => 'STRING',
            'MULTIPLE' => $isMultipleVal,
            'DEFAULT' => $defaultValue,
            'ADDITIONAL_VALUES' => 'N',
        );

        //SELECT, MULTISELECT
        if($typeField == "select"){
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_ADD_VAL'] = array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_SELECT_ADD'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_SELECT_ADD_DEF'),
                'ADDITIONAL_VALUES' => 'N',
                'HIDDEN' => $arCurrentValues['CATEGORY_' . $arVal . '_MULTISELECT'] != 'Y' ? 'N' : 'Y',
            );
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_MULTISELECT'] = array(
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_SELECTMULTISELECT_ADD'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y',
                'PARENT' => 'CATEGORY_' . $arVal,
            );
        }else {
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_ADD_VAL']);
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_MULTISELECT']);
        }

        //CHECKBOX, RADIO
        if ($typeField == "checkbox" || $typeField == "radio") {
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_SHOW_INLINE'] = array(
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_VIEW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'Y',
                'REFRESH' => 'N',
                'PARENT' => 'CATEGORY_' . $arVal,
            );
        } else {
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_SHOW_INLINE']);
        }

        //JS VALIDATION
        if($arCurrentValues['USE_FORMVALIDATION_JS'] != 'N' && $arCurrentValues['HIDE_FORMVALIDATION_TEXT'] != 'Y'){
            if ($isFieldReq == 'Y') {
                $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_VALIDATION_MESSAGE'] = Array(
                    'PARENT' => 'CATEGORY_' . $arVal,
                    'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => Loc::getMessage('SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE_DEFAULT'),
                );
            }

            $defReqStr = '';
            if ($typeField == 'email') {
                $defReqStr = 'data-bv-emailaddress-message="' . Loc::getMessage('SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE_EMAIL_DEFAULT') . '"';
            }

            if ($typeField != 'hidden' && $typeField != 'file') {
                $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_VALIDATION_ADDITIONALLY_MESSAGE'] = Array(
                    'PARENT' => 'CATEGORY_' . $arVal,
                    'NAME' => Loc::getMessage('SLAM_EASYFORM_FIELD_VALIDATION_ADDITIONALLY_MESSAGE'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => $defReqStr
                );
            } else {
                unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_VALIDATION_ADDITIONALLY_MESSAGE']);
            }
        }
		
		//FILE
        if ($typeField == 'file') {
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_FILE_EXTENSION'] = array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_FILE_EXTENSION'),
                'TYPE' => 'STRING',
                'DEFAULT' => 'doc, docx, xls, xlsx, txt, rtf, pdf, png, jpeg, jpg, gif',
            );
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_FILE_MAX_SIZE'] = array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_FILE_MAX_SIZE'),
                'TYPE' => 'INT',
                'DEFAULT' => '20971520', //20Mb
            );

            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_DROPZONE_INCLUDE'] = array(
                'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_DROPZONE_JS'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'Y',
                'PARENT' => 'CATEGORY_' . $arVal,
            );

            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_VALUE']);
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_TYPE_VALIDATION']);
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_PLACEHOLDER']);
			
        }

        //INPUTMASK
        if($typeField == 'tel') {
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_INPUTMASK'] = array(
                'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_INPUTMASK'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y',
                'PARENT' => 'CATEGORY_' . $arVal,
            );
            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_INPUTMASK_TEMP'] = Array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => Loc::getMessage('SLAM_EASYFORM_GROUP_FIELD_INPUTMASK_TEMP'),
                'TYPE' => 'STRING',
                'DEFAULT' => '+7 (999) 999-9999',
                'REFRESH' => 'Y',
                'HIDDEN' =>  !(isset($arCurrentValues['CATEGORY_' . $arVal . '_INPUTMASK']) && $arCurrentValues['CATEGORY_' . $arVal . '_INPUTMASK'] === 'N')  ? 'N' : 'Y',
            );
            if($arCurrentValues['CATEGORY_' . $arVal . '_INPUTMASK'] == 'Y') {
                $mask = true;
            }
        } else{
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_INPUTMASK']);
            unset($arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_INPUTMASK_TEMP']);
        }

       
        //IBLOCK WRITE
        if ($useWriteIntoIblock) {

            if ($arVal == 'TITLE') {
                $iblockFieldDefault = 'NAME';
            } elseif ($arVal == 'MESSAGE') {
                $iblockFieldDefault = 'PREVIEW_TEXT';
            } else {
                $iblockFieldDefault = 'FORM_' . $arVal;
            }

            $arComponentParameters['PARAMETERS']['CATEGORY_' . $arVal . '_IBLOCK_FIELD'] = array(
                'PARENT' => 'CATEGORY_' . $arVal,
                'NAME' => Loc::getMessage('SLAM_EASYFORM_CATEGORY_IBLOCK_FIELD'),
                'TYPE' => 'LIST',
                'MULTIPLE' => 'N',
                'REFRESH' => 'Y',
                'VALUES' => array_merge(
                    array(
                        'NO_WRITE' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_FIELD_NO_WRITE'),
                        'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_FIELD_NAME'),
                        'DETAIL_TEXT' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_FIELD_DETAIL_TEXT'),
                        'PREVIEW_TEXT' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_FIELD_PREVIEW_TEXT'),
                        'FORM_' . $arVal => Loc::getMessage('SLAM_EASYFORM_IBLOCK_FIELD_FORM'),
                    ), $arProperty_LINK),
                'ADDITIONAL_VALUES' => 'N',
                'DEFAULT' => $iblockFieldDefault,
            );
        }

        $sort++;
    }

	if($mask)
		$arComponentParameters['PARAMETERS']['USE_INPUTMASK_JS'] = array(
			'NAME' => Loc::getMessage('SLAM_EASYFORM_USE_INPUTMASK_JS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT' => 'JS_LIB_SETTINGS',
	);
}
?>

