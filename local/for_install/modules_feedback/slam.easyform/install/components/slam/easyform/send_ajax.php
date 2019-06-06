<?
define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use \Bitrix\Main\Localization\Loc;


Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/slam/easyform/.parameters.php');

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$arSiteId = array();
$rsSite = CSite::GetList($by = 'sort', $order = 'asc', Array('ACTIVE' => 'Y'));
while ($arSite = $rsSite->fetch()) {
    $arSiteId[] = $arSite['LID'];
}

if($request['action'] == 'add') {
    switch ($request['type']) {
        case 'event':


            $arEventMessFields = array(
                'ACTIVE' => 'Y',
                'EVENT_NAME' => 'SLAM_EASYFORM',
                'LID' => $arSiteId,
                'EMAIL_FROM' => Loc::getMessage('SLAM_EASYFORM_EMAIL_FROM'),
                'EMAIL_TO' => Loc::getMessage('SLAM_EASYFORM_EVEN_EMAIL_TO'),
                'BCC' => Loc::getMessage('SLAM_EASYFORM_EVEN_BCC'),
                'SUBJECT' => Loc::getMessage('SLAM_EASYFORM_SUBJECT'),
                'BODY_TYPE' => 'html',
                'MESSAGE' => Loc::getMessage('SLAM_EASYFORM_MESSAGE'),
            );
            $APPLICATION->RestartBuffer();
            $eventM = new CEventMessage;
            $result = $eventM->Add($arEventMessFields);
            if ($result) {
                ob_end_clean();
                $GLOBALS['APPLICATION']->RestartBuffer();
                echo \Bitrix\Main\Web\Json::encode(array('value' => $result, "text" => '[' . $result . '] ' . Loc::getMessage('SLAM_EASYFORM_SUBJECT')));
                die();
            }

        break;
        case 'iblock':

            if(CModule::IncludeModule('iblock')) {


                $arTypesEx = \CIBlockParameters::GetIBlockTypes(Array('-' => ' '));


                if(!array_key_exists('formresult', $arTypesEx)) {
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
                }


                $key = rand(1, 100);

                $ib = new CIBlock;
                $arNewIBFields = Array(
                    'ACTIVE' => 'Y',
                    'NAME' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_RU_NAME').' '.$key,
                    'CODE' => 'form-result-'.$key,
                    'LIST_PAGE_URL' => '',
                    'DETAIL_PAGE_URL' => '',
                    'IBLOCK_TYPE_ID' => 'formresult',
                    'SITE_ID' => $arSiteId,
                    'SORT' => '500',
                    'VERSION' => '2',
                    'GROUP_ID' => Array('2' => 'R')
                );
                $result = $ib->Add($arNewIBFields);


                $APPLICATION->RestartBuffer();
                if ($result) {
                    ob_end_clean();
                    $GLOBALS['APPLICATION']->RestartBuffer();
                    echo \Bitrix\Main\Web\Json::encode(array(
                        'value' => $result,
                        'text' => Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_RU_NAME').' '.$key,
                        'type_value' => 'formresult',
                        'type_text' => '[formresult] '.Loc::getMessage('SLAM_EASYFORM_IBLOCK_LANG_RU_NAME'),
                    ));
                    die();
                }

            }

        break;
    }
}
?>