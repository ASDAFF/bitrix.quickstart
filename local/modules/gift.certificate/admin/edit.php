<?
/** @global CMain $APPLICATION */

use Bitrix\Main;
use Bitrix\Sale\Internals,
    Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Catalog\CatalogIblockTable;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

// Получаем константы
include(str_replace('/admin', '', dirname(__FILE__)) . '/prolog.php');
define('MODULE_PATH', strtolower(str_replace('.', '_', ADMIN_MODULE_ID)));

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/prolog.php');

Main\Loader::includeModule('sale');
Main\Loader::includeModule('catalog');

$errors = array();

$iTableID     = Option::get(ADMIN_MODULE_ID, 'GiftCertificateTableID');
$iEventTypeID = Option::get(ADMIN_MODULE_ID, 'GiftCertificateEventTypeID');
$iBlockID     = Option::get(ADMIN_MODULE_ID, 'GiftCertificateIBlockID');


$request = Application::getInstance()->getContext()->getRequest();

$hlblock           = Bitrix\Highloadblock\HighloadBlockTable::getById($iTableID)->fetch();
$entity            = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$arFields = array();

if ($request->get("ID")) {
    $arFields['ID'] = (int)$request->get("ID");
    if ($arFields['ID'] < 0) {
        $arFields['ID'] = 0;
    }
}

if (
    check_bitrix_sessid()
    && $request->getRequestMethod() == 'POST'
) {

    ;
    $arFields['UF_PRODUCT_ID']      = $request->getPost("UF_PRODUCT_ID");
    $arFields['UF_DAYS']            = $request->getPost("UF_DAYS");
    $arFields['UF_MAILTEMPLATE_ID'] = $request->getPost("UF_MAILTEMPLATE_ID");
    $arFields['UF_DISCOUNT_ID']     = $request->getPost("UF_DISCOUNT_ID");
    $arFields['UF_PRICE']           = $request->getPost("UF_PRICE");

    if ($arFields['ID'] == 0) {
        $result = $entity_data_class::add($arFields);
    } else {

        $ID = $arFields['ID'];
        unset($arFields['ID']);

        $result = $entity_data_class::update($ID, $arFields);

    }

    if ( ! $result->isSuccess()) {
        $errors[] = $result->getErrors();
    }

    if (empty($errors)) {
        	if (empty($_POST['apply']))
        		LocalRedirect("/bitrix/admin/".MODULE_PATH.".php");
        	else
        		LocalRedirect("/bitrix/admin/".MODULE_PATH."_edit.php?ID=" . $ID);
    }
}


if ( ! empty($errors)) {
    $errorMessage = new CAdminMessage(
        array(
            "DETAILS" => implode('<br>', $errors),
            "TYPE"    => "ERROR",
            "MESSAGE" => GetMessage(ADMIN_MODULE_LANG . "EDIT_MESS_SAVE_ERROR"),
            "HTML"    => true,
        )
    );
    echo $errorMessage->Show();
    unset($errorMessage);
}


if ($arFields['ID'] > 0) {

    $row      = $entity_data_class::getRowById($arFields['ID']);
    $arFields = array_merge($arFields, $row);
}

$tabList = array(
    array(
        'DIV'   => 'edit1',
        'ICON'  => 'sale',
        'TAB'   => GetMessage(ADMIN_MODULE_LANG . "EDIT_TAB_NAME_COMMON"),
        'TITLE' => GetMessage(ADMIN_MODULE_LANG . "EDIT_TAB_TITLE"),
    ),
);

$couponFormID = 'giftCertificateControl';
$control      = new CAdminForm($couponFormID, $tabList);
$couponFormID .= '_form';

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


$control->BeginEpilogContent();
echo bitrix_sessid_post();
if ($arFields['ID'] > 0) {
    ?> <input type="hidden" name="ID" value="<? echo $arFields['ID']; ?>"> <?
}
$control->EndEpilogContent();

