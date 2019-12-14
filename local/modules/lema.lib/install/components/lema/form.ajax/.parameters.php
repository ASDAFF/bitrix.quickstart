<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('iblock');

//iblock data
$iblockTypes = \CIBlockParameters::GetIBlockTypes();
$iblockIds = array();

$res = \Bitrix\Iblock\IblockTable::getList(array(
    'filter' => array(
        'IBLOCK_TYPE_ID' => (empty($arCurrentValues['IBLOCK_TYPE']) ? current(array_keys($iblockTypes)) : $arCurrentValues['IBLOCK_TYPE'])
    ),
    'order' => array('SORT' => 'ASC'),
    'select' => array('ID', 'NAME')
));
while($row = $res->fetch())
    $iblockIds[$row['ID']] = $row['NAME'];

//email type data
$emailTemplates = array();
$res = \CEventType::GetList(array(), array('SORT' => 'ASC'));
while($row = $res->fetch())
    $emailTypes[$row['ID']] = $row['NAME'];

$params = array(
    'FORM_CLASS' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_CLASS'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => 'ajax-form',
        'REFRESH' => 'N',
    ),
    'FORM_ACTION' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_ACTION'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => '',
        'REFRESH' => 'N',
    ),
    'FORM_152_FZ' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_152_FZ'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => 'Я ознакомлен <a target="_blank" href="/contacts/apply.pdf">c положением об обработке и защите персональных данных.</a>',
        'REFRESH' => 'N',
    ),
    'FORM_BTN_TITLE' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_BTN_TITLE'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => Loc::getMessage('Lema_FORM_AJAX_BTN_SEND'),
        'REFRESH' => 'N',
    ),
    'FORM_SUCCESS_FUNCTION' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_SUCCESS_FUNCTION'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => '$.fancybox.open("Ваше сообщение успешно отправлено")',
        'REFRESH' => 'N',
    ),
    'FORM_SUCCESS_FUNCTION_CORRECT_JSON' => array(
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_FORM_SUCCESS_FUNCTION_CORRECT_JSON'),
        'TYPE' => 'CHECKBOX',
        'MULTIPLE' => 'N',
        'DEFAULT' => 'Y',
        'REFRESH' => 'N',
    ),
    'NEED_SAVE_TO_IBLOCK' => array(
        'PARENT' => 'SAVE_TO_IBLOCK',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_NEED_SAVE_TO_IBLOCK'),
        'TYPE' => 'CHECKBOX',
        'MULTIPLE' => 'N',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
    ),
);

if(
    isset($params['NEED_SAVE_TO_IBLOCK']['DEFAULT']) && !isset($arCurrentValues['NEED_SAVE_TO_IBLOCK']) && $params['NEED_SAVE_TO_IBLOCK']['DEFAULT'] == 'Y' ||
    isset($arCurrentValues['NEED_SAVE_TO_IBLOCK']) && $arCurrentValues['NEED_SAVE_TO_IBLOCK'] != 'N'
)
{
    $params['IBLOCK_TYPE'] = array(
        'PARENT' => 'SAVE_TO_IBLOCK',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $iblockTypes,
        'DEFAULT' => 'news',
        'REFRESH' => 'Y',
    );
    $params['IBLOCK_ID'] = array(
        'PARENT' => 'SAVE_TO_IBLOCK',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $iblockIds,
        'DEFAULT' => 'news',
        'REFRESH' => 'Y',
    );
}

$params['NEED_SEND_EMAIL'] = array(
    'PARENT' => 'SEND_EMAIL',
    'NAME' => Loc::getMessage('Lema_FORM_AJAX_NEED_SEND_EMAIL'),
    'TYPE' => 'CHECKBOX',
    'MULTIPLE' => 'N',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y',
);


if(
    isset($params['NEED_SEND_EMAIL']['DEFAULT']) && !isset($arCurrentValues['NEED_SEND_EMAIL']) && $params['NEED_SEND_EMAIL']['DEFAULT'] == 'Y' ||
    isset($arCurrentValues['NEED_SEND_EMAIL']) && $arCurrentValues['NEED_SEND_EMAIL'] != 'N'
)
{
    $params['EVENT_TYPE'] = array(
        'PARENT' => 'SEND_EMAIL',
        'NAME' => Loc::getMessage('Lema_FORM_AJAX_EVENT_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $emailTypes,
        'DEFAULT' => 'news',
        'REFRESH' => 'N',
    );
}

$params['CACHE_TIME'] = array('DEFAULT' => 3600);
/*$params['CACHE_GROUPS'] = array(
    'PARENT' => 'CACHE_SETTINGS',
    'NAME' => Loc::getMessage('Lema_FORM_AJAX_CACHE_GROUPS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
);*/

$componentDir = preg_replace('~^' . preg_quote($_SERVER['DOCUMENT_ROOT']) . '~ui', '', __DIR__, 1);

$params['FORM_FIELDS'] = array(
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('Lema_FORM_AJAX_IBLOCK_FIELDS'),
    'TYPE' => 'CUSTOM',
    'CUSTOM_DIR' => $componentDir,
    'JS_FILE' => $componentDir . '/custom/custom.js?' . time(),
    'JS_DATA' => $arCurrentValues['FORM_FIELDS'],
    'JS_EVENT' => 'showCustom',
);

$arComponentParameters = array(
    'GROUPS' => array(
        'SAVE_TO_IBLOCK' => array(
            'NAME' => Loc::getMessage('Lema_FORM_AJAX_SAVE_TO_IBLOCK'),
            'SORT' => '500',
        ),
        'SEND_EMAIL' => array(
            'NAME' => Loc::getMessage('Lema_FORM_AJAX_SEND_EMAIL'),
            'SORT' => '500',
        ),
    ),
    'PARAMETERS' => $params,
);