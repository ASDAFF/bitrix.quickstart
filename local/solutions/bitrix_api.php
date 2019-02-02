<?php

$iblockId;
$sectionId;
$elementId;
$fileId;
$priceId;
$prodId;
$basketId;
$subId;

$name;
$res;

$arOrder;
$arSelect;
$arFilter;
$arImage;
$arFields;

/***************************************
 ************** ИНФОБЛОКИ **************
 ***************************************/

CModule::IncludeModule('iblock');

/*******
 ******* КЛАССЫ
 *******/

/***
 *** CIBlockElement
 ***/

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php
 */
$arOrder = array();
$arSelect = array();
$arFilter = array(
  'IBLOCK_ID' => $iblockId,
	'ACTIVE' => 'Y'
);
$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
while ($arFields = $res->GetNext()) {
	
}

/*
 * GetByID
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getbyid.php
 */
$res = CIBlockElement::GetByID($elementId);
if ($arFields = $res->GetNext()) {
	
}

/*
 * GetProperty
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getproperty.php
 */
$res = CIBlockElement::GetProperty($iblockId, $elementId, $arOrder, $arFilter);
if ($arFields = $res->Fetch()) {
	
}

/*
 * Add
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/add.php
 */
$res = new CIBlockElement;

$arFields = array(
  'MODIFIED_BY'    => $USER->GetID(),
  'IBLOCK_SECTION_ID' => false,
  'IBLOCK_ID'      => $iblockId,
  'PROPERTY_VALUES'=> array(),
  'NAME'           => $name,
  'ACTIVE'         => 'Y'
);

$elementId = $res->Add($arFields);
if (!$elementId = $res->Add($arFields)) {
	echo $res->LAST_ERROR;
}

/*
 * Update
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/update.php
 */
$res = new CIBlockElement;

$arFields = array(
  'MODIFIED_BY'    => $USER->GetID(),
  'IBLOCK_SECTION_ID' => false,
  'PROPERTY_VALUES'=> array(),
  'NAME'           => $name,
  'ACTIVE'         => 'Y'
);

if (!$res->Update($elementId, $arFields)) {
	echo $res->LAST_ERROR;
}

/*
 * Delete
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/delete.php
 */
if (!CIBlockElement::Delete($elementId)) {
    echo 'Ошибка удаления элемента с ID ' . $elementId;
}

/***
 *** CIBlock
 ***/

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblock/getlist.php
 */
$arFilter = array(
	'TYPE' => 'catalog',
	'SITE_ID' => SITE_ID,
	'ACTIVE' => 'Y'
);
$res = CIBlock::GetList(array(), $arFilter, false);
while ($arFields = $res->GetNext()) {
	
}

/*
 * GetByID
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblock/getbyid.php
 */
$res = CIBlock::GetByID($iblockId);
if ($arFields = $res->GetNext()) {
	
}

/***
 *** CIBlockSection
 ***/

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/getlist.php
 */
$arFilter = array(
	'IBLOCK_ID' => $iblockId,
	'ACTIVE' => 'Y'
);
$res = CIBlockSection::GetList(array(), $arFilter, false);
while ($arFields = $res->GetNext()) {
	
}

/*
 * GetByID
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/getbyid.php
 */
$res = CIBlockSection::GetByID($sectionId);
if ($arFields = $res->GetNext()) {
	
}

/*
 * Add
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/add.php
 */
$res = new CIBlockSection;

$arFields = array(
  'MODIFIED_BY'    => $USER->GetID(),
  'IBLOCK_SECTION_ID' => false,
  'IBLOCK_ID'      => $iblockId,
  'PROPERTY_VALUES'=> array(),
  'NAME'           => $name,
  'ACTIVE'         => 'Y'
);

$sectionId = $res->Add($arFields);
if (!$sectionId = $res->Add($arFields)) {
    echo $res->LAST_ERROR;
}

/*
 * Update
http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/update.php
 */
$res = new CIBlockSection;

$arFields = array(
  'MODIFIED_BY'    => $USER->GetID(),
  'PROPERTY_VALUES'=> array(),
  'NAME'           => $name,
  'ACTIVE'         => 'Y'
);

if (!$res->Update($sectionId, $arFields)) {
    echo $res->LAST_ERROR;
}



/***************************************
 *********** ГЛАВНЫЙ МОДУЛЬ ************
 ***************************************/

CModule::IncludeModule('main');

/******
 ****** КЛАССЫ
 ******/

/***
 *** CFile
 ***/

/*
 * GetPath — путь к файлу
http://dev.1c-bitrix.ru/api_help/main/reference/cfile/getpath.php
 */
$res = CFile::GetPath($fileId);

/* ResizeImageGet — ресайз картинки
 * 1 — ID файла или массив, полученный через CFile::GetByID
 * 2 — массив с параметрами картинки: array('width', 'height')
 * [3] — resizeType
 * [4] — bInitSizes
 */

$arImage = CFile::ResizeImageGet($fileId, array('width' => 00, 'height' => 00));
$arImage['src'];

/***
 *** CUser
 ***/

/*
 * Login
http://dev.1c-bitrix.ru/api_help/main/reference/cuser/login.php
 * 1 — логин
 * 2 — пароль
 * [3, N] — сохранять ли в куки
 * [4, Y] — конвертировать ли пароль в MD5
 */
