<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29.05.2018
 * Time: 13:52
 */

namespace Helper;

class CouponConditions
{

    /**
     * составляем описания условия применения купона
     * @param array $coupon
     *
     * @return bool
     */
    public static function toString(array $coupon)
    {

        if (!isset($coupon['MODULE_ID'])) {
            return false;
        }

        $arMessage = [];

        // пока только скидки корзины
        if ($coupon['MODULE_ID'] == 'sale') {

            $arDiscount = \CSaleDiscount::GetByID($coupon['DISCOUNT_ID']);

            $arControls = \CSaleCondCtrlBasketGroup::GetControls();

            $arConditions = unserialize($arDiscount['CONDITIONS']);

            foreach ($arConditions['CHILDREN'] as $arAtomCond) {

                switch ($arAtomCond['CLASS_ID']) {

                    //пока обработка только 'Общая стоимость товаров'
                    case 'CondBsktAmtGroup':
                        $str = '';
                        $str .= $arControls[$arAtomCond['CLASS_ID']]['LABEL'];
                        $str .= ' ';
                        $str .= $arControls[$arAtomCond['CLASS_ID']]['ATOMS']['Logic']['values'][$arAtomCond['DATA']['logic']];
                        $str .= ' ';
                        $str .= $arAtomCond['DATA']['Value'];
                        $str .= ' руб.';
                        $arMessage[] = $str;
                        break;
                }


            }
        }
        return $arMessage;
    }
}