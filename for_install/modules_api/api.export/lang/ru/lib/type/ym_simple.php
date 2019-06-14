<?
$MESS['YML_SIMPLE'] = array(
	 "CODE"                => "ym_simple",
	 "GROUP"               => "Яндекс.Маркет",
	 "NAME"                => "Упрощенный тип описания (simple)",
	 "DESCRIPTION"         => "Более гибкий, поскольку элементы &lt;model&gt; и &lt;vendor&gt; необязательные, но менее точный для попадания товара в карточку модели.<br>
Название предложения целиком передается в одном элементе &lt;name&gt; 
<a href=\"https://yandex.ru/support/partnermarket/offers.html\" target=\"_blank\">Подробнее...</a>",
	 "DATE_FORMAT"         => "Y-m-d H:i",
	 "FIELDS"              => array(
			"id"                    => array(
				 "CODE"     => "id",
				 "NAME"     => "<b>Идентификатор товарного предложения</b><br>Атрибут может содержать только цифры и латинские буквы.<br>Максимальная длина id — 20 символов",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("ID"),
			),
			"name"                  => array(
				 "CODE"         => "name",
				 "NAME"         => "<b>Название товарного предложения</b>",
				 "REQUIRED"     => 'Y',
				 "USE_FUNCTION" => "Y",
				 "FUNCTION"     => "htmlspecialchars",
				 "TYPE"         => array('FIELD'),
				 "VALUE"        => array("NAME"),
			),
			"model"                 => array(
				 "CODE" => "model",
				 "NAME" => "<b>Модель</b><br>Например: BenQ EW2750ZL",
			),
			"vendor"                => array(
				 "CODE" => "vendor",
				 "NAME" => "<b>Производитель</b><br>Например: BenQ",
			),
			"vendorCode"            => array(
				 "CODE" => "vendorCode",
				 "NAME" => "<b>Код товара (код производителя)</b><br>Например: 9H.LDELB.QBE",
			),
			"cbid"                  => array(
				 "CODE" => "cbid",
				 "NAME" => "<b>Размер ставки для карточки модели</b>",
			),
			"bid"                   => array(
				 "CODE" => "bid",
				 "NAME" => "<b>Размер ставки на остальных местах размещения (кроме карточки модели)</b>",
			),
			"fee"                   => array(
				 "CODE" => "fee",
				 "NAME" => "<b>Размер комиссии на товарное предложение, участвующее в программе «Заказ на Маркете»</b>",
			),
			"group_id"        => array(
				 "CODE" => "group_id",
				 "NAME" => "Элемент объединяет всех предложения, которые являются вариациями одной модели и должен иметь одинаковое значение. Значение должно быть целым числом, максимум 9 разрядов. ",
			),
			"url"                   => array(
				 "CODE"     => "url",
				 "NAME"     => "<b>URL страницы товара</b><br>Максимальная длина URL — 512 символов<br>Необязательный элемент для розничных магазинов",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("DETAIL_PAGE_URL"),
			),
			"price"                 => array(
				 "CODE"     => "price",
				 "NAME"     => "<b>Цена, по которой данный товар можно приобрести</b><br>Цена округляется и обновляется на Маркете каждые 40 – 80 минут.",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array("PRICE"),
				 "VALUE"    => array("RATIO_PRICE"),
			),
			"oldprice"              => array(
				 "CODE"  => "oldprice",
				 "NAME"  => "<b>Старая цена</b><br>Обязательно должна быть выше цены (price)<br>Необходима для автоматического расчета скидки на товар<br>Скидка обновляется на Маркете каждые 40 – 80 минут.",
				 "TYPE"  => array("PRICE"),
				 "VALUE" => array("OLD_PRICE"),
			),
			"currencyId"            => array(
				 "CODE"     => "currencyId",
				 "NAME"     => "<b>Идентификатор валюты товара (RUR / RUB, USD, UAH, KZT)</b><br>Для корректного отображения цены в национальной валюте необходимо использовать идентификатор (например, RUR) с соответствующим значением цены.",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array("CURRENCY"),
				 "VALUE"    => array("RUB"),
			),
			"categoryId"            => array(
				 "CODE"     => "categoryId",
				 "NAME"     => "<b>Идентификатор категории (раздела) товара</b><br>Целое число не более 18 знаков<br>Товарное предложение может принадлежать только одной категории",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("IBLOCK_SECTION_ID"),
			),
			/*"market_category" => array(
				"CODE"     => "market_category",
				"NAME"     => "<b>Категория товара, в которой он должен быть размещен на Яндекс.Маркете</b><br>Допустимо указывать названия категорий только из товарного дерева категорий Яндекс.Маркета",
				"VALUE"    => "IBLOCK_SECTION_ID",
				"TYPE"     => 'FIELD',
			),*/
			"picture"               => array(
				 "CODE"     => "picture",
				 "NAME"     => "<b>Ссылка на картинку товарного предложения</b><br>Недопустимо давать ссылку на «заглушку» или на «логотип» магазина",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("DETAIL_PICTURE"),
			),
			"delivery"              => array(
				 "CODE" => "delivery",
				 "NAME" => "<b>Возможность курьерской доставки товара</b><br><code>true</code> — товар может быть доставлен курьером<br><code>false</code> — товар не может быть доставлен курьером (только самовывоз);",
			),
			/*"delivery_options" => array(
				"CODE" => "delivery_options",
				"NAME" => "",
			),*/
			"pickup"                => array(
				 "CODE" => "pickup",
				 "NAME" => "<b>Возможность самовывоза из пунктов выдачи</b><br><code>true</code> — товар можно забрать в пунктах выдачи («самовывозом»);<br><code>false</code> — товар нельзя забрать в пунктах выдачи.",
			),
			"available"             => array(
				 "CODE"  => "available",
				 "NAME"  => "<strong>Cтатус доступности товара</strong><br><code>true</code> — «готов к отправке»<br>Товар будет доставлен курьером или в пункт выдачи в указанные сроки. На Яндекс.Маркете показываются сроки, настроенные в личном кабинете<br><code>false</code> — «на заказ»<br>Точный срок доставки курьером или в пункт выдачи неизвестен. Срок будет согласован с покупателем персонально (максимальный срок — два месяца). На Яндекс.Маркете сроки не показываются, показывается надпись «на заказ».<br><br><b>Внимание!</b> Элемент используется в дополнение к данным, настроенным в личном кабинете. Элемент не используется, когда условия локальной курьерской доставки настроены в прайс-листе (любого формата).",
			),
			"store"                 => array(
				 "CODE" => "store",
				 "NAME" => "<b>Возможность купить товар в розничном магазине</b><br><code>true</code> — товар можно купить в розничных магазинах.<br><code>false</code> — возможность покупки в розничных магазинах отсутствует;",
			),
			"outlets"               => array(
				 "CODE" => "outlets",
				 "NAME" => "<b>В элементе указывается:</b><br>
											<ul>
												<li>количество товара в точке продаж (пункте выдачи или розничном магазине);</li>
												<li>доступность товара для бронирования.</li>
											</ul>",
			),
			"description"           => array(
				 "CODE"         => "description",
				 "NAME"         => "<b>Описание товарного предложения</b>
														<br>В описании запрещено:<br>
														<ul>
															<li>давать инструкции по применению, установке или сборке;</li>
															<li>использовать слова «скидка», «распродажа», «дешевый», «подарок» (кроме подарочных категорий), «бесплатно», «акция», «специальная цена», «только», «новинка», «new», «аналог», «заказ», «хит»;</li>
															<li>указывать номера телефонов, адреса электронной почты, почтовые адреса, номера ICQ, логины мессенджеров, любые URL-ссылки</li>
														</ul>",
				 "USE_FUNCTION" => "Y",
				 "FUNCTION"     => "fn_htmlToText",
				 'TYPE'         => array("FIELD"),
				 "VALUE"        => array("DETAIL_TEXT"),
			),
			"sales_notes"           => array(
				 "CODE" => "sales_notes",
				 "NAME" => "<b>Элемент используется для отражения информации о:</b>
															<br>
															<ul>
																<li>минимальной сумме заказа, минимальной партии товара, необходимости предоплаты (указание элемента обязательно, если имеются такие условия);</li>
																<li>вариантах оплаты, описания акций и распродаж (указание элемента необязательно).</li>
															</ul>
															<br>Допустимая длина текста в элементе — 50 символов.",
			),
			"min-quantity"          => array(
				 "CODE" => "min-quantity",
				 "NAME" => "<b>Минимальное количество одинаковых товаров в одном заказе</b><br>(для случаев, когда покупка возможна только комплектом, а не поштучно). Элемент используется только в категориях «Автошины», «Грузовые шины», «Мотошины», «Диски».",
			),
			"step-quantity"         => array(
				 "CODE" => "step-quantity",
				 "NAME" => "<b>Количество товара, которое покупатель может добавлять к минимальному в корзине Яндекс.Маркета</b><br>Элемент используется в дополнение к min-quantity и только в категориях «Автошины», «Грузовые шины», «Мотошины», «Диски».",
			),
			"manufacturer_warranty" => array(
				 "CODE" => "manufacturer_warranty",
				 "NAME" => "<b>Элемент предназначен для отметки товаров, имеющих официальную гарантию производителя</b><br><code>true</code> — товар имеет официальную гарантию.<br><code>false</code> — товар не имеет официальной гарантии;",
			),
			"country_of_origin"     => array(
				 "CODE" => "country_of_origin",
				 "NAME" => "<b>Элемент предназначен для указания страны производства товара</b>",
			),
			"adult"                 => array(
				 "CODE" => "adult",
				 "NAME" => "<b>Элемент обязателен для обозначения товара, имеющего отношение к удовлетворению сексуальных потребностей, либо иным образом эксплуатирующего интерес к сексу.</b>",
			),
			"age"                   => array(
				 "CODE" => "age",
				 "NAME" => "<b>Возрастная категория товара</b>
										<br>
										<ul>
											<li>Годы задаются с помощью атрибута unit со значением year. Допустимые значения параметра age при unit=\"year\": 0, 6, 12, 16, 18.</li>
											<li>Месяцы задаются с помощью атрибута unit со значением month. Допустимые значения параметра age при unit=\"month\": 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12.</li>
										</ul>",
			),
			"barcode"               => array(
				 "CODE" => "barcode",
				 "NAME" => "<b>Штрихкод товара, указанный производителем</b>",
			),
			"cpa"                   => array(
				 "CODE" => "cpa",
				 "NAME" => "<b>С помощью элемента можно управлять участием предложений в программе «Заказ на Маркете».</b>
										<br><code>1</code> — товар можно заказать прямо на Маркете;
										<br><code>0</code> — товар нельзя заказать на Маркете (только на сайте магазина).
										<br>Если значение не указано, по умолчанию считается, что товар можно заказать на Маркете.",
			),
			"expiry"                => array(
				 "CODE" => "expiry",
				 "NAME" => "<b>Элемент предназначен для указания срока годности / срока службы либо для указания даты истечения срока годности / срока службы.</b>
										<br>Значение элемента должно быть в формате ISO8601:
										<ul>
											<li>для срока годности / срока службы: P1Y2M10DT2H30M.<br>
											 Расшифровка примера — 1 год, 2 месяца, 10 дней, 2 часа и 30 минут;
											</li>
											<li>для даты истечения срока годности / срока службы: YYYY-MM-DDThh:mm.</li>
										</ul>",
			),
			"weight"                => array(
				 "CODE" => "weight",
				 "NAME" => "<b>Элемент предназначен для указания веса товара.</b><br>Вес указывается в килограммах с учетом упаковки.",
			),
			"dimensions"            => array(
				 "CODE" => "dimensions",
				 "NAME" => "<b>Элемент предназначен для указания габаритов товара (длина, ширина, высота) в упаковке</b><br>Размеры указываются в сантиметрах",
			),
			"downloadable"          => array(
				 "CODE" => "downloadable",
				 "NAME" => "<b>Элемент предназначен для обозначения товара, который можно скачать</b><br>Если указано значение параметра true, товарное предложение показывается во всех регионах независимо от регионов доставки, указанных магазином на странице «Общие настройки».",
			),
			"rec"                   => array(
				 "CODE" => "rec",
				 "NAME" => "<b>Элемент предназначен для передачи рекомендованных товаров.</b>",
			),
			//!!!Кастомное поле не удалять!!!
			"param"                 => array(
				 "CODE"      => "param",
				 "NAME"      => "<b>Описание характеристик и параметров товара</b>",
				 "IS_CUSTOM" => 1,
			),
	 ),
	 "XML_HEADER"          => '<?xml version="1.0" encoding="#ENCODING#"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="#DATE#">
<shop>
<name>#SHOP_NAME#</name>
<company>#SHOP_COMPANY#</company>
<url>#SHOP_URL#</url>
<platform>1C-Bitrix</platform>
<currencies>#CURRENCIES#</currencies>
<categories>#CATEGORIES#</categories>
<delivery-options>#DELIVERY_OPTIONS#</delivery-options>
<offers>',
	 "XML_FOOTER"          => '</offers>
</shop>
</yml_catalog>',
	 "XML_CURRENCY"        => '<currency id="#ID#" rate="#RATE#" plus="#PLUS#"></currency>',
	 "XML_CATEGORY"        => '<category id="#ID#">#NAME#</category>',
	 "XML_CATEGORY_PARENT" => '<category id="#ID#" parentId="#PARENT_ID#">#NAME#</category>',
	 "XML_DELIVERY_OPTION" => '<option cost="#cost#" days="#days#" order-before="#order_before#"/>',
	 "XML_OFFER"           => '<offer id="#id#" available="#available#" bid="#bid#" cbid="#cbid#" fee="#fee#" group_id="#group_id#">
    <url>#url#</url>
    <price>#price#</price>
    <oldprice>#oldprice#</oldprice>
    <currencyId>#currencyId#</currencyId>
    <categoryId>#categoryId#</categoryId>
    <picture>#picture#</picture>
    <store>#store#</store>
    <pickup>#pickup#</pickup>
    <delivery>#delivery#</delivery>
    <vendor>#vendor#</vendor>
    <vendorCode>#vendorCode#</vendorCode>
    <model>#model#</model>
		<name>#name#</name>
    <description>#description#</description>
    <sales_notes>#sales_notes#</sales_notes>
    <manufacturer_warranty>#manufacturer_warranty#</manufacturer_warranty>
    <country_of_origin>#country_of_origin#</country_of_origin>
    <downloadable>#downloadable#</downloadable>
    <adult>#adult#</adult>
    <age>#age#</age>
    <barcode>#barcode#</barcode>
    <cpa>#cpa#</cpa>
    <rec>#rec#</rec>
    <expiry>#expiry#</expiry>
    <weight>#weight#</weight>
    <dimensions>#dimensions#</dimensions>
    #custom#
	</offer>',
);