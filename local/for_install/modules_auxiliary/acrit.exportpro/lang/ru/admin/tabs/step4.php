<?
$MESS["ACRIT_EXPORTPRO_SCHEME_DETAIL"] = "Детальная настройка схемы выгрузки";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA"] = "Выводить название торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_PREVIEWTEXT"] = "Выводить анонс торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_DETAILTEXT"] = "Выводить полное описание торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_DETAILPICTURE"] = "Выводить детальную картинку торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_QUANTITY"] = "Выводить количество торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_QUANTITY_RESERVED"] = "Выводить зарезервированное количество торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_WEIGHT"] = "Выводить вес торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_WIDTH"] = "Выводить ширину торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_LENGTH"] = "Выводить длину торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_HEIGHT"] = "Выводить высоту торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_PURCHASING_PRICE"] = "Выводить закупочную цену торговых предложений:";
$MESS["ACRIT_EXPORTPRO_SCHEME_OFFER_IF_SKU_EMPTY"]="Брать из товарного предложения а в случае отсутствия значения из товара";

$MESS["ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_SELECT"] = "Товар/Торговое предложение - выбор данных";
$MESS["ACRIT_EXPORTPRO_SCHEME_TAGS"] = "Служебные теги (описание)";
$MESS["ACRIT_EXPORTPRO_SCHEME_FULLDESC"] = "
<b>#ENCODING#</b> - Выбранная кодировка файла<br/>
<b>#DATE#</b> - Дата в выбранном формате<br/>
<b>#SHOP_NAME#</b> - Наименование магазина<br/>
<b>#COMPANY_NAME#</b> - Наименование компании<br/>
<b>#SITE_URL#</b> - ссылка на сайт<br/>
<b>#CURRENCY#</b> - Блок валюта<br/>
<b>#CATEGORY#</b> - Блок категории<br/>
<b>#DESCRIPTION#</b> - Описание<br/>
<b>#ITEMS#</b> - Основная часть - вывод торговых предложений<br/>
<b>#GROUP_ITEM_ID#</b> - ID группы товарных предложений<br/><br/><i>Все остальные данные правятся под необходимый формат вывода</i>
";
$MESS["ACRIT_EXPORTPRO_SCHEME_ALL"] = "Общая структура";
$MESS["ACRIT_EXPORTPRO_SCHEME_HEADER"] = "Шапка";
$MESS["ACRIT_EXPORTPRO_SCHEME_ISLNFO"] = "Служебная информация";
$MESS["ACRIT_EXPORTPRO_SCHEME_BEGINDATE"] = "Начало (дата)";
$MESS["ACRIT_EXPORTPRO_SCHEME_DATEFORMAT"] = "Формат вывода даты:";
$MESS["ACRIT_EXPORTPRO_SCHEME_SITE_PROTOCOL"] = "Протокол для включения в файл экспорта:";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES"] = "Экспортировать родительские категории:";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_TO_OFFER"] = "Экспортировать родительские категории в товарное предложение:";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_OFFER_CATEGORIES_TO_OFFER"] = "Экспортировать список категорий товарного предложения в товарное предложение:";
$MESS["ACRIT_EXPORTPRO_SCHEME_CENTRAL"] = "Центральная часть";
$MESS["ACRIT_EXPORTPRO_SCHEME_OFFER"] = "Предложение";
$MESS["ACRIT_EXPORTPRO_SCHEME_FOOTER"] = "Подвал";
$MESS["ACRIT_EXPORTPRO_SCHEME_CATEGORY"] = "Категории";
$MESS["ACRIT_EXPORTPRO_SCHEME_CURRENCY"] = "Валюта";
$MESS["ACRIT_EXPORTPRO_SCHEME_ITEMS_TEMPLATES"] = "";
$MESS["ACRIT_EXPORTPRO_"] = "±h";
$MESS["ACRIT_EXPORTPRO_1"] = "±hi";
$MESS["ACRIT_EXPORTPRO_2"] = "±h:i";

$MESS["ACRIT_EXPORTPRO_SCHEME_DETAIL_DESCRIPTION"] = "
Это основная часть настройки выходного файла.
Настройка схемы включает в себя настройку общей структуры, торговых предложений, блока с категориями и валютами.";

