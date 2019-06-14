<?php
$MESS["GOOGLE_ONLINE"] = array(
	 "CODE"                => "google_online",
	 "GROUP"               => "Google",
	 "NAME"                => "Обновление онлайн-ассортимента (Online)",
	 "DESCRIPTION"         => "Этот фид используется, чтобы регулярно обновлять информацию о ценах, скидках и наличии товаров
<a href=\"https://support.google.com/merchants/answer/6191341\" target=\"_blank\">Подробнее...</a>",
	 "FIELDS"              => array(
			"g:id"                        => array(
				 "CODE"     => "g:id",
				 "NAME"     => "Идентификатор товара",
				 "REQUIRED" => "Y",
				 "TYPE"     => array("FIELD"),
				 "VALUE"    => array("ID"),
			),
			"g:price"                     => array(
				 "CODE"     => "g:price",
				 "NAME"     => "Новая цена товара",
				 "REQUIRED" => "Y",
				 "TYPE"     => array("PRICE"),
				 "VALUE"    => array("RATIO_BASE_PRICE"),
			),
			"g:sale_price"                => array(
				 "CODE"  => "g:sale_price",
				 "NAME"  => "Цена со скидкой",
				 "TYPE"  => array("PRICE"),
				 "VALUE" => array("RATIO_PRICE"),
			),
			"g:sale_price_effective_date" => array(
				 "CODE" => "g:sale_price_effective_date",
				 "NAME" => "Срок действия скидки",
			),
			"g:availability"              => array(
				 "CODE"          => "g:availability",
				 "NAME"          => "Наличие товара в магазине",
				 "REQUIRED"      => "Y",
				 "USE_BOOLEAN"   => "Y",
				 "BOOLEAN_VALUE" => "in stock/out of stock",
				 "TYPE"          => array("PRODUCT"),
				 "VALUE"         => array("AVAILABLE"),
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
			<g:price>#g:price#</g:price>
			<g:sale_price>#g:sale_price#</g:sale_price>
			<g:availability>#g:availability#</g:availability>
			<g:sale_price_effective_date>#g:sale_price_effective_date#</g:sale_price_effective_date>
		</item>',
);

