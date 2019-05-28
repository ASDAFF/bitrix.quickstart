<?php
set_time_limit(0);
ob_implicit_flush(1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Sale\Internals;

global $APPLICATION;

CModule::IncludeModule("main");


/* Костыль - 2 правила подряд не ставится. выносим правило для редактирования сюда*/
CUrlRewriter::Add(array(
    "CONDITION" => "#^/bitrix/admin/order_coupon_edit.php#",
    "PATH" => '/local/modules/order.coupon/admin/edit.php',
    "ID" => 'local:order.coupon',
));
/* закостылили */

$arError = [];

$APPLICATION->SetTitle("Купон за заказ");

require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/lang/ru/admin/event_log.php");


$sTableID = "tbl_ordercoupon";

$iTableID = COption::GetOptionString('order.coupon', 'OrderCouponTableID');

$hlblock           = \Bitrix\Highloadblock\HighloadBlockTable::getById($iTableID)->fetch();
$entity            = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$entity_table_name = $hlblock['TABLE_NAME'];
$sTableID          = 'tbl_' . $entity_table_name;

$oSort    = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin   = new CAdminList($sTableID, $oSort);

if ( $listID = $lAdmin->GroupAction()) {
    $listID = array_filter($listID);
    if ( ! empty($listID)) {
        switch ($_REQUEST['action']) {
// на вырост
//            case 'activate':
//            case 'deactivate':
            case 'delete':

                foreach ($listID as &$itemID) {
                    $result = $entity_data_class::delete($itemID);
                    if ( ! $result->isSuccess()) {
                        $arError[] = "Ошибка удаления записи";
                    }
                    unset($result);
                }
                unset($itemID);
                break;
        }
    }

   $APPLICATION->RestartBuffer();

    if (!empty($arError)) {
        $lAdmin->AddGroupError(implode('<br>',$arError));
    }
}



$rsData            = $entity_data_class::getList(array(
    "select" => array('*'),
    "filter" => array(),
    "order"  => array("ID" => "DESC"),
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Скидки"));

$lAdmin->AddHeaders(array(
    array(
        "id"      => "ID",
        "content" => "ID",
        "sort"    => "id",
        "default" => true,
    ),
    array(
        "id"      => "UF_DISCOUNT_ID",
        "content" => "Скидка",
        "sort"    => "name",
        "default" => true,
    ),
    array(
        "id"      => "UF_MAILTEMPLATE_ID",
        "content" => "Шаблон письма",
        "sort"    => "lid",
        "default" => true,
    ),
    array(
        "id"      => "UF_DAYS",
        "content" => "Купон активен (дней)",
        "sort"    => "sort",
        "align"   => "right",
        "default" => true,
    ),

));

//получаем все скидки
$discountList     = array();
$discountIterator = Internals\DiscountTable::getList(array(
    'select' => array('ID', 'NAME'),
    'filter' => array('=ACTIVE' => 'Y'),
    'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
));
while ($discount = $discountIterator->fetch()) {
    $discount['ID']                = (int)$discount['ID'];
    $discount['NAME']              = (string)$discount['NAME'];
    $discountList[$discount['ID']] = '[<a href="/bitrix/admin/sale_discount_edit.php?ID=' . $discount['ID'] . '&lang=ru">' . $discount['ID'] . '</a>]' . ($discount['NAME'] !== '' ? ' ' . $discount['NAME'] : '');
}

//получаем все шаблоны
$arEventList = array();
$dbEvent     = CEventMessage::GetList($b = "ID", $order = "ASC", Array("EVENT_NAME" => "ITSFERA_ORDER_COUPON"));
while ($arEvent = $dbEvent->Fetch()) {
    $arEvent['ID']               = (int)$arEvent['ID'];
    $arEvent['SUBJECT']          = (string)$arEvent['SUBJECT'];
    $arEventList[$arEvent['ID']] = '[<a href="/bitrix/admin/message_edit.php?lang=ru&ID=' . $arEvent['ID'] . '&lang=ru">' . $arEvent['ID'] . '</a>]' . ($arEvent['SUBJECT'] !== '' ? ' ' . $arEvent['SUBJECT'] : '');

}

while ($arRes = $rsData->NavNext(true, "f_")) {

    $row =& $lAdmin->AddRow($f_ID, $arRes);

    $row->AddField("UF_DISCOUNT_ID", $discountList[$arRes['UF_DISCOUNT_ID']]);
    $row->AddField("UF_MAILTEMPLATE_ID", $arEventList[$arRes['UF_MAILTEMPLATE_ID']]);

    $arActions = Array(
        array(
            "ICON"    => "edit",
            "DEFAULT" => true,
            "TEXT"    => "Изменить",
            "ACTION"  => $lAdmin->ActionRedirect("order_coupon_edit.php?ID=" . $f_ID),
        ),
        array(
            "ICON"   => "delete",
            "TEXT"   => "Удалить",
            "ACTION" => "if(confirm('Удалить?')) " . $lAdmin->ActionDoGroup($f_ID, "delete"),
        ),
    );
    $row->AddActions($arActions);

}

$lAdmin->AddAdminContextMenu(
    array(
        array(
            "TEXT"  => "Добавить",
            "LINK"  => "order_coupon_edit.php?lang=" . LANG,
            "TITLE" => "Создать",
            "ICON"  => "btn_new",
        ),
    )
);


$lAdmin->DisplayList();


?>


<?
if (!$lAdmin->GroupAction()) {
    require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
}
?>