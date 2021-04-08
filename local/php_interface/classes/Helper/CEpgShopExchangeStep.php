<?php

namespace Helper;

use SimpleXMLElement;


/**
 * Class CEpgShopExchangeStep
 * @package Helper
 */
class CEpgShopExchangeStep
{
    /**
     * @param $IBLOCK_ID__CATALOG
     * @param $IBLOCK_ID__BRAND
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function importGoods($IBLOCK_ID__CATALOG, $IBLOCK_ID__BRAND, $step = 50, $delay = 2)
    {
        $fileContent = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/orders/in/goods.xml");
        $goodsArr = new SimpleXMLElement($fileContent);
        foreach ($goodsArr as $ar) {
            $goodsArray[] = $ar;
        }
        $count_goods = count($goodsArray);
        if ($_REQUEST["step"] == false) {
            $count_step = 0;
        } elseif ($_REQUEST["step"] > $count_goods) {
            echo "finish";
            die();
        } else {
            $count_step = $_REQUEST["step"];
        }
        $step_r = $count_step + $step;
        $arSlice = array_slice((array)$goodsArray, $count_step, $step);
        $curUser = 1;
        $page = $_SERVER['PHP_SELF'];
        $time = time();
        $count = 0;
        $count_update = 0;
        $count_add = 0;
        $count_update_err = 0;
        $count_add_err = 0;
        $arrForDeleteItems = array();
        foreach ($arSlice as $good) {
            $arrForDeleteItems[] = $good["КодТовара"];
            $arProduct = array(
                "MODIFIED_BY" => $curUser,
                "IBLOCK_ID" => $IBLOCK_ID__CATALOG,
                "ACTIVE" => "Y"
            );
            $arProduct["CODE"] = mb_strtolower(Translit::UrlTranslit((string)$good["НаименованиеТовара"]));
            $arProduct["NAME"] = $good["НаименованиеТовара"];
            $arProduct["XML_ID"] = $good["КодТовара"];
            if (strlen($good["ОписаниеHTML"]) > 0) {
                $arProduct["DETAIL_TEXT"] = strip_tags($good["ОписаниеHTML"], '<p><a>');
                $arProduct["DETAIL_TEXT_TYPE"] = 'html';
            }
            if (strlen($good["КодРодителя"]) > 0) {
                $arParents = explode(',', (string)$good["КодРодителя"]);
                //foreach ($arParents as $keyParent => $arParent){
                $resSections = \CIBlockSection::GetList(
                    array("ID" => "ASC"),
                    array("IBLOCK_ID" => $IBLOCK_ID__CATALOG, "ACTIVE" => "Y", "XML_ID" => $arParents),
                    false,
                    array('ID', 'NAME', 'CODE', 'XML_ID')
                );
                while ($ar_fields = $resSections->fetch()) {
                    $sectionParentID[] = $ar_fields['ID'];
                }
                //}
                $arProduct['IBLOCK_SECTION'] = $sectionParentID;
                unset($sectionParentID);
            }

            $measure = self::measure($good["ЕдиницаИзмерения"]);

            $arProductProps["MEAPACK"] = $good["ЕдиницаИзмеренияВУпаковке"];
            $arProductProps["ARTNUMBER"] = $good["АртикулТовара"];
            $arProductProps["VIDEO_URL"] = $good["videolink"];

            if ($good["ЦенаРаспродажи"] != "0,00") {
                $arProductProps["MINIMUM_PRICE"] = str_replace(',', '.', $good["ЦенаРаспродажи"]);
                $arProductProps["CURRENCY"] = $good["ВалютаЦеныРаспродажи"];
                $arProductProps["INSALDO"] = 21;
            } else {
                $arProductProps["MINIMUM_PRICE"] = str_replace(',', '.', $good["Цена"]);
                $arProductProps["CURRENCY"] = $good["ВалютаЦены"];
            }

            if (strlen($good["Новинки"]) > 0) {
                $new = (int)$good["Новинки"];
                $arProductProps["NEWPRODUCT"] = ($new == 1) ? 1 : 0;
            }
            if (strlen($good["ХитПродаж"]) > 0) {
                $hit = (int)$good["ХитПродаж"];
                $arProductProps["SALELEADER"] = ($hit == 1) ? 2 : 0;
            }
            if (strlen($good["Скидки"]) > 0) {
                $sale = (int)$good["Скидки"];
                $arProductProps["DISCOUNT"] = ($sale == 1) ? 3 : 0;
            }
            $arProduct["QUANTITY"] = 0;
            // Update product
            $goodID = \CIBlockElement::GetList(
                array(), array('XML_ID' => $arProduct["XML_ID"], 'IBLOCK_ID' => $IBLOCK_ID__CATALOG), false, false, array('ID', 'DETAIL_PICTURE')
            )->Fetch();

            if (!empty($goodID['ID'])) { // Если находим товар на сайте

                $element = new \CIBlockElement;

                if (strlen($good["ИмяОсновногоИзображения"]) > 0 & strlen($good["КаталогИзображений"]) > 0) {
                    $arProduct["DETAIL_PICTURE"] = self::imgProduct($good["ИмяОсновногоИзображения"], $good["КаталогИзображений"]);
                    $arProduct["PREVIEW_PICTURE"] = self::imgProduct($good["ИмяОсновногоИзображения"], $good["КаталогИзображений"]);
                }

                if (strlen((string)$good["КодБренда"]) > 0) {
                    $arProductProps['BRAND'] = self::brandID((string)$good["КодБренда"], $IBLOCK_ID__BRAND);
                }

                $res_upd = $element->Update($goodID['ID'], $arProduct); // Обновляем товар без дополнительных свойств
                \CIBlockElement::SetPropertyValuesEx($goodID['ID'], false, $arProductProps); // Обновляем дополнительные свойства товара
                \CCatalogProduct::Update($goodID['ID'], array("MEASURE" => $measure, "VAT_INCLUDED" => "Y")); // Можно также обнулять кол-во товара здесь quantity = 0

                $arPrice = array(
                    "PRODUCT_ID" => $goodID['ID'], // ID товара
                    "CATALOG_GROUP_ID" => 1, // Группа
                    "PRICE" => str_replace(",", '.', (string)$arProductProps["MINIMUM_PRICE"]), // Стоимость
                    "CURRENCY" => (string)$arProductProps["CURRENCY"], // Валюта
                    "QUANTITY_FROM" => false, // От
                    "QUANTITY_TO" => false // До
                );
                $prices = \CPrice::GetList(array(), array("PRODUCT_ID" => $goodID['ID'], "CATALOG_GROUP_ID" => 1));
                if ($the_price = $prices->Fetch()) {
                    \CPrice::Update($the_price["ID"], $arPrice); // Обновляем цену
                }

                unset($arPrice);

            } else { // Если это новый товар

                $element = new \CIBlockElement;

                if (strlen($good["ИмяОсновногоИзображения"]) > 0 & strlen($good["КаталогИзображений"]) > 0) {
                    $arProduct["DETAIL_PICTURE"] = self::imgProduct($good["ИмяОсновногоИзображения"], $good["КаталогИзображений"]);
                    $arProduct["PREVIEW_PICTURE"] = self::imgProduct($good["ИмяОсновногоИзображения"], $good["КаталогИзображений"]);
                }

                if (strlen((string)$good["КодБренда"]) > 0) {
                    $arProductProps['BRAND'] = self::brandID((string)$good["КодБренда"], $IBLOCK_ID__BRAND);
                }

                if (count($arProductProps) > 0) {
                    $arProduct['PROPERTY_VALUES'] = $arProductProps;
                }

                $res_new = $element->Add($arProduct);

                $productAdd = \Bitrix\Catalog\Model\Product::add(array("ID" => $res_new, "MEASURE" => $measure, "VAT_INCLUDED" => "Y"));

                $arFields = array(
                    "PRODUCT_ID" => $productAdd->getId(),
                    "CATALOG_GROUP_ID" => 1,
                    "PRICE" => str_replace(",", '.', (string)$arProductProps["MINIMUM_PRICE"]),
                    "CURRENCY" => (string)$arProductProps["CURRENCY"],
                );
                $result = \Bitrix\Catalog\Model\Price::add($arFields, true);

                unset($arFields);

            }

            $count++;

            if ($res_upd == true) {
                $count_update++;
            } elseif ($res_upd == false) {
                $count_update_err++;
            }

            if ($res_new == true) {
                $count_add++;
            } elseif ($res_new == false) {
                $count_add_err++;
            }

            unset($arProduct);
            unset($arProductProps);
        }

        header("Refresh: $delay; url=$page?step=$step_r");

        echo "Общее кол-во товаров: " . $count_goods;
        echo "<br>";
        echo "Текущий шаг с: " . $count_step;
        echo "<br>";
        echo "Текущий шаг по: " . ($count_step + $step);
        echo "<br>";
        echo "Кол-во товаров обработанных за шаг: " . count($arSlice);
        echo "<br>";
        echo "<br>";
        echo 'Добавлено товаров: ' . $count_add . ' элементов<br>';
        echo 'Обновлено товаров: ' . $count_update . ' элементов<br>';
        //echo 'Удалено товаров: ' . $count_del . ' элементов<br>';
        echo "Затрачено памяти: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB<br>";
        echo "Затрачено времени: " . ((time() - $time)) . " сек";
    }

    /**
     * @param $value
     * @return string
     */
    function measure($value)
    {
        if (strlen($value) > 0) {
            $arItemProps["EDINICY"] = $value;
            if ($arItemProps["EDINICY"] == "шт.") {
                $measure = "5";
            } elseif ($arItemProps["EDINICY"] == "л") {
                $measure = "2";
            } elseif ($arItemProps["EDINICY"] == "уп.") {
                $measure = "7";
            } elseif ($arItemProps["EDINICY"] == "комп.") {
                $measure = "6";
            } else {
                $measure = "5";
            }
            return $measure;
        }
    }

    /**
     * @param $name
     * @param $catalog
     * @return mixed
     */
    function imgProduct($name, $catalog)
    {
        $catPic = str_replace("\\", "/", $catalog);
        $imgProduct = \CFile::MakeFileArray("https://epgshop.ru/upload/orders/pictures/" . $catPic . "/" . $name);
        return $imgProduct;
    }

    /**
     * @param $value
     * @param $IBLOCK_ID__BRAND
     * @return array|bool|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function brandID($value, $IBLOCK_ID__BRAND)
    {
        $brandID = \CIBlockElement::GetList(array(), array('XML_ID' => (string)$value, 'IBLOCK_ID' => $IBLOCK_ID__BRAND), false, false, array('ID', 'NAME', 'XML_ID'))->Fetch();
        if ($brandID['ID'] > 0) {
            return $brandID;
        }
    }
}