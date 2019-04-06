<?
$MESS["ACRIT_EXPORTPRO_EBAY_1_NAME"] = "Экспорт в систему ebay.com (Описание товаров)";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_SKU"] = "Идентификатор торгового предложения<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_TITLE"] = "Наименование торгового предложения<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_SUBTITLE"] = "Краткое описание торгового предложения<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_TEMPLATE"] = "Шаблон";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_DESCRIPTION"] = "Описание торгового предложения<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_URL"] = "URL изображений";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_CONDITION"] = "Состояние торгового предложения<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_BRAND"] = "Бренд";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_MODEL"] = "Модель";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_MANUFACTURE_CODE"] = "Фабричный номер детали";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_COUNTRY"] = "Страна/регион производителя";

$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_FORMAT_POLICE_PAY"] = "Название политики оплаты";
$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_FORMAT_POLICE_RETURN"] = "Название политики возврата";
$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_FORMAT_POLICE_DELIVERY"] = "Название политики доставки";

$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_EXAMPLE_BRAND"] = "Бренд";
$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_EXAMPLE_MODEL"] = "Модель";
$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_EXAMPLE_MANUFACTURECODE"] = "Фабричный номер детали";
$MESS["ACCRIT_EXPORTPRO_TYPE_EBAY_1_EXAMPLE_COUNTRY"] = "Страна/регион производителя";

$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_EBAY_1_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";

$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_1_PORTAL_REQUIREMENTS"] = "http://pages.ebay.com/ru/ru-ru/kak-prodavat-na-ebay-spravka/mip-neobhodimie-dannie.html";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_1_EXAMPLE"] = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ListingArray>
<Listing>	
	<Product>
	<SKU>0001</SKU>
	<ProductInformation>
		<Title>Шайка d19 (тестовый товар)</Title>
		<SubTitle>Товар для ремонта</SubTitle>
			<Description>
				<Template></Template>
				<ProductDescription>&lt;p style=&quot;color:red&quot;&gt;Внимание! Товар не покупать&lt;/p&gt;</ProductDescription>
			</Description>				
			<PictureUrls>
				<PictureUrl>http://ссылка на главную фотографию товара</PictureUrl>
				<PictureUrl>http://ссылка на вторую фотографию товара</PictureUrl>
			</PictureUrls>
			<Categories>
				<Category Type=\"eBayLeafCategory\">42903</Category>
			</Categories>
			<Attributes>
				<Attribute Name=\"Бренд\">Helix</Attribute>
				<Attribute Name=\"Модель\">101010</Attribute>
				<Attribute Name=\"Фабричный номер детали\">отсутствует</Attribute>
				<Attribute Name=\"Страна/регион производителя\">Россия</Attribute>
			</Attributes>
			<ConditionInfo>
				<Condition>New</Condition>
			</ConditionInfo>
	</ProductInformation>
	</Product>
	<ListingDetails>
		<PaymentPolicy>Название политики оплаты</PaymentPolicy>
		<ReturnPolicy>Название политики возврата </ReturnPolicy>
		<ShippingPolicy>Название политики доставки</ShippingPolicy>
	</ListingDetails>
</Listing>									
</ListingArray>
";
?>