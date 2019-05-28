<?
/** @global CMain $APPLICATION */

use Bitrix\Main;
use Bitrix\Sale\Internals,
    Bitrix\Main\Application;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/prolog.php');
Main\Loader::includeModule('sale');
Main\Loader::includeModule('catalog');

$errors = array();

$iTableID     = COption::GetOptionString('order.coupon', 'OrderCouponTableID');
$iEventTypeID = COption::GetOptionString('order.coupon', 'OrderCouponEventTypeID');

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

    $arFields['UF_DAYS']            = $request->getPost("UF_DAYS");
    $arFields['UF_MAILTEMPLATE_ID'] = $request->getPost("UF_MAILTEMPLATE_ID");
    $arFields['UF_DISCOUNT_ID']     = $request->getPost("UF_DISCOUNT_ID");

    $obCond3    = new CSaleCondTree();
    $boolCond   = $obCond3->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_SALE, array(
        'INIT_CONTROLS' => array(
            'SITE_ID'  => SITE_ID,
            'CURRENCY' => CSaleLang::GetLangCurrency(SITE_ID),
        ),
    ));
    $CONDITIONS = $obCond3->Parse();

    $arFields['UF_CONDITIONS'] = base64_encode(serialize($CONDITIONS));

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
        		LocalRedirect("/bitrix/admin/order_coupon.php");
        	else
        		LocalRedirect("/bitrix/admin/order_coupon_edit.php?ID=" . $ID);
    }
}



if ( ! empty($errors)) {
    $errorMessage = new CAdminMessage(
        array(
            "DETAILS" => implode('<br>', $errors),
            "TYPE"    => "ERROR",
            "MESSAGE" => GetMessage("ITSFERA_ORDERCOUPON_EDIT_MESS_SAVE_ERROR"),
            "HTML"    => true,
        )
    );
    echo $errorMessage->Show();
    unset($errorMessage);
}


if ($arFields['ID'] > 0) {

    $row      = $entity_data_class::getRowById($arFields['ID']);
    $arFields = array_merge($arFields, $row);

    $arFields['UF_CONDITIONS'] = base64_decode($row['UF_CONDITIONS']);

}

$tabList = array(
    array(
        'DIV'   => 'edit1',
        'ICON'  => 'sale',
        'TAB'   => GetMessage("ITSFERA_ORDERCOUPON_EDIT_TAB_NAME_COMMON"),
        'TITLE' => GetMessage("ITSFERA_ORDERCOUPON_EDIT_TAB_TITLE"),
    ),
);

$couponFormID = 'orderCouponControl';
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
        'TEXT' => GetMessage("ITSFERA_ORDERCOUPON_BTN_LIST"),
        'LINK' => 'order_coupon.php',
    ),
);


$contextMenu = new CAdminContextMenu($contextMenuItems);
$contextMenu->Show();
unset($contextMenu, $contextMenuItems);

$APPLICATION->SetTitle(GetMessage("ITSFERA_ORDERCOUPON_PAGE_TITLE"));

$control->Begin(array(
    'FORM_ACTION' => 'order_coupon_edit.php?lang=' . LANGUAGE_ID,
));

$control->BeginNextFormTab();

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
if ( ! empty($discountList)) {
    $control->AddDropDownField(
        'UF_DISCOUNT_ID',
        GetMessage("ITSFERA_ORDERCOUPON_DISCOUNT_TITLE"),
        true,
        $discountList,
        $arFields['UF_DISCOUNT_ID']
    );
}


$arEventList = array();
$dbEvent     = CEventMessage::GetList($b = "ID", $order = "ASC", Array("EVENT_NAME" => "ITSFERA_ORDER_COUPON"));
while ($arEvent = $dbEvent->Fetch()) {
    $arEvent['ID']               = (int)$arEvent['ID'];
    $arEvent['SUBJECT']          = (string)$arEvent['SUBJECT'];
    $arEventList[$arEvent['ID']] = '[' . $arEvent['ID'] . ']' . ($arEvent['SUBJECT'] !== '' ? ' ' . $arEvent['SUBJECT'] : '');

}
unset($arEvent, $dbEvent);
if ( ! empty($arEventList)) {
    $control->AddDropDownField(
        'UF_MAILTEMPLATE_ID',
        GetMessage("ITSFERA_ORDERCOUPON_MAILTEMPLATE_TITLE"),
        true,
        $arEventList,
        $arFields['UF_MAILTEMPLATE_ID']
    );
}


$control->AddEditField(
    'UF_DAYS',
    GetMessage("ITSFERA_ORDERCOUPON_DAYS_ACTIVE_TITLE"),
    false,
    array('id' => 'UF_DAYS'),
    ($arFields['UF_DAYS'] > 0 ? $arFields['UF_DAYS'] : '')
);


$control->AddSection("BT_SALE_DISCOUNT_SECT_COND", GetMessage("ITSFERA_ORDERCOUPON_SECT_COND"));

$control->BeginCustomField("CONDITIONS", GetMessage("ITSFERA_ORDERCOUPON_SECT_COND") . ":", false);
?>
    <tr id="tr_CONDITIONS">
    <td valign="top" colspan="2">
        <div id="tree" style="position: relative; z-index: 1;"></div><?

        if ( ! is_array($arFields['UF_CONDITIONS'])) {
            if (CheckSerializedData($arFields['UF_CONDITIONS'])) {
                $arFields['UF_CONDITIONS'] = unserialize($arFields['UF_CONDITIONS']);
            } else {
                $arFields['UF_CONDITIONS'] = '';
            }
        }

        //  dm($arFields['UF_CONDITIONS']);

        $arCondParams = array(
            'FORM_NAME'     => $couponFormID,
            'CONT_ID'       => 'tree',
            'JS_NAME'       => 'JSSaleCond',
            'INIT_CONTROLS' => array(
                'SITE_ID'  => SITE_ID,
                'CURRENCY' => CSaleLang::GetLangCurrency(SITE_ID),
            ),
        );

        $obCond   = new CSaleCondTree();
        $boolCond = $obCond->Init(BT_COND_MODE_DEFAULT, BT_COND_BUILD_SALE, $arCondParams);
        if ( ! $boolCond) {
            if ($ex = $APPLICATION->GetException()) {
                echo $ex->GetString() . "<br>";
            }
        } else {
            $obCond->Show($arFields['UF_CONDITIONS']);
        }

        ?></td>
    </tr><?
$control->EndCustomField('CONDITIONS',
    '<input type="hidden" name="CONDITIONS" value="' . htmlspecialcharsbx($strCond) . '">' .
    '<input type="hidden" name="CONDITIONS_CHECK" value="' . htmlspecialcharsbx(md5($strCond)) . '">'
);


$control->Buttons(
    array(
        'disabled' => $readOnly,
        'back_url' => "order_coupon.php",
    )
);
$control->Show();
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");