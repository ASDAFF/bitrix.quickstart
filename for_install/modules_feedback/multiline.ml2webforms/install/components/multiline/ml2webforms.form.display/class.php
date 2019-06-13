<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @var CMain $APPLICATION;
 */
use \Ml2WebForms\WebFormsRequestController;

class CMl2WebFormsFormDisplay extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $formCode = isset($arParams['ID']) ? $arParams['ID'] : '';
        $formCode = preg_replace('/([^a-z0-9\_]+)/', '_', $formCode);
        $result = array(
            'ID' => $formCode,
        );
        return $result;
    }

    public function executeComponent()
    {
        \CModule::IncludeModule('multiline.ml2webforms');

        $wfrc = new WebFormsRequestController($this->arParams['ID']);
        $webForm = $wfrc->getWebForm();
        $this->arResult['TPL'] = $wfrc->getFormTemplate();
        $this->arResult['FIELDS'] = $webForm->getFields();
        foreach ($webForm->fieldsVariantsLists as $field => $list) {
            $this->arResult['FIELDS'][$field]['list'] = $list;
        }

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}