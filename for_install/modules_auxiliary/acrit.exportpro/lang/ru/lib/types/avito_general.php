<?
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_NAME"] = "Экспорт в систему авито";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_CATEGORY"] = "Категория объявления<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";


$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"1\">
    <Ad>
        <Id>К137</Id>
        <Category>Комнаты</Category>
        <!-- DateBegin и DateEnd не указаны - объявление будет<br> показано в течение 30 дней, начиная со дня приёма -->
        <Region>Санкт-Петербург</Region>
        <Subway>Парк Победы</Subway>
        <Description>Продаются 2 комнаты в 3-комнатной кв. Дом - кирпичная хрущевка, вход со двора,
        окна на зеленый двор, сделан ремонт, есть телефон, пол - паркет, с/у - раздельный,
        ванна отдельная. Светлая комната, квадратная. 1 сосед - молодой человек, квартира чистая,
        стеклопакеты. Зеленый двор, школа и детский сад рядом. торг
        </Description>
        <ContactPhone>293 45 45</ContactPhone>
        <Price>2400000</Price>
        <Images>
            <Image url=\"http://img.industry-soft.ru/591F0E40-2238-48DB-8F7B-4A4F3A0F2BA1.jpg\"></Image>
            <Image url=\"http://img.industry-soft.ru/8F81612B-5742-4492-9CF3-ECF9DCBA0172.jpg\"></Image>
            <Image url=\"http://img.industry-soft.ru/95B5B3DA-2FCC-4126-9CD0-BA0B1A000313.jpg\"></Image>
        </Images>
        <AdStatus>Free</AdStatus>
    </Ad>
</Ads>
";
?>