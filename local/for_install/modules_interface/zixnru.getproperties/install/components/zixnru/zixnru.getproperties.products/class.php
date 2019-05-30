<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */

class GetPropertiesProducts extends CBitrixComponent {
    /*
     * Получает свойства элементов 
     */

    public function getProps($arParams, $element_id) {
        $arListProps = $this->getPropertiesTypeList($arParams, $element_id);

        //$arResultProps=  array_merge($arListProps);
        $arResultProps = self::multisort($arListProps, 'NAME');
        return $arResultProps;
    }

    /**
     * Извлечение свойств типа список
     * @param array $arParams массив параметров, где IBLOCK_ID обязательные значения
     * @return string
     */
    protected function getPropertiesTypeList($arParams, $element_id) {
        if (empty($arParams['IBLOCK_ID']) OR empty($element_id)) {
            return array();
        }
        if (!CModule::IncludeModule('iblock')) {
            return false;
        }
        $iblock_id = $arParams['IBLOCK_ID']; //ИД инфоблока
        if (!empty($arParams['EXCLUDE_PROPS'])) {
            $exclude_id = explode(',', $arParams['EXCLUDE_PROPS']); //Массив исключений по ID свойства
        } else {
            $exclude_id = array();
        }

        $cdbResult = \CIBlockElement::GetProperty($iblock_id, $element_id, array(), array(
                    'ACTIVE' => 'Y',
                    'PROPERTY_TYPE' => 'L',
                    'EMPTY' => 'N',
        ));
        while ($arPropsEl = $cdbResult->Fetch()) {
            if (array_search($arPropsEl['ID'], $exclude_id) === FALSE) {
                $arProps[] = $arPropsEl;
            }
        }


        if (!empty($arProps)) {

            return $arProps;
        } else {
            return array();
        }
    }

    /**
     * Сортирует многомерный массив по ключу
     * @param array $array Массив для сортировки
     * @param string $index Ключ для по которму будет сортироваться
     * @return array Отсортированный массив
     */
    protected static function multisort($array, $index) {
        if (empty($array)) {
            return array();
        }

        foreach ($array as $key => $value) {

            $el_arr = $value[$index];
            $new_arr[] = $el_arr;
        }
        asort($new_arr);
        $keys = array_keys($new_arr);
        for ($key = 0, $count = count($keys); $key < $count; $key++)
            $result[] = $array[$keys[$key]];
        return $result;
    }

}
