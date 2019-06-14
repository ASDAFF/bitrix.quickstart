<?
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_NAME"] = "Экспорт в систему авито(\"Недвижимость за рубежом\")";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CATEGORY"] = "Категория объявления<br><b>Возможные значения: Недвижимость за рубежом</b><br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_IMPORTREAL_CATEGORY_VALUE"] = "Недвижимость за рубежом";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_OPERATIONTYPE'] = "Тип объявления<br><b>Возможные значения: Продам, Сдам<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_LOCALITY'] = "Город или населенный пункт, уточнение";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_STREET'] = "Наименование улицы, на которой<br>находится объект объявления";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ROOMS'] = "Количество комнат в квартире";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_SQUARE'] = "Площадь в кв. м.<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CITY'] = "Город или населенный пункт, в котором находится объект объявления<br>см. Местоположение объявления<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_COUNTRY'] = "Страна, в которой находится объект объявления<br>Значение из справочника стран<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_LEASETYPE'] = "Тип аренды,только для типа 'Сдам'<br><b>Возможные значения:<ul>
<li>Долгосрочная</li>
<li>Посуточная</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>
";

$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_IMAGE'] = "Изображения";
$MESS['ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_OBJECTTYPE'] = "Вид объекта<br><b>Возможные значения:<ul>
<li>Квартира, апартаменты</li>
<li>Дом, вилла</li>
<li>Земельный участок</li>
<li>Гараж, машиноместо</li>
<li>Коммерческая недвижимость</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>";


$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"2\">
    <Ad>
        <Id>38</Id>
        <Category>Недвижимость за рубежом</Category>
        <OperationType>Продам</OperationType>
        <ObjectType>Квартира, апартаменты</ObjectType>
        <DateBegin>2012-05-01</DateBegin>
        <DateEnd>2012-05-14</DateEnd>
        <Country>Болгария</Country>
        <Locality>Приморско</Locality>
        <Region>Москва</Region>
        <Subway>Сокол</Subway>
        <Rooms>1</Rooms>
        <Square>40</Square>
        <Description>В собственности с 2010г,акт 16, с мебелью ,кухонным уголком,
        балкон,окна на улицу, 300 м до пляжа(2 пляжа северный и южный).
        Комплекс состоит из двух зданий, имеются также бассейн, ресторан, фитнес, массаж,
        румсервис, бар, детская площадка и бассейн, места отдыха, магазины, панорамный балкон,
        настольный теннис, велосипеды, автостоянка и ряд развлекательных удобств располагаются
        на территории общей площадью в 2500 кв.м.
        </Description>
        <ContactPhone>+7 (495) 389-89-14</ContactPhone>
        <Price>990000</Price>
        <Images>
            <Image url=\"http://img.industry-soft.ru/591F0E40-2238-48DB-8F7B-4A4F3A0F2BA1.jpg\"/>
            <Image url=\"http://img.industry-soft.ru/95B5B3DA-2FCC-4126-9CD0-BA0B1A000313.jpg\"/>
        </Images>
        <AdStatus>Premium</AdStatus>
    </Ad>
</Ads>
";
?>