<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Интернет-магазин \"Бытовая техника\"");
?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="50%" align="left">
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "slides", Array(
						"DISPLAY_DATE" => "N",	// Выводить дату элемента
						"DISPLAY_NAME" => "Y",	// Выводить название элемента
						"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
						"DISPLAY_PREVIEW_TEXT" => "N",	// Выводить текст анонса
						"AJAX_MODE" => "N",	// Включить режим AJAX
						"IBLOCK_TYPE" => "services",	// Тип информационного блока (используется только для проверки)
						"IBLOCK_ID" => "#slides_main_IBLOCK_ID#",	// Код информационного блока
						"NEWS_COUNT" => "5",	// Количество новостей на странице
						"SORT_BY1" => "SORT",	// Поле для первой сортировки новостей
						"SORT_ORDER1" => "ASC",	// Направление для первой сортировки новостей
						"SORT_BY2" => "ID",	// Поле для второй сортировки новостей
						"SORT_ORDER2" => "DESC",	// Направление для второй сортировки новостей
						"FILTER_NAME" => "",	// Фильтр
						"FIELD_CODE" => "",	// Поля
						"PROPERTY_CODE" => array(	// Свойства
							0 => "LINK",
						),
						"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
						"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
						"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
						"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
						"SET_TITLE" => "N",	// Устанавливать заголовок страницы
						"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
						"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
						"PARENT_SECTION" => "",	// ID раздела
						"PARENT_SECTION_CODE" => "",	// Код раздела
						"CACHE_TYPE" => "A",	// Тип кеширования
						"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
						"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
						"CACHE_GROUPS" => "Y",	// Учитывать права доступа
						"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
						"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
						"PAGER_TITLE" => "Новости",	// Название категорий
						"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
						"PAGER_TEMPLATE" => "",	// Название шаблона
						"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
						"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
						"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
						"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
						"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
						),
						false
					);?>					
				</td>
				<td>	
				
					
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="ac_lv" width="3%" >
						</td>
						<td class="ac_v" width="94%">
						</td>
						<td class="ac_rv" width="3%">
						</td>
					</tr>
					<tr>
						<td class="ac_lc" width="3%">
						</td>
						<td width="94%">					
					
							<?$APPLICATION->IncludeComponent("bitrix:news.list", "template1", array(
								"IBLOCK_TYPE" => "news",
								"IBLOCK_ID" => "#ACTION_IBLOCK_ID#",
								"NEWS_COUNT" => "2",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_ORDER1" => "DESC",
								"SORT_BY2" => "SORT",
								"SORT_ORDER2" => "ASC",
								"FILTER_NAME" => "",
								"FIELD_CODE" => array(
									0 => "",
									1 => "",
								),
								"PROPERTY_CODE" => array(
									0 => "",
									1 => "DIRECTION",
									2 => "",
								),
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_SHADOW" => "Y",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "N",
								"CACHE_GROUPS" => "Y",
								"PREVIEW_TRUNCATE_LEN" => "",
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
								"SET_TITLE" => "N",
								"SET_STATUS_404" => "N",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"ALL_LINK" => "action/",
								"DISPLAY_DATE" => "N",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "Y",
								"DISPLAY_PREVIEW_TEXT" => "N",
								"AJAX_OPTION_ADDITIONAL" => ""
								),
								false
							);?>
					
						</td>
						<td class="ac_rc" width="3%">	
						</td>
					</tr>
					<tr>
						<td class="ac_ln" width="3%" >
						</td>
						<td class="ac_n" width="94%">
						</td>
						<td class="ac_rn" width="3%" >
						</td>
					</tr>
				</table>
				</td>	
			</tr>
		</table>
		
		
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
	"ELEMENT_COUNT" => "8",
	"LINE_ELEMENT_COUNT" => "4",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
		4 => "",
	),
	"FLAG_PROPERTY_CODE" => "BESTPRICE",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => SITE_DIR."/personal/basket.php",
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
	"USE_PRODUCT_QUANTITY" => "N",
	"SECTION_NAME" => "Лучшая цена",
	"SECTION_LINK" => "bestprice.php"
	),
	false
);?>
				
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
	"ELEMENT_COUNT" => "4",
	"LINE_ELEMENT_COUNT" => "4",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
		4 => "",
	),
	"FLAG_PROPERTY_CODE" => "NOVELTY",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => SITE_DIR."/personal/basket.php",
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
	"USE_PRODUCT_QUANTITY" => "N",
	"SECTION_NAME" => "Новинки",
	"SECTION_LINK" => "novelty.php"
	),
	false
);?>
		
		
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
	"ELEMENT_COUNT" => "4",
	"LINE_ELEMENT_COUNT" => "4",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
		4 => "",
	),
	"FLAG_PROPERTY_CODE" => "HIT",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => SITE_DIR."/personal/basket.php",
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
	"USE_PRODUCT_QUANTITY" => "N",
	"SECTION_NAME" => "Хит",
	"SECTION_LINK" => "hit.php"
	),
	false
);?>
		

		
		<table width="100%" class="pr" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="pr_lv" width="1%" >
				</td>
				<td class="pr_v" width="80%">
				</td>
				<td class="pr_rv" width="1%">
				</td>
			</tr>
			<tr>
				<td class="pr_lc" width="1%">
				</td>
				<td width="80%">					
					<table width="100%">
				<tr>
					<td colspan="4">
						<div class="maintext">
							SEO-блок
						</div>
						<div class="item"></div>
					</td>
				
				</tr>
				<tr>
					<td colspan="4" style="text-align:left;">
					<div id="text">
						Бытовая техника и электроника делает жизнь человека комфортной. 
Сегодня невозможно представить жизнь без бытовой техники: она облегчает нам жизнь, помогает и развлекает.
 Холодильники, стиральные машины, кухонные плиты – уже давно стали необходимым атрибутом наших домов.
 Кухонная техника помогает творить настоящие кулинарные шедевры. Бытовая техника для красоты и здоровья
 делает уход за собой простым и легким. Техника для дома превращает домашние хлопоты в приятное и необременительное занятие. 
 В нашем магазине вы найдете большой выбор бытовой техники самых разных производителей и ценовых категорий.
 Наш интернет-магазин позволит Вам приобрести качественный товар мировых производителей легко и быстро, не покидая Вашего уютного дома.
Для выбора продукции существует удобный в эксплуатации электронный каталог товаров, с помощью которого Вы можете 
сравнить ту или иную продукцию по характеристикам и ценам. Опытные высококвалифицированные сотрудники нашего магазина 
всегда помогут Вам при выборе продукции. Для Вашего удобства в нашем магазине есть различные варианты связи с нашими 
специалистами, по телефону или через ICQ.
Экономные люди уже давно знают, что на нашем сайте можно купить качественную бытовую технику дешевле чем в обычных магазинах!
						</div>
					</td>
			</tr>
			</table>	
				</td>
				<td class="pr_rc" width="1%">	
				</td>
			</tr>
			<tr>
				<td class="pr_ln" width="1%" >
				</td>
				<td class="pr_n" width="80%">
				</td>
				<td class="pr_rn" width="1%" >
				</td>
			</tr>
		</table>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>