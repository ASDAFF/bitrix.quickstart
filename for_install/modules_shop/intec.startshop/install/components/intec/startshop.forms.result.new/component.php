<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    global $APPLICATION;

    CStartShopTheme::ApplyTheme(SITE_ID);

    $arDefaultParams = array(
        'REQUEST_VARIABLE_ACTION' => 'action',
        'FORM_VARIABLE_CAPTCHA_SID' => 'CAPTCHA_SID',
        'FORM_VARIABLE_CAPTCHA_CODE' => 'CAPTCHA_CODE',
        'FORM_ID' => '',
        'AJAX_MODE' => 'N'
    );

    $arParams = array_merge($arDefaultParams, $arParams);
    $arResult = CStartShopForm::GetByID($arParams['FORM_ID'])->Fetch();
    $sAction = $_REQUEST[$arParams['REQUEST_VARIABLE_ACTION']];

    if (!empty($arResult)) {
        $arResult['SENT'] = false;

        if (in_array(SITE_ID, $arResult['SID'])) {
            $bContinue = true;

            $arResult['PROPERTIES'] = CStartShopUtil::DBResultToArray(CStartShopFormProperty::GetList(array('SORT' => 'ASC'), array(
                'FORM' => $arResult['ID'],
                'ACTIVE' => 'Y'
            )), 'ID');

            $arResult['ERROR'] = array('CODE' => 0); // Код ошибки 0: Все впорядке

            if (empty($arResult['PROPERTIES'])) {
                $arResult['ERROR'] = array('CODE' => 3); // Код ошибки 3: У формы нет полей
                $bContinue = false;
            }

            if ($sAction == 'send' && $bContinue) {
                if ($arResult['USE_CAPTCHA'] == 'Y') {
                    if (!$APPLICATION->CaptchaCheckCode($_REQUEST[$arParams['FORM_VARIABLE_CAPTCHA_CODE']], $_REQUEST[$arParams['FORM_VARIABLE_CAPTCHA_SID']])) {
                        $arResult['ERROR'] = array('CODE' => 4); // Код ошибки 4: Неверный код проверки
                        $bContinue = false;
                    }
                }

                if ($bContinue) {
                    $arFields = array();
                    $arFieldsEmpty = array();
                    $arFieldsInvalid = array();

                    foreach ($arResult['PROPERTIES'] as $arProperty) {
                        $cPropertyValue = $_REQUEST[$arProperty['CODE']];

                        if ($arProperty['TYPE'] != STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT) {
                            $cPropertyValue = strval($cPropertyValue);
                        } else {
                            $cPropertyValue = is_array($cPropertyValue) ? $cPropertyValue : array();
                        }

                        if ($arProperty['REQUIRED'] == 'Y' && $arProperty['TYPE'] != STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX && empty($cPropertyValue)) {
                            $arFieldsEmpty[$arProperty['CODE']] = $arProperty;
                        } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT || $arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA) {
                            if (!preg_match('/'.$arProperty['DATA']['EXPRESSION'].'/'.$arProperty['DATA']['EXPRESSION_FLAGS'], $cPropertyValue))
                                $arFieldsInvalid[$arProperty['CODE']] = $arProperty;
                        }

                        $arFields[$arProperty['ID']] = $cPropertyValue;
                    }

                    $bContinue = empty($arFieldsEmpty) && empty($arFieldsInvalid);

                    if ($bContinue) {
                        $iResultID = CStartShopForm::CreateResult($arResult['ID'], $arFields);

                        if ($iResultID) {
                            $arResult['SENT'] = true;
                        } else {
                            $arResult['ERROR'] = array('CODE' => 6); // Код ошибки 6: Неизвестная ошибка
                        }
                    } else {
                        $arResult['ERROR'] = array(
                            'CODE' => 5,
                            'FIELDS' => array(
                                'EMPTY' => $arFieldsEmpty,
                                'INVALID' => $arFieldsInvalid
                            )
                        ); // Код ошибки 5: Некоторые поля не заполнены или неверны
                    }
                }
            }
        } else {
            $arResult = array();
            $arResult['SENT'] = false;
            $arResult['ERROR'] = array('CODE' => 2); // Код ошибки 2: Форма не принадлежит к данному сайту
        }
    } else {
        $arResult = array();
        $arResult['SENT'] = false;
        $arResult['ERROR'] = array('CODE' => 1); // Код ошибки 1: Форма с таким идентификатором не найдена
    }

    $this->IncludeComponentTemplate();
?>