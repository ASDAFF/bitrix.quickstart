<?php
$MESS["GOOGLE_PRODUCTS"] = array(
	 "CODE"                => "google_products",
	 "GROUP"               => "Google",
	 "NAME"                => "Фид товаров (Products)",
	 "DESCRIPTION"         => "Основной фид данных, с помощью которого вы отправляете в Google Покупки актуальные сведения о своих товарах.
<a href=\"https://support.google.com/merchants/answer/7052112\" target=\"_blank\">Подробнее...</a>",
	 "FIELDS"              => array(

		 //---------- Основные сведения о товарах ----------//
		 "g:id"                          => array(
				"CODE"     => "g:id",
				"NAME"     => "Уникальный идентификатор товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("FIELD"),
				"VALUE"    => array("ID"),
		 ),
		 "g:title"                       => array(
				"CODE"     => "g:title",
				"NAME"     => "Название товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("FIELD"),
				"VALUE"    => array("NAME"),
		 ),
		 "g:description"                 => array(
				"CODE"     => "g:description",
				"NAME"     => "Описание товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("FIELD"),
				"VALUE"    => array("PREVIEW_TEXT"),
		 ),
		 "g:link"                        => array(
				"CODE"     => "g:link",
				"NAME"     => "Ссылка на целевую страницу товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("FIELD"),
				"VALUE"    => array("DETAIL_PAGE_URL"),
		 ),
		 "g:image_link"                  => array(
				"CODE"     => "g:image_link",
				"NAME"     => "Ссылка на основное изображение товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("FIELD"),
				"VALUE"    => array("DETAIL_PICTURE"),
		 ),
		 "g:additional_image_link"       => array(
				"CODE" => "g:additional_image_link",
				"NAME" => "Ссылка на дополнительное изображение товара",
		 ),
		 "g:mobile_link"                 => array(
				"CODE" => "g:mobile_link",
				"NAME" => "Ссылка на целевую страницу товара, оптимизированная для мобильных устройств",
		 ),


		 //---------- Цена и наличие ----------//
		 "g:availability"                => array(
				"CODE"          => "g:availability",
				"NAME"          => "Наличие товара в магазине",
				"REQUIRED"      => "Y",
				"USE_BOOLEAN"   => "Y",
				"BOOLEAN_VALUE" => "in stock/out of stock",
				"TYPE"          => array("PRODUCT"),
				"VALUE"         => array("AVAILABLE"),
		 ),
		 "g:availability_date"           => array(
				"CODE" => "g:availability_date",
				"NAME" => "Дата, когда товар поступит в магазин",
		 ),
		 "g:expiration_date"             => array(
				"CODE" => "g:expiration_date",
				"NAME" => "Дата, до которой объявления должны отображаться",
		 ),
		 "g:price"                       => array(
				"CODE"     => "g:price",
				"NAME"     => "Цена товара",
				"REQUIRED" => "Y",
				"TYPE"     => array("PRICE"),
				"VALUE"    => array("RATIO_BASE_PRICE"),
		 ),
		 "g:sale_price"                  => array(
				"CODE"  => "g:sale_price",
				"NAME"  => "Цена товара с учетом скидки",
				"TYPE"  => array("PRICE"),
				"VALUE" => array("RATIO_PRICE"),
		 ),
		 "g:sale_price_effective_date"   => array(
				"CODE" => "g:sale_price_effective_date",
				"NAME" => "Диапазон дат, в течение которого действительно значение атрибута sale_price [цена_со_скидкой]",
		 ),
		 "g:unit_pricing_measure"        => array(
				"CODE" => "g:unit_pricing_measure",
				"NAME" => "Количество и единица измерения товара",
		 ),
		 "g:unit_pricing_base_measure"   => array(
				"CODE" => "g:unit_pricing_base_measure",
				"NAME" => "Базовая единица, за которую рассчитывается цена товара, например 100ml",
		 ),
		 "g:installment"                 => array(
				"CODE" => "g:installment",
				"NAME" => "Подробные сведения об оплате покупки в рассрочку",
		 ),
		 "g:loyalty_points"              => array(
				"CODE" => "g:loyalty_points",
				"NAME" => "Количество и тип бонусных баллов, которые пользователь получает за покупку",
		 ),



		 //---------- Категория товара ----------//
		 "g:google_product_category"     => array(
				"CODE" => "g:google_product_category",
				"NAME" => "Категория товара в соответствии с классификацией Google",
		 ),
		 "g:product_type"                => array(
				"CODE" => "g:product_type",
				"NAME" => "Категория товара по классификации продавца",
		 ),


		 //---------- Идентификаторы товара ----------//
		 "g:brand"                       => array(
				"CODE" => "g:brand",
				"NAME" => "Марка товара",
		 ),
		 "g:gtin"                        => array(
				"CODE" => "g:gtin",
				"NAME" => "Код международной маркировки и учета логистических единиц для товара",
		 ),
		 "g:mpn"                         => array(
				"CODE" => "g:mpn",
				"NAME" => "Код производителя товара",
		 ),
		 "g:identifier_exists"           => array(
				"CODE" => "g:identifier_exists",
				"NAME" => "С помощью атрибута identifier_exists [имеет_идентификатор] можно указать, если ли у товара уникальный идентификатор, например gtin [gtin], mpn [mpn] или brand [марка]. ",
		 ),



		 //---------- Подробное описание товара ----------//
		 "g:condition"                   => array(
				"CODE"       => "g:condition",
				"NAME"       => "Состояние товара",
				"REQUIRED"   => 'Y',
				"USE_TEXT"   => 'Y',
				"TEXT_VALUE" => 'new',
		 ),
		 "g:adult"                       => array(
				"CODE" => "g:adult",
				"NAME" => "Указывает на то, что товар содержит материалы сексуального характера",
		 ),
		 "g:multipack"                   => array(
				"CODE" => "g:multipack",
				"NAME" => "Число идентичных товаров в наборе, сформированном продавцом",
		 ),
		 "g:is_bundle"                   => array(
				"CODE" => "g:is_bundle",
				"NAME" => "Указывает на принадлежность товара к набору из нескольких разных товаров, сформированному продавцом",
		 ),
		 "g:energy_efficiency_class"     => array(
				"CODE" => "g:energy_efficiency_class",
				"NAME" => "Класс энергоэффективности товара",
		 ),
		 "g:min_energy_efficiency_class" => array(
				"CODE" => "g:min_energy_efficiency_class",
				"NAME" => "Минимальный класс энергоэффективности",
		 ),
		 "g:max_energy_efficiency_class" => array(
				"CODE" => "g:max_energy_efficiency_class",
				"NAME" => "Максимальный класс энергоэффективности",
		 ),
		 "g:age_group"                   => array(
				"CODE" => "g:age_group",
				"NAME" => "Возраст потребителей, для которых предназначен товар",
		 ),
		 "g:color"                       => array(
				"CODE" => "g:color",
				"NAME" => "Цвет товара",
		 ),
		 "g:gender"                      => array(
				"CODE" => "g:gender",
				"NAME" => "Пол пользователей, для которых предназначен товар",
		 ),
		 "g:material"                    => array(
				"CODE" => "g:material",
				"NAME" => "Материал, из которого изготовлен товар",
		 ),
		 "g:pattern"                     => array(
				"CODE" => "g:pattern",
				"NAME" => "Узор или рисунок на товаре",
		 ),
		 "g:size"                        => array(
				"CODE" => "g:size",
				"NAME" => "Размер товара",
		 ),
		 "g:size_type"                   => array(
				"CODE" => "g:size_type",
				"NAME" => "Особенности покроя товара",
		 ),
		 "g:size_system"                 => array(
				"CODE" => "g:size_system",
				"NAME" => "Система размеров, которая используется в целевой стране товара",
		 ),
		 "g:item_group_id"               => array(
				"CODE" => "g:item_group_id",
				"NAME" => "Идентификатор, общий для всех вариантов одного товара",
		 ),



		 //---------- Торговые кампании и другие инструменты ----------//
		 "g:adwords_redirect"            => array(
				"CODE" => "g:adwords_redirect",
				"NAME" => "URL для отслеживания трафика из Google Покупок",
		 ),
		 "g:excluded_destination"        => array(
				"CODE" => "g:excluded_destination",
				"NAME" => "Атрибут позволяет исключать определенные товары из рекламных кампаний",
		 ),
		 "g:custom_label_0"              => array(
				"CODE" => "g:custom_label_0",
				"NAME" => "Ярлык, по которому можно группировать товары в рамках кампании",
		 ),
		 "g:custom_label_1"              => array(
				"CODE" => "g:custom_label_1",
				"NAME" => "custom_label_1",
		 ),
		 "g:custom_label_2"              => array(
				"CODE" => "g:custom_label_2",
				"NAME" => "custom_label_2",
		 ),
		 "g:custom_label_3"              => array(
				"CODE" => "g:custom_label_3",
				"NAME" => "custom_label_3",
		 ),
		 "g:custom_label_4"              => array(
				"CODE" => "g:custom_label_4",
				"NAME" => "custom_label_4",
		 ),
		 "g:promotion_id"                => array(
				"CODE" => "g:promotion_id",
				"NAME" => "Идентификатор, по которому товары сопоставляются с условиями промоакций",
		 ),



		 //---------- Доставка ----------//
		 /*"g:shipping"     => array(
				"CODE" => "g:shipping",
				"NAME" => "Доставка",
		 ),*/
		 "g:shipping_country"            => array(
				"CODE" => "g:shipping_country",
				"NAME" => "Страна доставки",
		 ),
		 "g:shipping_region"             => array(
				"CODE" => "g:shipping_region",
				"NAME" => "Регион доставки",
		 ),
		 /*"g:shipping_postal_code"     => array(
				"CODE" => "g:shipping_postal_code",
				"NAME" => "почтовый_индекс",
		 ),
		 "g:shipping_location_id"     => array(
				"CODE" => "g:shipping_location_id",
				"NAME" => "идентификатор_местоположения",
		 ),
		 "g:shipping_location_group_name"     => array(
				"CODE" => "g:shipping_location_group_name",
				"NAME" => "название_группы_местоположений",
		 ),*/
		 "g:shipping_service"            => array(
				"CODE" => "g:shipping_service",
				"NAME" => "Сервис доставки",
		 ),
		 "g:shipping_price"              => array(
				"CODE" => "g:shipping_price",
				"NAME" => "Цена доставки",
		 ),
		 "g:shipping_label"              => array(
				"CODE" => "g:shipping_label",
				"NAME" => "Этикетка, по которой можно сопоставить товар и стоимость его доставки в настройках аккаунта",
		 ),
		 "g:shipping_weight"             => array(
				"CODE" => "g:shipping_weight",
				"NAME" => "Вес товара, по которому рассчитывается стоимость доставки",
		 ),
		 "g:shipping_length"             => array(
				"CODE" => "g:shipping_length",
				"NAME" => "Длина упаковки с товаром; нужна, чтобы рассчитать стоимость доставки по габаритному весу",
		 ),
		 "g:shipping_width"              => array(
				"CODE" => "g:shipping_width",
				"NAME" => "Ширина упаковки с товаром; нужна, чтобы рассчитать стоимость доставки по габаритному весу",
		 ),
		 "g:shipping_height"             => array(
				"CODE" => "g:shipping_height",
				"NAME" => "Высота упаковки с товаром; нужна, чтобы рассчитать стоимость доставки по габаритному весу",
		 ),
		 "g:max_handling_time"           => array(
				"CODE" => "g:max_handling_time",
				"NAME" => "Максимальный период времени с момента размещения заказа до оправки товара",
		 ),
		 "g:min_handling_time"           => array(
				"CODE" => "g:min_handling_time",
				"NAME" => "Минимальный период времени с момента размещения заказа до оправки товара",
		 ),


		 //---------- Налоги ----------//
		 /*"g:tax"                    => array(
				"CODE" => "g:tax_rate",
				"NAME" => "Налог",
		 ),*/
		 "g:tax_rate"                    => array(
				"CODE" => "g:tax_rate",
				"NAME" => "Налоговая ставка в процентах",
		 ),
		 "g:tax_country"                 => array(
				"CODE" => "g:tax_country",
				"NAME" => "Код страны",
		 ),
		 "g:tax_region"                  => array(
				"CODE" => "g:tax_region",
				"NAME" => "Регион",
		 ),
		 "g:tax_postal_code"             => array(
				"CODE" => "g:tax_postal_code",
				"NAME" => "Почтовый ?индекс",
		 ),
		 "g:tax_location_id"             => array(
				"CODE" => "g:tax_location_id",
				"NAME" => "Идентификатор ?местоположения",
		 ),
		 "g:tax_tax_ship"                => array(
				"CODE" => "g:tax_tax_ship",
				"NAME" => "Налог на доставку",
		 ),
		 "g:tax_category"                => array(
				"CODE" => "g:tax_category",
				"NAME" => "Категория_налогообложения",
		 ),
	 ),
	 "XML_HEADER"          => '<?xml version="1.0"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
		<title>#SHOP_NAME#</title>
		<link>#SHOP_URL#</link>
		<description>#SHOP_COMPANY#</description>',
	 "XML_FOOTER"          => '</channel>
</rss>',
	 "XML_CURRENCY"        => '',
	 "XML_CATEGORY"        => '',
	 "XML_CATEGORY_PARENT" => '',
	 "XML_DELIVERY_OPTION" => '',
	 "XML_OFFER"           => '	<item>
			<g:id>#g:id#</g:id>
			<g:title>#g:title#</g:title>
			<g:description>#g:description#</g:description>
			<g:link>#g:link#</g:link>
			<g:image_link>#g:image_link#</g:image_link>
			<g:additional_image_link>#g:additional_image_link#</g:additional_image_link>
			<g:mobile_link>#g:mobile_link#</g:mobile_link>
			<g:availability>#g:availability#</g:availability>
			<g:availability_date>#g:availability_date#</g:availability_date>
			<g:expiration_date>#g:expiration_date#</g:expiration_date>
			<g:price>#g:price#</g:price>
			<g:sale_price>#g:sale_price#</g:sale_price>
			<g:sale_price_effective_date>#g:sale_price_effective_date#</g:sale_price_effective_date>
			<g:unit_pricing_measure>#g:unit_pricing_measure#</g:unit_pricing_measure>
			<g:unit_pricing_base_measure>#g:unit_pricing_base_measure#</g:unit_pricing_base_measure>
			<g:installment>#g:installment#</g:installment>
			<g:loyalty_points>#g:loyalty_points#</g:loyalty_points>
			<g:google_product_category>#g:google_product_category#</g:google_product_category>
			<g:product_type>#g:product_type#</g:product_type>
			<g:brand>#g:brand#</g:brand>
			<g:gtin>#g:gtin#</g:gtin>
			<g:mpn>#g:mpn#</g:mpn>    
			<g:identifier_exists>#g:identifier_exists#</g:identifier_exists>    
			<g:condition>#g:condition#</g:condition>
			<g:adult>#g:adult#</g:adult>
			<g:multipack>#g:multipack#</g:multipack>
			<g:is_bundle>#g:is_bundle#</g:is_bundle>
			<g:energy_efficiency_class>#g:energy_efficiency_class#</g:energy_efficiency_class>
			<g:min_energy_efficiency_class>#g:min_energy_efficiency_class#</g:min_energy_efficiency_class>
			<g:age_group>#g:age_group#</g:age_group>
			<g:color>#g:color#</g:color>
			<g:gender>#g:gender#</g:gender>
			<g:material>#g:material#</g:material>
			<g:pattern>#g:pattern#</g:pattern>
			<g:size>#g:size#</g:size>
			<g:size_type>#g:size_type#</g:size_type>
			<g:size_system>#g:size_system#</g:size_system>
			<g:item_group_id>#g:item_group_id#</g:item_group_id>
			<g:adwords_redirect>#g:adwords_redirect#</g:adwords_redirect>
			<g:excluded_destination>#g:excluded_destination#</g:excluded_destination>
			<g:custom_label_0>#g:custom_label_0#</g:custom_label_0>
			<g:custom_label_1>#g:custom_label_1#</g:custom_label_1>
			<g:custom_label_2>#g:custom_label_2#</g:custom_label_2>
			<g:custom_label_3>#g:custom_label_3#</g:custom_label_3>
			<g:custom_label_4>#g:custom_label_4#</g:custom_label_4>
			<g:promotion_id>#g:promotion_id#</g:promotion_id>
			<g:shipping_country>#g:shipping_country#</g:shipping_country>
			<g:shipping_region>#g:shipping_region#</g:shipping_region>
			<g:shipping_service>#g:shipping_service#</g:shipping_service>
			<g:shipping_price>#g:shipping_price#</g:shipping_price>
			<g:shipping_label>#g:shipping_label#</g:shipping_label>
			<g:shipping_weight>#g:shipping_weight#</g:shipping_weight>
			<g:shipping_length>#g:shipping_length#</g:shipping_length>
			<g:shipping_width>#g:shipping_width#</g:shipping_width>
			<g:shipping_height>#g:shipping_height#</g:shipping_height>
			<g:max_handling_time>#g:max_handling_time#</g:max_handling_time>
			<g:min_handling_time>#g:min_handling_time#</g:min_handling_time>
			<g:tax_rate>#g:tax_rate#</g:tax_rate>
			<g:tax_country>#g:tax_country#</g:tax_country>
			<g:tax_region>#g:tax_region#</g:tax_region>
			<g:tax_postal_code>#g:tax_postal_code#</g:tax_postal_code>
			<g:tax_location_id>#g:tax_location_id#</g:tax_location_id>
			<g:tax_tax_ship>#g:tax_tax_ship#</g:tax_tax_ship>
			<g:tax_category>#g:tax_category#</g:tax_category>
		</item>',
);
