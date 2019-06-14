<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$MESS["IBLOCK_TYPE_TIP"]     = "Из выпадающего списка выбирается один из созданных в системе типов инфоблоков. После нажатия кнопки <b><i>ок</b></i> будут подгружены инфоблоки, созданные для выбранного типа.";
$MESS["IBLOCK_ID_TIP"]       = "Выбирается один из инфоблоков установленного типа. Если пункт (другое)->, то необходимо указать ID инфоблока в поле рядом.";
$MESS["FILTER_NAME_TIP"]     = "Задается имя переменной, в которую передается массив параметров из фильтра. Служит для определения выходящих из фильтра элементов. Поле может быть оставлено пустым, тогда используется значение по умолчанию.";
$MESS["FIELD_CODE_TIP"]      = "Среди перечисленных полей можно выбрать дополнительные поля для фильтра.";
$MESS["PROPERTY_CODE_TIP"]   = "Среди свойств инфоблока можно выбрать те, которые будут отображены при показе в качестве полей фильтра. При выборе пункта (не выбрано)->  и без указания кодов свойств в строках ниже, свойства выведены не будут.";
$MESS["PRICE_CODE_TIP"]      = "Устанавливается, какой из типов цен будут выведен для элементов. Если ни один из типов не выбран, то цена и кнопки <i>Купить</i> и <i>В корзину</i> не будет показаны.";
$MESS["CACHE_TYPE_TIP"]      = "<i>Авто</i>: действует при включенном кешировании в течение заданного времени;<br /><i>Кешировать</i>: для кеширования необходимо определить только время кеширования;<br /><i>Не кешировать</i>: кеширования нет в любом случае.";
$MESS["CACHE_TIME_TIP"]      = "Поле служит для указания времени кеширования в секундах.";
$MESS["SAVE_IN_SESSION_TIP"] = "При отмеченной опции будут сохраняться установки фильтра в сессии пользователя.";
$MESS["TEXT_WIDTH_TIP"]      = "Только число";
$MESS["NUMBER_WIDTH_TIP"]    = "Только число";
$MESS["LIST_HEIGHT_TIP"]     = "Только число";
$MESS["SELECT_WIDTH_TIP"]    = "Только число";
$MESS["ELEMENT_IN_ROW_TIP"]  = "Только число";
$MESS["NAME_WIDTH_TIP"]      = "Только число";
$MESS["SECTION_ID_TIP"]      = 'Для комплексного компонента каталога впишите сюда: ={$arResult["VARIABLES"]["SECTION_ID"]}';
$MESS["SECTION_CODE_TIP"]    = 'Для комплексного компонента каталога впишите сюда: ={$arResult["VARIABLES"]["SECTION_CODE"]}';
$MESS["SECTION_CODE_TIP"]    = 'Для комплексного компонента каталога впишите сюда: ={$arResult["VARIABLES"]["SECTION_CODE"]}';

$MESS["CHECK_ACTIVE_SECTIONS_TIP"] = 'Будет учитываться активность разделов каталога при поиске значений полей интервалов';
$MESS["SECTIONS_DEPTH_LEVEL_TIP"] = 'Через запятую и без пробелов:<br>1,2,3,4,5 и т.д';
$MESS["INCLUDE_JQUERY_UI_SLIDER_TIP"] = 'Внимание! Без  jQuery UI не будут работать';



$MESS["INCLUDE_CHOSEN_PLUGIN_TIP"]          = 'jQuery Chosen v1.2.0<br>(c) 2011-2013 by Harvest<br>MIT License<br>https://github.com/harvesthq/chosen/blob/master/LICENSE.md';
$MESS["INCLUDE_FORMSTYLER_PLUGIN_TIP"]      = 'jQuery Form Styler v1.4.8.3<br>(c) Dimox<br>MIT License<br>https://github.com/Dimox/jQueryFormStyler';
$MESS["INCLUDE_AUTOCOMPLETE_PLUGIN_TIP"]    = 'jQuery Ajax Autocomplete v1.2.9<br>(c) 2013 Tomas Kirda<br>MIT License<br>https://github.com/devbridge/jQuery-Autocomplete';
$MESS["INCLUDE_PLACEHOLDER_TIP"]            = 'jQuery HTML5 Placeholder v2.0.7<br>(c) @mathias<br>MIT License<br>http://mths.be/placeholder';