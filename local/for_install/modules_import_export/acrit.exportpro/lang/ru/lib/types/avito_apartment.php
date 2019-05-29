<?
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_NAME"] = "Экспорт в систему авито(\"Квартиры\")";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_CATEGORY"] = "Категория объявления<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_ADSTATUS"] = "Информация о платной услуге, которую нужно применить к объявлению — одно из значений списка:<br/><br/>\"Free\" — обычное объявление;<br/>\"Premium\" — премиум-объявление;<br/>\"VIP\" — VIP-объявление;<br/>\"PushUp\" — поднятие объявления в поиске;<br/>\"Highlight\" — выделение объявления;<br/>\"TurboSale\"— применение пакета \"Турбо-продажа\";<br/>\"QuickSale\" — применение пакета \"Быстрая продажа\".";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_ADSTATUS_VALUE"] = "Free";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_APARTMENT_CATEGORY_VALUE"] = "Квартиры";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_OPERATIONTYPE'] = "Тип объявления<br><b>Возможные значения: Продам, Сдам<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_LOCALITY'] = "Город или населенный пункт, уточнение";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_STREET'] = "Наименование улицы, на которой<br>находится объект объявления";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_SALEROOMS'] = "Количество комнат на продажу / сдающихся<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_ROOMS'] = "Количество комнат в квартире<br>Для квартиры-студии укажите Студия.<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_SQUARE'] = "Площадь комнаты (в м.кв.)<br>Если продается/сдается несколько комнат,<br>указывается их суммарная площадь<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_FLOOR'] = "Этаж, на котором находится объект";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_FLOORS'] = "Количество этажей в доме";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_HOUSETYPE'] = "Тип дома. <br><b>Возможные значения:<ul>
<li>Кирпичный</li>
<li>Панельный</li>
<li>Блочный</li>
<li>Монолит</li>
<li>Деревянный</li></ul> </b>";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_LEASETYPE'] = "Тип аренды,только для типа 'Сдам'<br><b>Возможные значения:<ul>
<li>Долгосрочная</li>
<li>Посуточная</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>
";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_MARKETTYPE'] = "Принадлежность квартиры к рынку новостроек<br> или вторичному,
только для типа 'Продам'<br><b>Возможные значения:<ul>
<li>Вторичка</li>
<li>Новостройка</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>
";
$MESS['ACRIT_EXPORTPRO_AVITO_APARTMENT_FIELD_IMAGE'] = "Изображения";


$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_APARTMENT_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_APARTMENT_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_APARTMENT_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"1\">
    <Ad>
        <Id>5308</Id>
        <Category>Квартиры</Category>
        <OperationType>Продам</OperationType>
        <DateBegin>2012-05-01</DateBegin>
        <DateEnd>2012-05-14</DateEnd>
        <Region>Санкт-Петербург</Region>
        <City>Левашово</City>
        <Locality>Юкки</Locality>
        <Street>Энгельса просп 93</Street>
        <Subway>Удельная</Subway>
        <Rooms>3</Rooms>
        <Square>161.7</Square>
        <Floor>1</Floor>
        <Floors>2</Floors>
        <HouseType>Панельный</HouseType>
        <MarketType>Вторичка</MarketType>
        <LeaseType>Долгосрочная</LeaseType>
        <Description>Крупно-панельный дом (г.п. 2007); вход с улицы, окна на улицу и во двор,
        с/у - совмещенный, 2-х комн. квартира. Дом находится в живописном месте, пруд с форелью,
        лес, ягоды, грибы, удобная транспортное сообщение с СПб, вся инфраструктура, хороший
        подъезд круглый год.
        </Description>
        <ContactPhone>+7 (905) 207-04-90</ContactPhone>
        <Price>13800000</Price>
        <Images>
            <Image url=\"http://img.industry-soft.ru/591F0E40-2238-48DB-8F7B-4A4F3A0F2BA1.jpg\"/>
            <Image url=\"http://img.industry-soft.ru/8F81612B-5742-4492-9CF3-ECF9DCBA0172.jpg\"/>
            <Image url=\"http://img.industry-soft.ru/95B5B3DA-2FCC-4126-9CD0-BA0B1A000313.jpg\"/>
        </Images>
        <AdStatus>Premium</AdStatus>
    </Ad>
</Ads>
";
?>