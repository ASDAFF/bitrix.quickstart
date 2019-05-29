<?
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_NAME"] = "Экспорт в систему авито(\"Дома, дачи, коттеджи\")";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_CATEGORY"] = "Категория объявления<br><b>Возможные значения: Дома, дачи, коттеджи</b><br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_HOUSE_CATEGORY_VALUE"] = "Дома, дачи, коттеджи";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_OPERATIONTYPE'] = "Тип объявления<br><b>Возможные значения: Продам, Сдам<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LOCALITY'] = "Город или населенный пункт, уточнение";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_STREET'] = "Наименование улицы, на которой<br>находится объект объявления";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_SALEROOMS'] = "Количество комнат на продажу / сдающихся<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_ROOMS'] = "Количество комнат в квартире<br>Для квартиры-студии укажите Студия.<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_SQUARE'] = "Площадь комнаты (в м.кв.)<br>Если продается/сдается несколько комнат,<br>указывается их суммарная площадь<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_FLOOR'] = "Этаж, на котором находится объект";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_FLOORS'] = "Количество этажей в доме";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_HOUSETYPE'] = "Тип дома. <br><b>Возможные значения:<ul>
<li>Кирпичный</li>
<li>Панельный</li>
<li>Блочный</li>
<li>Монолит</li>
<li>Деревянный</li></ul> </b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LEASETYPE'] = "Тип аренды,только для типа 'Сдам'<br><b>Возможные значения:<ul>
<li>Долгосрочная</li>
<li>Посуточная</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>
";

$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_IMAGE'] = "Изображения";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_OBJECTTYPE'] = "Вид объекта<br><b>Возможные значения:<ul>
<li>Коттедж</li>
<li>Таунхаус</li>
<li>Дом</li>
<li>Дача</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LANDAREA'] = "Площадь земли в сотках";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DISTANCETOCITY'] = "Расстояние до города в км.<br> Примечание: значение 0 означает,<br> что объект находится в черте города.";
$MESS['ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_WALLSTYPE'] = "Материал стенbr><b>Возможные значения:<ul>
<li>Кирпич</li>
<li>Брус</li>
<li>Бревно</li>
<li>Металл</li>
<li>Пеноблоки</li>
<li>Сэндвич-панели</li>
<li>Ж/б панели</li>
<li>Экспериментальные материалы</li>
</ul></b>
<br>";


$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"1\">
    <Ad>
        <Id>30154</Id>
        <Category>Дома, дачи, коттеджи</Category>
        <OperationType>Сдам</OperationType>
        <ObjectType>Дом</ObjectType>
        <DateBegin>2012-05-01</DateBegin>
        <DateEnd>2012-05-14</DateEnd>
        <Region>Ленинградская область</Region>
        <City>Сестрорецк</City>
        <Locality>п.Александровское</Locality>
        <Street>Тарховская</Street>
        <Square>90</Square>
        <LandArea>3.8</LandArea>
        <DistanceToCity>20</DistanceToCity>
        <WallsType>Кирпич</WallsType>
        <LeaseType>Долгосрочная</LeaseType>
        <Description>Курортный район. до ж/д станции Александровская 5 минут пешком.
        До Разлива 10 минут пешком. Красивая природа, тихо.
        Двухэтажный дом 60кв. м, два входа. В доме, на первом этаже: кухня 13кв. м,
        холл 11кв. м, спальня 15кв. м, открытая веранда. На втором этаже: спальня 14кв. м,
        терраса. Мебель сборная 95-2000-ых годов. Плита - газ (баллон). Участок 3,8 сотки,
        квадратный, разработан .На участке плодовые деревья, ягодные кусты,
        парник, площадка для автомобиля.
        </Description>
        <ContactPhone>985-34-12</ContactPhone>
        <Price>52000.00</Price>
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