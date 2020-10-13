<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.05.2018
 * Time: 13:27
 */
namespace Helper;

use Bitrix\Main\Config\Option;

class itsferaEvents
{
    static private $pId = false;
    protected static $productPrice = null;


    /**формирует массив с данными для скидки на конкретный товар
     *
     * @param $percent - процент скидки
     *
     * @return array
     */
    public static function getDiscountArray($percent){

        $arDiscount = array(
            'ID' => 169,    //тут должен быть ID существующей скидки, иначе не применятся купоны,
            'TYPE'            => 0,
            'SITE_ID'         => 'el',
            'ACTIVE'          => 'Y',
            'ACTIVE_FROM'     => null,
            'ACTIVE_TO'       => null,
            'RENEWAL'         => 'N',
            'NAME'            => 'Скидка '.$percent.'% на товар ' . self::$pId,
            'SORT'            => '100',
            'MAX_DISCOUNT'    => '0.0000',
            'VALUE_TYPE'      => 'P',
            'VALUE'           => $percent . '.0000',
            'CURRENCY'        => 'RUB',
            'PRIORITY'        => '1',
            'LAST_DISCOUNT'   => 'N',
            'COUPON'          => '',
            'COUPON_ONE_TIME' => null,
            'COUPON_ACTIVE'   => '',
            'UNPACK'          => '((((isset($arProduct[\'PARENT_ID\']) ? ((isset($arProduct[\'ID\']) && (($arProduct[\'ID\'] == ' . self::$pId . '))) || ($arProduct[\'PARENT_ID\'] == ' . self::$pId . ')) : (isset($arProduct[\'ID\']) && (($arProduct[\'ID\'] == ' . self::$pId . ')))))))',
            'CONDITIONS'      => serialize(array(
                'ACTIVE'     => 'Y',
                "CONDITIONS" => array(
                    "CLASS_ID" => "CondGroup",
                    "DATA"     => array(
                        "All"  => "AND",
                        "True" => "True",
                    ),
                    "CHILDREN" => array(
                        array(
                            "CLASS_ID" => "CondIBElement",
                            "DATA"     => array(
                                "logic" => "Equal",
                                "value" => self::$pId,
                            ),
                        ),
                    ),
                ),
            )),
            'HANDLERS'        =>
                array(
                    'MODULES'   =>
                        array(),
                    'EXT_FILES' =>
                        array(),
                ),
            'MODULE_ID'       => 'catalog',
        );

        return $arDiscount;
    }

    public function OnGetDiscountHandler($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS){
        self::$pId = $intProductID;
        return true;
    }
    public function OnGetDiscountResultHandler(&$arResult)
    {

        if (CModule::IncludeModule("gift.certificate")) {
            //собираем правила
            $iBlockID = Option::get('gift.certificate', 'GiftCertificateIBlockID');
            if ((int)\CIBlockElement::GetIBlockByID(self::$pId) == $iBlockID ) {
                return;
            }
        }


        GLOBAL $USER;
        if ($USER->IsAuthorized()) {
            CModule::IncludeModule("iblock");

            //получаем данные по дисконтным картам
            $arSelect    = Array("ID", "NAME", "PROPERTY_PERCENT", "PROPERTY_CARDTYPE", "PROPERTY_TOTAL");
            $arFilter    = Array(
                "IBLOCK_ID"         => getIBlockIdByCode("discount_cards"),
                "PROPERTY_USER_ID"  => cuser::getid(),
                "PROPERTY_CARDTYPE" => [317085, 317086],
            );
            $resDiscount = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);


            $arSelect = Array(
                "ID",
                "IBLOCK_ID",
                "CATALOG_GROUP_1",
                "PROPERTY_SAYT_AKTSIONNYY_TOVAR",
                "PROPERTY_OLD_PRICE_1",
            );
            $arFilter = Array("ID" => self::$pId, "ACTIVE" => "Y");
            $res      = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();

                //$actionDiscount - скидка по разнице между старой и новой ценой
                if (($arFields["PROPERTY_SAYT_AKTSIONNYY_TOVAR_VALUE"] == 'Да') &&
                    ($arFields['PROPERTY_OLD_PRICE_1_VALUE'])) {

                    $actionDiscount = round(($arFields['PROPERTY_OLD_PRICE_1_VALUE'] - $arFields['CATALOG_PRICE_1']) / ($arFields['PROPERTY_OLD_PRICE_1_VALUE'] / 100));
                } else {
                    $actionDiscount = 0;
                }

                $db_props      = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'],
                    array("sort" => "asc"), Array("CODE" => "SAYTBEZSKIDKI"));
                $SAYTBEZSKIDKI = $db_props->Fetch();
                if ($SAYTBEZSKIDKI['VALUE_XML_ID'] != 'true') {

                    if ($obDiscount = $resDiscount->GetNextElement()) {
                        $arFieldsDiscount = $obDiscount->GetFields();

                        // если привязана дисконт карта подменим все скидки на товар на найденную скидку по карте
                        if ($arFieldsDiscount['PROPERTY_CARDTYPE_VALUE'] == 'Накопительная') {

                            $rsRanges                           = CCatalogDiscountSave::GetRangeByDiscount(
                                array('RANGE_FROM' => 'DESC'),
                                array('DISCOUNT_ID' => 168, '<=RANGE_FROM' => $arFieldsDiscount['PROPERTY_TOTAL_VALUE']),
                                false,
                                array('nTopCount' => 1)
                            );
                            $arRange                            = $rsRanges->Fetch();
                            $arFieldsDiscount['PROPERTY_PERCENT_VALUE'] = $arRange['VALUE'];
                        }

                        //установим большую скидку
                        if ($actionDiscount < (int)$arFieldsDiscount['PROPERTY_PERCENT_VALUE']) {
                            $actionDiscount = $arFieldsDiscount['PROPERTY_PERCENT_VALUE'];
                        }
                    }
                }

                //https://itscp.ru/company/personal/user/507/tasks/task/view/3502/
                //если СайтБезСкидки = Да - не не делается скидка по дисконтной карте! А по акции на такие товары скидка может быть и даже будет!
                if ($actionDiscount && $arFields["PROPERTY_SAYT_AKTSIONNYY_TOVAR_VALUE"] != 'Да') { //добавил $arFields["PROPERTY_SAYT_AKTSIONNYY_TOVAR_VALUE"] != 'Да'
                    $arResult[] = self::getDiscountArray($actionDiscount);
                }
				
            }
        }

    }
}