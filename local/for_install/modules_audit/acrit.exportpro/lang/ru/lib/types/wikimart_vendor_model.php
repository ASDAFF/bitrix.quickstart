<?
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_NAME"] = "Произвольный товар (vendor.model)";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_ID"] = "Идентификатор торгового предложения.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_AVAILABLE"] = "Cтатус доступности товара.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_URL"] = "URL страницы товара.<br>Максимальная длина URL — 512 символов.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_OLDPRICE"] = "Старая цена на товар, которая обязательно должна быть выше новой цены (price). Параметр <oldprice> необходим для автоматического расчета скидки на товар";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_CATEGORY"] = "Идентификатор категории товара.<br>Товарное предложение может принадлежать только одной категории.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_MODEL"] = "Название товара.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_TYPEPREFIX"] = "Группа товаров/категория.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_VENDOR"] = "Производитель. Не отображается в названии предложения.<br><b>Обязательный элемент</b>.";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_VENDORCODE"] = "Код товара (указывается код производителя)";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_DESCRIPTION"] = "Аннотация к книге.<br>Длина текста не более 175 символов (не включая знаки препинания),<br> запрещено использовать HTML-теги <br>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_COUNTRY_OF_ORIGIN"] = "Страна производства товара.<br> Список стран доступен по адресу:<br>http://partner.market.yandex.ru/pages/help/Countries.pdf";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_STOCK"] = "Количество доступных для заказа единиц товара";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_ACCESSORY"] = "Указание товарных предложений, являющихся аксессуарами к товару";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_MANUFACTURER_WARRANTY"] = "Отмечает наличие или отсутствие у товара гарантии производителя. <br>false - официальная гарантия отсутствует<br>true - официальная гарантия есть";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_LOCAL_DELIVERY_COST"] = "Стоимость доставки данного товара в своем регионе";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_WIKIMART_DELIVERY_COST"] = "Описание условий доставки";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_PARAM"] = "Характеристики товара";

$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_WIKIMART_VENDORMODEL_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_TYPE_WIKIMART_VENDORMODEL_PORTAL_REQUIREMENTS"] = "http://img.0cw.ru/docs/Wikimart_common_instruction.pdf?c796e6";
$MESS["ACRIT_EXPORTPRO_TYPE_WIKIMART_VENDORMODEL_EXAMPLE"] = "<offer id=\"12346\" available=\"true\">
    <url>http://best.seller.ru/product_page.asp?pid=12348</url>
    <price>600</price>
    <currencyId>USD</currencyId>
    <categoryId>6</categoryId>
    <picture>http://best.seller.ru/img/device12345.jpg</picture>
    <name>Casio A1234567B</name>
    <typePrefix>Наручные часы</typePrefix>
    <vendor>Casio</vendor>
    <vendorCode>A1234567B</vendorCode>
    <description>Изящные наручные часы</description>
    <param name=\"Пол\">женские</param>
    <manufacturer_warranty>P2Y</manufacturer_warranty>
    <country_of_origin>Япония</country_of_origin>
</offer>";
?>