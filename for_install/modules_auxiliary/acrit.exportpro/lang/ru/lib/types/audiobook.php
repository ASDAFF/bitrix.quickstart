<?
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_NAME"] = "Аудиокниги (audiobook)";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_BID"] = "Основная ставка клика";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_URL"] = "URL страницы товара.<br>Максимальная длина URL — 512 символов.<br>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_CATEGORY"] = "Идентификатор категории товара.<br>Товарное предложение может принадлежать только одной категории.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_AUTHOR"] = "Автор произведения";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_NAME"] = "Название произведения.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PUBLISHER"] = "Издательство";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_SERIES"] = "Серия";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_YEAR"] = "Год издания";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_ISBN"] = "Код книги";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_VOLUME"] = "Номер тома";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PART"] = "Номер части";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_LANGUAGE"] = "Язык произведения";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_TABLEOFCONTENTS"] = "Оглавление.<br>Выводится информация о названиях произведений,<br>если это сборник рассказов или стихов";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PERFORMEDBY"] = "Исполнитель";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_PERFORMANCETYPE"] = "Тип аудиокниги";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_STORAGE"] = "Носитель, на котором поставляется аудиокнига";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_FORMAT"] = "Формат аудиокниги";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_RECORDINGLENGTH"] = "Время звучания задается в формате mm.ss (минуты.секунды)";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_DESCRIPTION"] = "Аннотация к книге.<br>Длина текста не более 175 символов (не включая знаки препинания),<br> запрещено использовать HTML-теги <br>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_DOWNLOADABLE"] = "Элемент предназначен для обозначения товара, который можно скачать";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_AGE"] = "Возрастная категория товара";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_MARKET_AUDIOBOOK_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_AUDIOBOOK_PORTAL_REQUIREMENTS"] = "https://yandex.ru/support/partnermarket/offers.xml#audiobook";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_AUDIOBOOK_PORTAL_VALIDATOR"] = "https://webmaster.yandex.ru/xsdtest.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_AUDIOBOOK_EXAMPLE"] = "
<offer id=\"12342\" type=\"audiobook\" available=\"true\" bid=\"17\">
    <url>http://best.seller.ru/product_page.asp?pid=14345</url>
    <price>200</price>
    <currencyId>RUR</currencyId>
    <categoryId>3</categoryId>
    <picture>http://best.seller.ru/product_page.asp?pid=14345.jpg</picture>
    <author>Владимир Кунин</author>
    <name>Иваnов и Rабинович, или Аj'гоу ту 'Хаjфа!</name>
    <publisher>1С-Паблишинг, Союз</publisher>
    <year>2008</year>
    <ISBN>978-5-9677-0757-5</ISBN>
    <language>ru</language>
    <performed_by>Николай Фоменко</performed_by>
    <performance_type>начитана </performance_type>
    <storage>CD</storage>
    <format>mp3</format>
    <description>Перу Владимира Кунина принадлежат десятки сценариев к кинофильмам,
    серия книг про КЫСЮ и многое, многое другое.</description>
    <downloadable>true</downloadable>
    <age unit=\"year\">18</age>
</offer>
";
?>