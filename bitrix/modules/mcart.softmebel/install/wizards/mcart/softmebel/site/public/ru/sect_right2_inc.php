
<p align="center"><font color="#ff0000"><b><font size="5"><a ><font size="4">  </font></a></font></b></font></p>
 
<p align="center"><?$APPLICATION->IncludeComponent("bitrix:catalog.top", "template1", Array(
	"IBLOCK_TYPE" => "catalog",	
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",	
	"ELEMENT_SORT_FIELD" => "sort",	// По какому полю сортируем элементы
	"ELEMENT_SORT_ORDER" => "asc",	// Порядок сортировки элементов
	"SECTION_URL" => "",	// URL, ведущий на страницу с содержимым раздела
	"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
	"BASKET_URL" => "/personal/basket.php",	// URL, ведущий на страницу с корзиной покупателя
	"ACTION_VARIABLE" => "action",	// Название переменной, в которой передается действие
	"PRODUCT_ID_VARIABLE" => "id",	// Название переменной, в которой передается код товара для покупки
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// Название переменной, в которой передается количество товара
	"PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// Название переменной, в которой передается код группы
	"DISPLAY_COMPARE" => "N",	// Выводить кнопку сравнения
	"ELEMENT_COUNT" => "2",	// Количество выводимых элементов
	"LINE_ELEMENT_COUNT" => "1",	// Количество элементов выводимых в одной строке таблицы
	"PROPERTY_CODE" => "",	// Свойства
	"OFFERS_LIMIT" => "5",	// Максимальное количество предложений для показа (0 - все)
	"PRICE_CODE" => "",	// Тип цены
	"USE_PRICE_COUNT" => "N",	// Использовать вывод цен с диапазонами
	"SHOW_PRICE_COUNT" => "1",	// Выводить цены для количества
	"PRICE_VAT_INCLUDE" => "Y",	// Включать НДС в цену
	"PRODUCT_PROPERTIES" => "",	// Характеристики товара
	"USE_PRODUCT_QUANTITY" => "N",	// Разрешить указание количества товара
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"CACHE_NOTES" => "",
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"CONVERT_CURRENCY" => "N",	// Показывать цены в одной валюте
	),
	false
);?>
<div align="center"> <font size="1">Фабрика &quot;Мягкая мебель&quot; занимается производством и продажей мягкой мебели с 1993 года!</font></div>