$contextMenuItems = array(
    array(
        'ICON' => 'btn_list',
        'TEXT' => GetMessage(ADMIN_MODULE_LANG . "BTN_LIST"),
        'LINK' => MODULE_PATH . '.php',
    ),
);


$contextMenu = new CAdminContextMenu($contextMenuItems);
$contextMenu->Show();
unset($contextMenu, $contextMenuItems);

$APPLICATION->SetTitle(GetMessage(ADMIN_MODULE_LANG . "PAGE_TITLE"));

$control->Begin(array(
    'FORM_ACTION' => MODULE_PATH . '_edit.php?lang=' . LANGUAGE_ID,
));

$control->BeginNextFormTab();

$arProducts = \Bitrix\Iblock\ElementTable::getList(array(
    'select' => array(
        'NAME',
        'ID',
    ),
    'filter' => array(
        'IBLOCK_ID' => $iBlockID,
        'ACTIVE'    => 'Y',
    ),
));


$arProductList = array();
while ($item = $arProducts->fetch()) {
    $item['ID']                 = (int)$item['ID'];
    $item['NAME']               = (string)$item['NAME'];
    $arProductList[$item['ID']] = '[' . $item['ID'] . ']' . ($item['NAME'] !== '' ? ' ' . $item['NAME'] : '');
}

$control->AddDropDownField(
    'UF_PRODUCT_ID',
    GetMessage(ADMIN_MODULE_LANG . "PRODUCT_TITLE"),
    true,
    $arProductList,
    $arFields['UF_PRODUCT_ID']
);


$discountList     = array();
$discountIterator = Internals\DiscountTable::getList(array(
    'select' => array('ID', 'NAME'),
    'filter' => array('=ACTIVE' => 'Y'),
    'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
));
while ($discount = $discountIterator->fetch()) {
    $discount['ID']                = (int)$discount['ID'];
    $discount['NAME']              = (string)$discount['NAME'];
    $discountList[$discount['ID']] = '[' . $discount['ID'] . ']' . ($discount['NAME'] !== '' ? ' ' . $discount['NAME'] : '');
}
unset($discount, $discountIterator);

$control->AddDropDownField(
    'UF_DISCOUNT_ID',
    GetMessage(ADMIN_MODULE_LANG . "DISCOUNT_TITLE"),
    true,
    $discountList,
    $arFields['UF_DISCOUNT_ID']
);


$arEventList = array();
$dbEvent     = CEventMessage::GetList($b = "ID", $order = "ASC", Array("EVENT_NAME" => "ITSFERA_GIFT_CERTIFICATE"));
while ($arEvent = $dbEvent->Fetch()) {
    $arEvent['ID']               = (int)$arEvent['ID'];
    $arEvent['SUBJECT']          = (string)$arEvent['SUBJECT'];
    $arEventList[$arEvent['ID']] = '[' . $arEvent['ID'] . ']' . ($arEvent['SUBJECT'] !== '' ? ' ' . $arEvent['SUBJECT'] : '');

}
unset($arEvent, $dbEvent);

$control->AddDropDownField(
    'UF_MAILTEMPLATE_ID',
    GetMessage("GIFT_CERTIFICATE_MAILTEMPLATE_TITLE"),
    true,
    $arEventList,
    $arFields['UF_MAILTEMPLATE_ID']
);


$control->AddEditField(
    'UF_PRICE',
    GetMessage("GIFT_CERTIFICATE_PRICE"),
    false,
    array('id' => 'UF_PRICE'),
    ($arFields['UF_PRICE'] > 0 ? $arFields['UF_PRICE'] : '')
);


$control->AddEditField(
    'UF_DAYS',
    GetMessage("GIFT_CERTIFICATE_DAYS_ACTIVE_TITLE"),
    false,
    array('id' => 'UF_DAYS'),
    ($arFields['UF_DAYS'] > 0 ? $arFields['UF_DAYS'] : '')
);


$control->Buttons(
    array(
        'disabled' => $readOnly,
        'back_url' => MODULE_PATH . ".php",
    )
);
$control->Show();
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");