<?php

namespace GiftCertificate;

use Bitrix\Sale;
use Bitrix\Seo\Engine\Bitrix;
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Currency;
use Bitrix\Sale\Discount\Index;
use Bitrix\Sale\Internals;
use \Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Main\Config\Option;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/general/discount.php');


class Event extends \CAllSaleDiscount
{

    function OnSaleOrderSavedHandler ($ID, $val)
    {

        //запускаем если статус - "Формируется к отправке"
        if ($val == 'P') {

            //собираем правила
            $iTableID = Option::get('gift.certificate', 'GiftCertificateTableID');
            $iBlockID = Option::get('gift.certificate', 'GiftCertificateIBlockID');

            $hlblock           = \Bitrix\Highloadblock\HighloadBlockTable::getById($iTableID)->fetch();
            $entity_data_class = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();


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

                $IBlockProduct = (int)\CIBlockElement::GetIBlockByID($arItems['PRODUCT_ID']);

                if ($iBlockID == $IBlockProduct) {

                    $rsData = $entity_data_class::getList(array(
                        "select" => array('*'),
                        "filter" => array('=UF_PRODUCT_ID' => $arItems['PRODUCT_ID']),
                        "order"  => array("ID" => "DESC"),
                    ));

                    while ($item = $rsData->fetch()) {

                        $activeFrom = new \Bitrix\Main\Type\DateTime();
                        $activeTo   = new \Bitrix\Main\Type\DateTime();
                        $activeTo   = $activeTo->add($item['UF_DAYS'] . ' days');

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
                            'DESCRIPTION' => 'Сертификат по скидке №' . $item['UF_DISCOUNT_ID'],
                        ));

                        if ($addDb->isSuccess()) {

                            $arUser = \CUser::GetByID($arOrder['USER_ID'])->fetch();

                            $arEventFields = array(
                                "COUPON"    => $coupon,
                                "ACTIVE_TO" => $activeTo->format("H:i d.m.Y"),
                                "EMAIL"     => $arUser['EMAIL'],
                                "USER_ID"   => $arOrder['USER_ID'],
                            );

                            \CEvent::Send("ITSFERA_GIFT_CERTIFICATE", 'el', $arEventFields, 'Y',
                                $item['UF_MAILTEMPLATE_ID']);


                        } else {
                            \CEventLog::Add(array(
                                "SEVERITY"      => "ERROR",
                                "AUDIT_TYPE_ID" => "DEBUG",
                                "MODULE_ID"     => "gift.sertificate",
                                "ITEM_ID"       => 123,
                                "DESCRIPTION"   => "Ошибка при создании сертификата: скидка " . $item['UF_DISCOUNT_ID'],
                            ));
                        }

                    }

                }

            }
        }
    }

}