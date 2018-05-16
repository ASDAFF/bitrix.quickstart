<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.05.2018
 * Time: 22:10
 */

$module_id = 'my_company_code.my_module_id';

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/options.php');

$showRightsTab = true;
$arSel = array('REFERENCE_ID' => array(1, 3, 5, 7), 'REFERENCE' => array('Значение 1', 'Значение 2', 'Значение 3', 'Значение 4'));

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => 'Настройки',
        'ICON' => '',
        'TITLE' => 'Настройки'
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => 'Имя группы', 'TAB' => 0)
);

$arOptions = array(
    'TEST_0' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Строка',
        'TYPE' => 'STRING',
        'DEFAULT' => 'Значение по-умолчанию',
        'SORT' => '0',
        'NOTES' => 'Это подсказка к полю "Строка".'
    ),
    'TEST_1' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Число',
        'TYPE' => 'INT',
        'DEFAULT' => '0',
        'SORT' => '1',
        'REFRESH' => 'Y',
        'NOTES' => 'Это подсказка к полю "Число". У данного поля установлен параметр REFRESH = "Y"'
    ),
    'TEST_2' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Текст',
        'TYPE' => 'TEXT',
        'DEFAULT' => '',
        'SORT' => '2',
        'COLS' => 40,
        'ROWS' => 15,
        'NOTES' => 'Это подсказка к полю "Текст". У данного поля установлен параметр COLS = "40", ROWS = "15"'
    ),
    'TEST_2' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Текст',
        'TYPE' => 'TEXT',
        'DEFAULT' => '',
        'SORT' => '2',
        'COLS' => 40,
        'ROWS' => 15,
        'NOTES' => 'Это подсказка к полю "Текст". У данного поля установлен параметр COLS = "40", ROWS = "15"'
    ),
    'TEST_3' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Флажок',
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y',
        'SORT' => '3'
    ),
    'TEST_4' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Список',
        'TYPE' => 'SELECT',
        'VALUES' => $arSel,
        'SORT' => '4'
    ),
    'TEST_5' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Список с множественным выбором',
        'TYPE' => 'MSELECT',
        'VALUES' => $arSel,
        'SORT' => '5'
    ),
    'TEST_6' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Файл',
        'TYPE' => 'FILE',
        'BUTTON_TEXT' => 'Выбери-ка файл',
        'SORT' => '6',
        'NOTES' => 'Это поле "Файл".'
    ),
    'TEST_7' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Выбор цвета',
        'TYPE' => 'COLORPICKER',
        'SORT' => '7'
    ),
    'TEST_8' => array(
        'GROUP' => 'MAIN',
        'TITLE' => '',
        'TYPE' => 'CUSTOM',
        'VALUE' => '<span>Это текст в параметре <b>VALUE</b></span>',
        'SORT' => '8',
        'NOTES' => 'Настраиваемое поле без параметра TITLE'
    )
);

/*
Конструктор класса CModuleOptions
$module_id - ID модуля
$arTabs - массив вкладок с параметрами
$arGroups - массив групп параметров
$arOptions - собственно сам массив, содержащий параметры
$showRightsTab - определяет надо ли показывать вкладку с настройками прав доступа к модулю ( true / false )
*/

$opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
$opt->ShowHTML();
