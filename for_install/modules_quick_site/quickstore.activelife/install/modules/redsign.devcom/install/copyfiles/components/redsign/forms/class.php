<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Iblock;
use \Bitrix\Main\Config\Option;

class RedsignForms extends CBitrixComponent
{
    protected $messages = array(
        'ERRORS' => array(),
        'SUCCESS' => array(),
    );

    protected $fields = array();

    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    public function onPrepareComponentParams($params)
    {
        if (isset($params['USE_CAPTCHA']) && $params['USE_CAPTCHA'] == 'Y') {
            include_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/captcha.php';
        }

        return $params;
    }

    public function getFields()
    {
        if (empty($this->arParams['IBLOCK_ID'])) {
            return false;
        }

        $propertyIterator = Iblock\PropertyTable::getList(array(
            'filter' => array(
                '=ACTIVE' => 'Y',
                '=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            ),
            'order' => array('SORT' => 'ASC'),
        ));

        while ($arProperty = $propertyIterator->fetch()) {
            if ($arProperty['PROPERTY_TYPE'] != 'S' && $arProperty['PROPERTY_TYPE'] != 'L') {
                continue;
            }

            if ($arProperty['PROPERTY_TYPE'] == 'L') {
                $propertyEnumIterator = Iblock\PropertyEnumerationTable::getList(array('filter' => array('=PROPERTY_ID' => $arProperty['ID'])));
                $arProperty['VALUES'] = $propertyEnumIterator->fetchAll();
            }

            $arProperty['CURRENT_VALUE'] = '';
            $this->fields[] = $arProperty;
        }
    }

    protected function getCaptchaCode()
    {
        $cpt = new CCaptcha();
        $captchaPass = Option::get('main', 'captcha_password', '');
        if (strlen($captchaPass) <= 0) {
            $captchaPass = randString(10);
            Option::set('main', 'captcha_password', $captchaPass);
        }
        $cpt->SetCodeCrypt($captchaPass);

        return htmlspecialcharsbx($cpt->GetCodeCrypt());
    }

    protected function getResult()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $this->arResult['REQUEST_URI'] = $request->getRequestUri();

        $this->arResult['FIELDS'] = $this->fields;

        if (isset($this->arParams['USE_CAPTCHA']) && $this->arParams['USE_CAPTCHA'] == 'Y') {
            $this->arResult['CAPTCHA_CODE'] = $this->getCaptchaCode();
            $this->arResult['USE_CAPTCHA'] = 'Y';
        } else {
            $this->arResult['USE_CAPTCHA'] = 'N';
        }

        $this->arResult['MESSAGES'] = $this->messages;
    }

    protected function checkCaptcha($code, $word)
    {
        $cpt = new CCaptcha();
        $pass = Option::get('main', 'captcha_password', '');

        if (strlen($code) > 0 && $cpt->CheckCodeCrypt($word, $code, $pass)) {
            return true;
        }

        return false;
    }

    protected function save()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        if ($request->isPost() && check_bitrix_sessid()) {
            $arProps = array();

            foreach ($this->fields as &$arField) {
                if (!empty($arField['USER_TYPE']) && $arField['USER_TYPE'] == 'HTML') {
                    $arProps[$arField['CODE']] = array(
                      'VALUE' => array(
                          'TYPE' => 'TEXT',
                          'TEXT' => $request->getPost('FIELD_'.$arField['CODE'])
                      )
                    );

                    $arField['CURRENT_VALUE'] = $request->getPost('FIELD_'.$arField['CODE']);
                }
                else {
                    $arProps[$arField['CODE']] = $arField['CURRENT_VALUE'] = $request->getPost('FIELD_'.$arField['CODE']);
                }
            }
            unset($arField);

            if ((isset($this->arParams['USE_CAPTCHA']) && $this->arParams['USE_CAPTCHA'] == 'Y')) {
                $captchaCode = $request->getPost('captcha_sid');
                $captchaWord = $request->getPost('captcha_word');
                if (!$this->checkCaptcha($captchaCode, $captchaWord)) {
                    $this->messages['ERRORS'][] = Loc::getMessage('RS_FORMS_CAPTCHA_ERROR');

                    return fasle;
                }
            }

            $name = $request->getPost('FORM_NAME');
            if (empty($name)) {
                $name = ConvertTimeStamp(false, 'FULL');
            }

            $el = new CIBlockElement();
            $elId = $el->add(array(
                'IBLOCK_SECTION_ID' => false,
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'CODE' => md5($name),
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES' => $arProps,
            ));

            if ($elId) {
                $this->messages['SUCCESS'][] = isset($this->$arParams['SUCCESS_MESSAGE']) ? $this->arParams['SUCCESS_MESSAGE'] : Loc::getMessage('RS_FORMS_SUCCESS_MSG');

                return true;
            } else {
                $this->messages['ERRORS'] += explode('<br>', $el->LAST_ERROR);
                unset($this->messages[count($this->messages) - 1]);

                return false;
            }
        }

        return false;
    }

    protected function send()
    {
        if (empty($this->arParams['EVENT_TYPE']) || empty($this->arParams['EMAIL_TO'])) {
            return false;
        }

        $arEventFields = array();

        foreach ($this->fields as $field) {
            $arEventFields[$field['CODE']] = $field['CURRENT_VALUE'];
        }

        $arEventFields['EMAIL_TO'] = $this->arParams['EMAIL_TO'];

        CEvent::Send($this->arParams['EVENT_TYPE'], SITE_ID, $arEventFields, 'N');
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('iblock')) {
            return false;
        }

        $this->setFramemode(false);

        $this->getFields();
        if ($this->save()) {
            $this->send();
        }
        $this->getResult();

        $this->includeComponentTemplate();
    }
}
