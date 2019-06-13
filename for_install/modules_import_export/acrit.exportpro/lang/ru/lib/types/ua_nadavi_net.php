<?
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_NAME"] = "Выгрузка на портал Nadavi.net";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_URL"] = "URL страницы товара.<br>Максимальная длина URL — 512 символов.<br>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_CATEGORY"] = "Идентификатор категории товара.<br>Товарное предложение может принадлежать только одной категории.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_NAME"] = "Название произведения.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_DESCRIPTION"] = "Аннотация к книге.<br>Длина текста не более 175 символов (не включая знаки препинания),<br> запрещено использовать HTML-теги <br>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_TYPEPREFIX"] = "Группа товаров/категория<br>дополнительный параметр, например: Утюг, Чайник, Ковеварка)";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_VENDOR"] = "Производитель. Не отображается в названии предложения";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_UA_NADAVI_NET_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";

$MESS["ACRIT_EXPORTPRO_TYPE_UA_NADAVI_NET_PORTAL_REQUIREMENTS"] = "http://nadavi.net/nadavi.php?idPage_=57&idBookmark_=5";
$MESS["ACRIT_EXPORTPRO_TYPE_UA_NADAVI_NET_EXAMPLE"] = "
<item id=\"12346\">
    <url>http://best.seller.ru/product_page.asp?pid=12348</url>
    <price>600</price>
    <categoryId>6</categoryId>
    <typePrefix>Часы</typePrefix>
    <vendor>Casio</vendor>
    <name>Наручные часы Casio A1234567B</name>
    <image>http://best.seller.ru/img/device12345.jpg</image>
    <description>Изящные наручные часы.</description>
</item>
";
?>