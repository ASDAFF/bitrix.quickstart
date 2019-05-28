<?php

namespace OrderCoupon;

use Bitrix\Sale;
use Bitrix\Seo\Engine\Bitrix;
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Currency;
use Bitrix\Sale\Discount\Index;
use Bitrix\Sale\Internals;
use \Bitrix\Sale\Internals\DiscountCouponTable;


require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/general/discount.php');


class Event extends \CAllSaleDiscount
{

    function OnSaleOrderSavedHandler ($ID, $val)
    {

        //запускаем если статус - "Формируется к отправке"
        if ($val == 'P') {

            //собираем правила
            $iTableID = \COption::GetOptionString('order.coupon', 'OrderCouponTableID');

            $hlblock           = \Bitrix\Highloadblock\HighloadBlockTable::getById($iTableID)->fetch();
            $entity_data_class = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();
            $rsData            = $entity_data_class::getList(array(
                "select" => array('*'),
                "filter" => array(),
                "order"  => array("ID" => "DESC"),
            ));

            //собираем заказ
            $arOrder       = \CSaleOrder::GetByID($ID);
            $dbBasketItems = \CSaleBasket::GetList(
                array(),
                array(
                    "ORDER_ID" => $ID,
                )
            );

            while ($arItems = $dbBasketItems->Fetch()) {
                $arOrder['BASKET_ITEMS'][] = $arItems;
            }


            $arList = array();
            while ($item = $rsData->fetch()) {
                $arList[] = $item;

                $arCond = base64_decode($item['UF_CONDITIONS']);

                $arCond = unserialize($arCond);

                $conditionData = array(
                    'HANDLERS'       => array(),
                    'ENTITY'         => array(),
                    'EXECUTE_MODULE' => array(),
                );

                $Unpack = '';

                self::prepareDiscountConditions(
                    $arCond,
                    $Unpack,
                    $conditionData,
                    self::PREPARE_CONDITIONS,
                    'el');

                $isCheck = self::__Unpack($arOrder, $Unpack);

                if ($isCheck) {

                    $activeFrom = new \Bitrix\Main\Type\DateTime();
                    $activeTo   = new \Bitrix\Main\Type\DateTime();
                    $activeTo   = $activeTo->add($item['UF_DAYS'].' days');

                    $coupon = DiscountCouponTable::generateCoupon(true);

                    $addDb = DiscountCouponTable::add(array(
                        'DISCOUNT_ID' => $item['UF_DISCOUNT_ID'],
                        'ACTIVE'      => 'Y',
                        'COUPON'      => $coupon,
                        'TYPE'        => DiscountCouponTable::TYPE_ONE_ORDER,
                        'MAX_USE'     => 1,
                        'ACTIVE_FROM' => $activeFrom,
                        'ACTIVE_TO'   => $activeTo,
                        'USER_ID'     => $arOrder['USER_ID'],
                        'DESCRIPTION' => 'Купон по скидке №' . $item['UF_DISCOUNT_ID'],
                    ));

                    if ($addDb->isSuccess()) {

                        $arUser = \CUser::GetByID($arOrder['USER_ID'])->fetch();

                        $arEventFields = array(
                            "COUPON"    => $coupon,
                            "ACTIVE_TO" => $activeTo->format("H:i d.m.Y"),
                            "EMAIL"     => $arUser['EMAIL'],
                            "USER_ID"   => $arOrder['USER_ID'],
                        );

                        \CEvent::Send("ITSFERA_ORDER_COUPON", 'el', $arEventFields, 'Y', $item['UF_MAILTEMPLATE_ID']);


                    } else {

                        \CEventLog::Add(array(
                            "SEVERITY"      => "ERROR",
                            "AUDIT_TYPE_ID" => "DEBUG",
                            "MODULE_ID"     => "order.coupon",
                            "ITEM_ID"       => 123,
                            "DESCRIPTION"   => "Ошибка при создании купона: скидка " . $item['UF_DISCOUNT_ID'],
                        ));
                    }

                }


            }

        }
    }

}