<?
IncludeModuleLangFile(__FILE__);

/**
 * Класс настроек обновления инфоблоков
 */
class CTopsale_general
{

    protected static $arProperty = false;

    /**
     * Поля таблицы
     */
    protected static $arFieldsKey = array(
        'ID' => true,
        'IBLOCK_ID' => true,
        'FIELDS' => true,
        'VALUE' => true,
        'TRIGGER' => true,
    );

    /**
     * Поля обязательные для заполнения
     */
    protected static $arRequiredFieldsKey = array(
        'IBLOCK_ID' => true,
    );

    /**
     * Последние возникшие ошибки
     */
    public static $LAST_ERROR = '';

    function CTopsale_general()
    {
    }

    /**
     * Функция возвращает массив всех обновляемых полей инфоблоков
     *
     * @return Array
     */
    static function GetListArray()
    {
        self::$arProperty = array();
        $res = CTopsale::GetList();
        if (strlen(self::$LAST_ERROR) == 0) {
            while ($arRes = $res->Fetch()) {
                self::$arProperty[$arRes['ID']] = $arRes;
            }
        }
        return self::$arProperty;
    }

    /**
     * Агент обновляет сортировку всех товаров
     *
     * @return String
     */
    static function AgentRefresh()
    {
        $arIBlockFields = array();
        $arFieldsValueLimit = array();
        $arPropertyUpdate = self::GetListArray();
        $time = time();

        //проверяем какие поля и инфоблоки устарели и чистим от них
        if (!empty($arPropertyUpdate) && CModule::IncludeModule('iblock')) {
            $arIBlockList = array();
            $rsIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'));
            while ($arIBlock = $rsIBlock->GetNext()) {
                $arIBlockList[$arIBlock['ID']] = $arIBlock;
            }
            $arPropertyList = array();
            $rsProperty = CIBlockProperty::GetList();
            while ($arProperty = $rsProperty->GetNext()) {
                $arPropertyList[$arProperty['IBLOCK_ID']][$arProperty['ID']] = $arProperty;
            }

            foreach ($arPropertyUpdate as $key => $arProperty) {
                if (empty($arIBlockList[$arProperty['IBLOCK_ID']])) {
                    continue;
                }
                if (!empty($arProperty['FIELDS'])
                    && empty($arPropertyList[$arProperty['IBLOCK_ID']][$arProperty['FIELDS']])) {
                    continue;
                }
                $arIBlockFields[$arProperty['IBLOCK_ID']][] = $arProperty['FIELDS'];
                if (!empty($arProperty['VALUE']) && $arProperty['TRIGGER'] > 0) {
                    $arFieldsValueLimit[$arProperty['IBLOCK_ID']][$arProperty['FIELDS']][$arProperty['TRIGGER']] = $arProperty;
                }
            }
        }
        if (!empty($arFieldsValueLimit)) {
            foreach ($arFieldsValueLimit[$iblockID] as $key => $val) {
                ksort($arFieldsValueLimit[$iblockID][$key]);
            }
        }

        //если есть что обновлять, обрабатываем корзину
        if (!empty($arIBlockFields) && CModule::IncludeModule('sale')) {
            $order = COption::GetOptionString("tatonimus.topsale", "order");
            $period = intval(COption::GetOptionString("tatonimus.topsale", "period"));
            $levelCount = intval(COption::GetOptionString("tatonimus.topsale", "level_count"));
            if ($levelCount <= 0) {
                $levelCount = 5;
            }

            //Выбираем реальное количество продаж товаров
            $arProductSort = array();
            $arSaleCounts = array();
            $arFilter = array();
            if (empty($order)) {
                $arFilter['>ORDER_ID'] = 0;
            }
            if ($period > 0) {
                $arFilter['>=DATE_UPDATE'] = date('d.m.Y', strtotime('-' . $period . 'day'));
            }

            $rsSaleCount = CSaleBasket::GetList(
                array(),
                $arFilter,
                array('PRODUCT_ID'),
                false,
                array('PRODUCT_ID')
            );
            while($arSaleCount = $rsSaleCount->Fetch()) {
                $arSaleCounts[ceil($arSaleCount['CNT'] / $levelCount)][] = $arSaleCount['PRODUCT_ID'];
            }
            krsort($arSaleCounts);
            foreach ($arSaleCounts as $arSaleCount) {
                rsort($arSaleCount);
                $arProductSort = array_merge($arProductSort, $arSaleCount);
            }
            $arProductSort = array_flip($arProductSort);

            global $USER;
            $userClear = false;
            if (empty($USER)) {
                $USER = new CUser();
                $userClear = true;
            }
            //выбираем инфоблок и обновляем рейтинги
            foreach ($arIBlockFields as $iblockID => $arFields) {
                if ($time && time() - $time > 20) {
                    set_time_limit(0);
                    $time = false;
                }
                $arSelect = array('ID');
                foreach ($arFields as $fields) {
                    if ($fields == 0) {
                        $arSelect[] = 'SORT';
                    } else {
                        $arSelect[] = 'PROPERTY_' . $fields;
                    }
                }
                $rsElement = CIBlockElement::GetList(
                    array(),
                    array('IBLOCK_ID' => $iblockID),
                    false,
                    false,
                    $arSelect
                );
                $maxIndex = $rsElement->SelectedRowsCount();
                if ($maxIndex > 0) {
                    $maxIndex = pow(10, strlen($maxIndex));
                    $cElement = new CIBlockElement;
                    while ($arElement = $rsElement->Fetch()) {
                        $arUpdateFields = array();
                        $indexVal = isset($arProductSort[$arElement['ID']]) ? intval($arProductSort[$arElement['ID']]) + 1 : $maxIndex;
                        foreach ($arFields as $fields) {
                            $index = $indexVal;
                            $isInt = true;
                            if (!empty($arFieldsValueLimit[$iblockID][$fields])) {
                                $index = null;
                                foreach ($arFieldsValueLimit[$iblockID][$fields] as $val) {
                                    if ($indexVal <= $val['TRIGGER']) {
                                        $index = $val['VALUE'];
                                        break;
                                    }
                                }
                                $isInt = false;
                            }

                            if ($fields == 0) {
                                if (intval($arElement['SORT']) != $index) {
                                    $cElement->Update($arElement['ID'], array('SORT' => $index));
                                }
                            } else {
                                if (isset($arElement['PROPERTY_' . $fields . '_ENUM_ID'])) {
                                    if (intval($arElement['PROPERTY_' . $fields . '_ENUM_ID']) != intval($index)) {
                                        $arUpdateFields[$fields] = $index;
                                    }
                                } elseif ($isInt) {
                                    if (intval($arElement['PROPERTY_' . $fields . '_ENUM_ID']) != $index) {
                                        $arUpdateFields[$fields] = $index;
                                    }
                                } elseif (strval($arElement['PROPERTY_' . $fields . '_VALUE']) != $index) {
                                    $arUpdateFields[$fields] = $index;
                                }
                            }
                        }
                        if (!empty($arUpdateFields)) {
                            CIBlockElement::SetPropertyValuesEx($arElement['ID'], $iblockID, $arUpdateFields);
                        }
                    }
                }
            }
            if ($userClear) {
                unset($USER);
            }
        }
        return 'CTopsale::AgentRefresh();';
    }

}

?>