global $USER;
if (!is_object($USER)) $USER = new CUser;
$arAuthResult = $USER->Login('admin', '123456', 'Y');
$APPLICATION->arAuthResult = $arAuthResult;

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/main/reference/cuser/getlist.php
 */
$arFilter = array(
	
);
$res = CUser::GetList(($by = 'timestamp_x'), ($order = 'desc'), $arFilter, array());
while ($arFields = $res->GetNext()) {
	echo $arFields['NAME'] . ' ' . $arFields['LAST_NAME'] . '<br />';
}

/*
 * Update
http://dev.1c-bitrix.ru/api_help/main/reference/cuser/update.php
 */
$user = new CUser;
$arFields = Array(
  'NAME'              => 'Сергей',
  'LAST_NAME'         => 'Иванов',
  'EMAIL'             => 'ivanov@microsoft.com',
  'LOGIN'             => 'ivan',
  'LID'               => 'ru',
  'ACTIVE'            => 'Y',
  'GROUP_ID'          => array(2, 3),
  'PASSWORD'          => '123456',
  'CONFIRM_PASSWORD'  => '123456',
  );
$user->Update($id, $arFields);
$strError .= $user->LAST_ERROR;

/***
**** CSite
***/

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/main/reference/csite/getlist.php
 */
$res = CSite::GetList($by = 'sort', $order = 'desc', array('NAME' => ''));
while ($arSite = $rsSites->Fetch()) {
	
}

/***
**** CEvent
***/

/*
 * Send
http://dev.1c-bitrix.ru/api_help/main/reference/cevent/send.php
 */
$arMail = array(
	'DATE' => '',
	'MAIL' => '',
	'TEXT_MESSAGE' => ''
);
CEvent::Send('MAIL_TYPE', array('s1'), $arMail);




/***************************************
 ********** ТОРГОВЫЙ КАТАЛОГ ***********
 ***************************************/

CModule::IncludeModule('catalog');

/******
 ****** КЛАССЫ
 ******/

/***
 *** CPrice
 ***/

/*
 * GetBasePrice
http://dev.1c-bitrix.ru/api_help/catalog/classes/cprice/cprice__getbaseprice.9dc276c9.php
 */
$res = CPrice::GetBasePrice($priceId);
$res['PRICE'];


/***
 *** CCatalogProduct
 ***/

/*
 * GetByID
http://dev.1c-bitrix.ru/api_help/catalog/classes/ccatalogproduct/ccatalogproduct__getbyid.cc16046d.php
 */
$res = CCatalogProduct::GetByID($prodId);

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/catalog/classes/ccatalogproduct/ccatalogproduct__getlist.971a2b70.php
 */
$arFilter = array();
$arSelect = array();
$res = CCatalogProduct::GetList(array(), $arFilter, false, false, $arSelect);
while ($arFields = $res->GetNext()) {
	
}



/***************************************
 ********** ИНТЕРНЕТ-МАГАЗИН ***********
 ***************************************/

CModule::IncludeModule('sale');

/* ОФОРМЛЕНИЕ ЗАКАЗА */
CSaleOrder::Add
+
CSaleBasket::OrderBasket
+
CSaleOrderPropsValue::Add

/******
 ****** КЛАССЫ
 ******/


/***
 *** CSaleBasket
 ***/

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/sale/classes/csalebasket/csalebasket__getlist.4d82547a.php
 */
$res = CSaleBasket::GetList(
	array(),
	array(
		'FUSER_ID' => CSaleBasket::GetBasketUserID(),
		'LID' => SITE_ID,
		'ORDER_ID' => 'NULL'
	),
	false,
	false,
	array('*')
);

while ($arFields = $res->GetNext()) {
    
}

/*
 * Delete
http://dev.1c-bitrix.ru/api_help/sale/classes/csalebasket/csalebasket__delete.e0d06223.php
 */
CSaleBasket::Delete($basketId);



/***************************************
 ************** ПОДПИСКИ ***************
 ***************************************/

CModule::IncludeModule('subscribe');

/******
 ****** КЛАССЫ
 ******/

/***
 *** CSubscription
 ***/

/*
 * Add
http://dev.1c-bitrix.ru/api_help/subscribe/classes/csubscription/csubscriptionadd.php
 */
$res = new CSubscription;
$arFields = array(
	'USER_ID' => ($USER->IsAuthorized() ? $USER->GetID() : false),
	'EMAIL' => $_POST['email'],
	'ACTIVE' => 'Y',
	'RUB_ID' => array(),
	'SEND_CONFIRM' => 'N'
);
if  (!$res->Add($arFields)) {
	echo $res->LAST_ERROR;
}

/*
 * GetList
http://dev.1c-bitrix.ru/api_help/subscribe/classes/csubscription/csubscriptiongetlist.php
 */
$arFilter = array(
	'ID' => $subId,
	'USER_ID' => $USER->GetID(),
	'EMAIL' => ''
);
$res = CSubscription::GetList(array(), $arFilter);
if ($arFields = $res->GetNExt()) {
	
}

/*
 * GetRubricArray
http://dev.1c-bitrix.ru/api_help/subscribe/classes/csubscription/csubscriptiongetrubricarray.php
 */
$res = array(CSubscription::GetRubricArray($subId));
?>
