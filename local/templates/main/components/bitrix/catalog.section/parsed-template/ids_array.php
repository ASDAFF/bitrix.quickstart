<?
/**
 * Copyright (c) 19/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

	//Массив идентификаторов элемента, необходим для работы JavaScript объектов
	$arItemIDs = array(
		'ID' => $strMainID,		//ID элемента
		'PICT' => $strMainID.'_pict',	//ID основной картинки
		'SECOND_PICT' => $strMainID.'_secondpict',	//ID дополнительной картинки (меняется при наведелнии на элемент в адаптивном шаблоне магазина)

		'QUANTITY' => $strMainID.'_quantity',	//ID поля "Количесвто"
		'QUANTITY_DOWN' => $strMainID.'_quant_down',	//ID кнопки - (уменьшить количество)
		'QUANTITY_UP' => $strMainID.'_quant_up',	//ID кнопки + (увеличить количество)
		'QUANTITY_MEASURE' => $strMainID.'_quant_measure',	//ID еденицы измерения
		'BUY_LINK' => $strMainID.'_buy_link',	//ID кнопки "Купить" (необязательно ссылка, это может быть любой другой элемент, событие onClick отработает и так)
		'SUBSCRIBE_LINK' => $strMainID.'_subscribe',	//ID енопки "Подписаться"

		'PRICE' => $strMainID.'_price',	//ID поля "Цена"
		'DSC_PERC' => $strMainID.'_dsc_perc',	//ID цены со  скидкой
		'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',	//ID второй цены со скидкой или старой цены, толком не разобрался

		'PROP_DIV' => $strMainID.'_sku_tree',	//ID контейнера содержащего "дерево" свойств элемента
		'PROP' => $strMainID.'_prop_',	//Идентификатор конкретного свойства
		'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',	//ID контейнера отображающего свойства элемента из DISPLAY_PROPERTIES
		'BASKET_PROP_DIV' => $strMainID.'_basket_prop',	//ID контейнера содержащего свойства элемента передаваемые в корзину покупателя
	);

	$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

	$strTitle = (
		isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && '' != isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
		? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
		: $arItem['NAME']
	);
	?>