$MESS["ACRIT_EXPORTPRO_SCHEME_MAIN_DESCRIPTION"] = "
    В общей структуре указывается формат выходного файла, кодировки, названия магазина,<br>
контейнера торговых предложений и остальных необходимых тегов.<br>
    В оснойной части могут быть использованы служебные зарезервированные теги описанные выше.<br>
Данная область выводится в файл экспорта как есть, заменяя служебные шаблоны.<br>
Если необходимо удалить какой-нибудь тэг из шапки файла или добавить, то он добавляется или удаляется из данной области вручную.
";

$MESS["ACRIT_EXPORTPRO_SCHEME_TAGS_DESCRIPTION"] = "
    Настройка товарного предложения. Все необходимые теги необходимо добавлять вручную.<br>
    Содержимое тэгов может быть прописано непосредственно внутри тэга или в <b>#ПОЛЕ-ЭКСПОРТА#</b>.<br>
    Название <b>#ПОЛЕЙ-ЭКСПОРТА#</b> может быть различным и не совпадать с названием тэга,<br>
    но обязательно совпадать с названием переменной на вкладке \"Поля экспорта\" без решеток (#) вначале и в конце.<br>
    Например для тэга <b>price</b> (&lt;price&gt;#Main_Price#&lt;price&gt;) мы задали шаблон переменную <b>#Main_Price#</b>,<br>
    а в полях экспорта она должна называться <b>Main_Price</b> и ей будет соответствовать какой-нибудь тип цены.<br>
    Тэги для который соответствие <b>#ПОЛЕЙ-ЭКСПОРТА#</b> в полях экспорта не заданы или значение их не определено<br>
    будут удалены из товарного предложения. Так же можно комбинировать статические данные и #ПОЛЯ-ЭКСПОРТА#.<br>
    Например, у вас есть тэг &lt;url&gt;#URL#&lt;/url&gt;, в инфоблоке у вас прописан шаблон для детального отображения относительный<br>
    /catalog/auto/audi-a6/, вы можете добавить доменное имя в тэг &lt;url&gt;http://mysite.ru#URL#&lt;/url&gt;.<br>
    Название #ПОЛЕЙ-ЭКСПОРТА# может быть любым, за исключением названий служебных шаблонов.<br>
    <b>#MARKET_CATEGORY#</b> - Категория соответствующая спецификации торговой площадки на вкладке \"Категории торговой площадки\", служебный шаблон<br>
";

$MESS["ACRIT_EXPORTPRO_SCHEME_CATEGORY_DESCRIPTION"] = "
    Аналогично основной части схемы, прописывается вручную и заменяет в оснойной части шаблон #CATEGORY#.<br>
    Может содержать следующие служебные шаблоны: <br><br>
    <b>#ID#</b> - идентификатор раздела<br>
    <b>#NAME#</b> - название раздела<br>
    <b>#PARENT_ID#</b> - родитель раздела<br>
    <b>#EXTERNAL_ID#</b> - внешний код<br>
    <b>#PARENT_EXTERNAL_ID#</b> - внешний код родительского раздела<br>
";

$MESS["ACRIT_EXPORTPRO_SCHEME_CURRENCY_DESCRIPTION"] = "
    Аналогично предыдущей секции, но заменяет в оснойной части шаблон #CURRENCY#<br>
    и список валют собирается из торговых предложений.<br>
    Может содержать следующие служебные шаблоны: <br><br>
    <b>#CURRENCY#</b> - валюта<br>
    <b>#RATE#</b> - курс<br>
    <b>#PLUS#</b> - коррекция<br>
";

$MESS["ACRIT_EXPORTPRO_SCHEME_DATEFORMAT_HELP"] = "Формат вывода даты";
$MESS["ACRIT_EXPORTPRO_SCHEME_SITE_PROTOCOL_HELP"] = "Протокол работы сайта, для включения в ссылки файла экспорта";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_HELP"] = "Экспортировать родительские категории";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_TO_OFFER_HELP"] = "Экспортировать родительские категории в товарное предложение";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_OFFER_CATEGORIES_TO_OFFER_HELP"] = "Экспортировать список категорий товарного предложения в товарное предложение";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS"]="Экспортировать родительские категории включая инфоблоки";
$MESS["ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS_HELP"]="Определяет экспорт полей инфоблоков";
?>