<?php
/**
 * Individ module
 *
 * @category    Individ
 * @link        http://individ.ru
 * @revision    $Revision$
 * @date        $Date$

 */

namespace Indi\Main;


class ListConfig
{
    /**
     * @var array набор колонок для списка элементов + параметры сортировки
     */
    private static $pageElementColOptionFull = array(
        'default' => array(
            'name' => array(
                'NAME' => 'Наименование',//Просто наименование колонки
                'SORT' => 'NAME'//Поле по которому будем сортировать, если false то не сортируем
            ),
            'active_from' => array('NAME' => 'Дата', 'SORT' => 'ACTIVE_FROM'),
            'col1' => array('NAME' => 'Колонка 1', 'SORT' => 'PROPERTY_COL1'),
            'col2' => array('NAME' => 'Колонка 2', 'SORT' => 'PROPERTY_COL2'),
            'col3' => array('NAME' => 'Колонка 3', 'SORT' => false),
        ),
        'newstable' => array(
            'NAME' => array(
                'NAME' => 'Наименование',//Просто наименование колонки
                'SORT' => 'NAME'//Поле по которому будем сортировать, если false то не сортируем
            ),
            'ACTIVE_FROM' => array(
                'NAME' => 'Дата',
                'SORT' => 'ACTIVE_FROM'
            ),
            'PREVIEW_TEXT' => array(
                'NAME' => 'Описание',
                'SORT' => 'PREVIEW_TEXT'
            ),
        ),
    );

    /**Сортирвоки
     * @var array
     */
    private static $pageElementSortOptionFull = array(
        'orders' => array(
            'FIELD' => 'DATE_INSERT',
            'ORDER' => 'DESC',
        ),
        'documents' => array(
            'FIELD' => 'DATE_ACTIVE_FROM',
            'ORDER' => 'DESC',
        ),
        'newstable' => array(
            'FIELD' => 'ACTIVE_FROM',
            'ORDER' => 'ASC',
        )
    );

    /**
     * @var array количество элементов на странице
     */
    private static $pageElementCountOption = array(
        '1',
        '2',
        '3',
        '50',
        '100',
        '200',
    );


    /**
     * Формируем массив части настроек для вызова компонента списка, для настройки списка колонок, сортировки, количества элементов на странице.
     *
     * @param string $name название функционала где выводим поля
     *
     * @return array
     */
    public static function get($name = 'default', $arSelect = array(), $arSort = array())
    {
        if (count($arSelect) > 0) {
            self::$pageElementColOptionFull = $arSelect;
        }

        if (count($arSort) > 0) {
            self::$pageElementSortOptionFull = $arSort;
        }

        if (!self::$pageElementColOptionFull[$name]) {
            return array();
        }

//Колонки
        $arResult['PAGE_ELEMENT_COL_OPTION_FULL'] = self::$pageElementColOptionFull[$name];
        $pageElementColOption = $arResult['PAGE_ELEMENT_COL_OPTION_FULL'];
        foreach ($pageElementColOption as $key => $option) {
            if (!$_COOKIE['PAGE_ELEMENT_COL_OPTION'][$name][$key]) {
                unset($pageElementColOption[$key]);
            }
        }
        $arResult['PAGE_ELEMENT_COL_OPTION'] = $pageElementColOption ? $pageElementColOption : self::$pageElementColOptionFull[$name];

//Сортировка
        $sortDef = self::$pageElementSortOptionFull[$key]
            ? self::$pageElementSortOptionFull[$key]
            : array('FIELD' => 'SORT', 'ORDER' => 'ASC');

//		$arResult['PAGE_ELEMENT_SORT'] = $_COOKIE['PAGE_ELEMENT_SORT'][$name] ? $_COOKIE['PAGE_ELEMENT_SORT'][$name] : array('FIELD' => 'NAME', 'ORDER' => 'ASC');
        $arResult['PAGE_ELEMENT_SORT'] =  $_COOKIE['PAGE_ELEMENT_SORT'][$name] ? $_COOKIE['PAGE_ELEMENT_SORT'][$name] : array('FIELD' => 'NAME', 'ORDER' => 'ASC');

//Количество элементов на странице
        $arResult['PAGE_ELEMENT_COUNT_OPTION'] = self::$pageElementCountOption;
        if ($_COOKIE['PAGE_ELEMENT_COUNT'][$name] && in_array($_COOKIE['PAGE_ELEMENT_COUNT'][$name], self::$pageElementCountOption)) {
            $arResult['PAGE_ELEMENT_COUNT'] = $_COOKIE['PAGE_ELEMENT_COUNT'][$name];
        } else {
            $arResult['PAGE_ELEMENT_COUNT'] = self::$pageElementCountOption[0];
        }

        $arResult['PAGE_ELEMENT_NAME'] = $name;

        return $arResult;
    }
}