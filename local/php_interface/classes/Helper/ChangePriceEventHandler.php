<?php

// namespace Itmit\Events\Handlers;

use Bitrix\Main\Loader;

/**
 * Представляет механизм обработки событий в результате, которых изменяется цена продукта.
 */
class ChangePriceEventHandler
{
    /**
     * Массив идентификаторов товаров, для изменения минимальной цены товара после удаления скидки.
     *
     * @var array
     */
    private static $_prodsIdsAfterDiscountDelete = [];

    private static $_minPricePropertyCode = "MIN_PRICE_DISCOUNT";

    /**
     * Инициализирует объект класса ChangePriceEventHandler.
     */
    public function __construct()
    {
        Loader::includeModule('catalog');
    }

    /**
     * Обрабатывает событие на изменение, добавление или удаление скидки.
     *
     * @param mixed $id
     * @param mixed $arFields
     * @return void
     */
    public function ChangeDiscountExecute($id, $arFields): void
    {
        $id = (int)$id;
        if ($id < 1) {
            throw new InvalidArgumentException("Ид товара не может быть меньше 1");
        }

        // При удалении скидки.
        if (empty($arFields["PRODUCT_IDS"]) && !empty($id)) {

            // Запомним ид товаров из акции.
            $resDiscount = CCatalogDiscount::GetList([], ["ID" => $id]);
            while ($obDiscount = $resDiscount->Fetch()) {
                self::$_prodsIdsAfterDiscountDelete[] = $obDiscount["PRODUCT_ID"];
            }
        }

        if (!empty($arFields["PRODUCT_IDS"])) {
            foreach ($arFields["PRODUCT_IDS"] as $prod_id) {

                // Нужно для сортировки цены со скидкой.
                $arPrice = CCatalogProduct::GetOptimalPrice($prod_id, 1, [2], "N", [], "s1");
                CIBlockElement::SetPropertyValueCode($prod_id, self::$_minPricePropertyCode, $arPrice["DISCOUNT_PRICE"]);
            }
        }
    }

    /**
     * Обрабатывает событие на добавление или обновление цены.
     *
     * @param mixed $id
     * @param mixed $arFields
     * @return void
     */
    public function ChangePriceExecute($id, $arFields): void
    {
        // Нужно для сортировки цены со скидкой.
        $arPrice = CCatalogProduct::GetOptimalPrice($arFields["PRODUCT_ID"], 1, [2], "N", [], "s1");
        CIBlockElement::SetPropertyValueCode($arFields["PRODUCT_ID"], self::$_minPricePropertyCode, $arPrice["DISCOUNT_PRICE"]);
    }

    /**
     * Обрабатывает событие после удаления скидки.
     *
     * @param mixed $id
     * @return void
     */
    public function AfterDiscountDeleteExecute($id): void
    {
        $id = (int)$id;
        if ($id < 1) {
            throw new InvalidArgumentException("Ид товара не может быть меньше 1");
        }
        if (count(self::$_prodsIdsAfterDiscountDelete) > 0) {
            foreach (self::$_prodsIdsAfterDiscountDelete as $prod_id) {
                $arPrice = CCatalogProduct::GetOptimalPrice($prod_id, 1, [2], "N", [], "s1");
                CIBlockElement::SetPropertyValueCode($prod_id, self::$_minPricePropertyCode, $arPrice["DISCOUNT_PRICE"]);
            }
        }
    }
}
