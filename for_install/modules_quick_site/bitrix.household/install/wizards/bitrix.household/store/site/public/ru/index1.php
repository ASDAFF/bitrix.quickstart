<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Интернет-магазин \"Бытовая техника\"");
?>

                    <div class="banner">
                    	<table class="grad" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td class="image">
									<div class="relative">
										<div>
										<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/banner.php"), false);?>
										</div>
									</div>
								</td>
								<td class="action">
									<?$APPLICATION->IncludeComponent("bitrix:news.list", "action", Array(
									"IBLOCK_TYPE" => "-",	// Тип информационного блока (используется только для проверки)
									"IBLOCK_ID" => "#ACTION_IBLOCK_ID#",	// Код информационного блока
									"NEWS_COUNT" => "2",	// Количество новостей на странице
									"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
									"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
									"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
									"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
									"FILTER_NAME" => "",	// Фильтр
									"FIELD_CODE" => array(	// Поля
										0 => "",
										1 => "",
									),
									"PROPERTY_CODE" => array(	// Свойства
										0 => "",
										1 => "DIRECTION",
										2 => "",
									),
									"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
									"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
									"AJAX_MODE" => "N",	// Включить режим AJAX
									"AJAX_OPTION_SHADOW" => "Y",	// Включить затенение
									"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
									"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
									"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
									"CACHE_TYPE" => "A",	// Тип кеширования
									"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
									"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
									"CACHE_GROUPS" => "Y",	// Учитывать права доступа
									"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
									"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
									"SET_TITLE" => "N",	// Устанавливать заголовок страницы
									"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
									"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
									"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
									"PARENT_SECTION" => "",	// ID раздела
									"PARENT_SECTION_CODE" => "",	// Код раздела
									"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
									"DISPLAY_BOTTOM_PAGER" => "N",	// Выводить под списком
									"PAGER_TITLE" => "",	// Название категорий
									"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
									"PAGER_TEMPLATE" => "",	// Название шаблона
									"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
									"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
									"DISPLAY_DATE" => "N",	// Выводить дату элемента
									"DISPLAY_NAME" => "Y",	// Выводить название элемента
									"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
									"DISPLAY_PREVIEW_TEXT" => "N",	// Выводить текст анонса
									"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
									),
									false
								);?>
								</td>
							</tr>
						</table>
                    </div>
                    <div class="group">
                    	<div class="title">
                    		<div class="title_main">
								<div class="title_body">
									<a href="<?=SITE_DIR?>bestprice.php"><h2>Лучшая цена</h2></a>
								</div>
							</div>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "template1", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => array(
		0 => "#REFRIGERATORS_IBLOCK_ID#",
		1 => "#WASHING_IBLOCK_ID#",
		2 => "#STOVES_IBLOCK_ID#",
		3 => "#APPLIANCE_IBLOCK_ID#",
		4 => "#HOME_IBLOCK_ID#",
		5 => "#BUILTIN_IBLOCK_ID#",
		6 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "3",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
	),
	"FLAG_PROPERTY_CODE" => "BESTPRICE",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?> 
	            	</div>
				    <div class="group">
                    	<div class="title">
                    		<div class="title_main">
								<div class="title_body">
									<a href="<?=SITE_DIR?>novelty.php"><h2>Новинки</h2></a>
								</div>
							</div>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "template1", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => array(
		0 => "#REFRIGERATORS_IBLOCK_ID#",
		1 => "#WASHING_IBLOCK_ID#",
		2 => "#STOVES_IBLOCK_ID#",
		3 => "#APPLIANCE_IBLOCK_ID#",
		4 => "#HOME_IBLOCK_ID#",
		5 => "#BUILTIN_IBLOCK_ID#",
		6 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "3",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
	),
	"FLAG_PROPERTY_CODE" => "NOVELTY",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?> 
	            	</div>
				    <div class="group">
                    	<div class="title">
                    		<div class="title_main">
								<div class="title_body">
									<a href="<?=SITE_DIR?>hit.php"><h2>Хит</h2></a>
								</div>
							</div>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "template1", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => array(
		0 => "#REFRIGERATORS_IBLOCK_ID#",
		1 => "#WASHING_IBLOCK_ID#",
		2 => "#STOVES_IBLOCK_ID#",
		3 => "#APPLIANCE_IBLOCK_ID#",
		4 => "#HOME_IBLOCK_ID#",
		5 => "#BUILTIN_IBLOCK_ID#",
		6 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "6",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
	),
	"FLAG_PROPERTY_CODE" => "HIT",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?> 
	            	</div>
				</div>
			</td>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>