<?php
//check send data
empty($_POST) && exit;
//check arParams
empty($_POST['arParams']) && exit;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$arParams = \CUtil::JsObjectToPhp($_POST['arParams']);

(empty($arParams) || empty($arParams['FORM_FIELDS'])) && exit;

$dataFields = json_decode(htmlspecialcharsback($arParams['FORM_FIELDS']), true);

$rules = $checkData = $orderedDataFields = array();

foreach($dataFields as $field)
{
    if(isset($field['required']) && $field['required'] == 'Y')
        $rules[] = array($field['name'], 'required', array('message' => 'Данное поле обязательно к заполнению'));
    if(isset($field['type']) && $field['type'] == 'email')
        $rules[] = array($field['name'], 'email');
    if(false !== stripos($field['name'], 'phone'))
        $rules[] = array($field['name'], 'phone', array('message' => 'Телефон должен быть в формате +7 (999) 666-33-11'));
    $checkData[$field['name']] = isset($_POST[$field['name']]) ? $_POST[$field['name']] : null;
    $orderedDataFields[$field['name']] = $field;
}

$form = new \Lema\Forms\AjaxForm();

$form->setRules($rules)->setFields($checkData);


if($form->validate())
{
    $fields = array();
    foreach($dataFields as $field)
        $fields[mb_strtoupper($field['name'], SITE_CHARSET)] = $form->getField($field['name']);

    $status = true;
    //отправка сообщения с событием $arParams['EVENT_TYPE']
    if(isset($arParams['NEED_SEND_EMAIL'], $arParams['EVENT_TYPE']) && $arParams['NEED_SEND_EMAIL'] === 'Y')
    {
        $res = \CEventType::GetList(array('ID' => $arParams['EVENT_TYPE']));
        if(!($row = $res->Fetch()))
            $status = false;
        if($status)
            $status = $status && $form->sendMessage($row['EVENT_NAME'], $fields);
    }

    //добавление записи в инфоблок с ID = $arParams['IBLOCK_ID']
    if(isset($arParams['NEED_SAVE_TO_IBLOCK'], $arParams['IBLOCK_ID']) && $arParams['NEED_SAVE_TO_IBLOCK'] === 'Y')
    {
        $previewText = null;
        foreach($form->getFields() as $field => $value)
            $previewText .= htmlspecialcharsbx((isset($orderedDataFields[$field]['title']) ? $orderedDataFields[$field]['title'] : $field) . ': ' . $value) . PHP_EOL;
        $status = $status && $form->addRecord($arParams['IBLOCK_ID'], array(
            'NAME' => $form->getField('name'),
            'PREVIEW_TEXT' => $previewText,
            'ACTIVE' => 'N',
            'PROPERTY_VALUES' => $fields,
        ));
    }

    if($status)
    {
        echo json_encode(array('status' => true));
    }
    else
    {
        echo json_encode(array('errors' => $form->getErrors()));
    }
}
else
    echo json_encode(array('errors' => $form->getErrors()));