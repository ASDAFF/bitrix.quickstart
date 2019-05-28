<?
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_NAME"] = "Экспорт в систему авито(\"Коммерческая недвижимость\")";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_CATEGORY"] = "Категория объявления<br><b>Возможные значения: Коммерческая недвижимость</b><br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_CATEGORY_VALUE"] = "Коммерческая недвижимость";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_OPERATIONTYPE'] = "Тип объявления<br><b>Возможные значения: Продам, Сдам<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_LOCALITY'] = "Город или населенный пункт, уточнение";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_STREET'] = "Наименование улицы, на которой<br>находится объект объявления";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_SALEROOMS'] = "Количество комнат на продажу / сдающихся<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_ROOMS'] = "Количество комнат в квартире<br>Для квартиры-студии укажите Студия.<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_SQUARE'] = "Площадь комнаты (в м.кв.)<br>Если продается/сдается несколько комнат,<br>указывается их суммарная площадь<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_FLOOR'] = "Этаж, на котором находится объект";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_FLOORS'] = "Количество этажей в доме";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_CITY'] = "Город или населенный пункт, в котором находится объект объявления<br>см. Местоположение объявления<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_BUILDINGCLASS'] = "Класс здания для офисных и складских помещений<br><b>Возможные значения:<ul>
<li>A</li>
<li>B</li>
<li>C</li>
<li>D</li>
</ul></b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_BUSINESSFORSALE'] = "Объект является готовым бизнесом.<br>
<b>Только для типа 'Продам'</b><br><b>Возможные значения:<ul>
<li>Да</li>
<li>Нет</li>
</ul></b>";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_IMAGE'] = "Изображения";
$MESS['ACRIT_EXPORTPRO_AVITO_COMMERCEREAL_FIELD_OBJECTTYPE'] = "Вид объекта<br><b>Возможные значения:<ul>
<li>Торговое помещение</li>
<li>Гостиница</li>
<li>Офисное помещение</li>
<li>Производственное помещение</li>
<li>Ресторан, кафе</li>
<li>Салон красоты</li>
<li>Складское помещение</li>
<li>Помещение свободного назначения</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>";

$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_COMMERCEREAL_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_COMMERCEREAL_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_COMMERCEREAL_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"2\">
  <Ad>
    <Id>26</Id>
    <Category>Коммерческая недвижимость</Category>
    <ObjectType>Складское помещение</ObjectType>
    <OperationType>Сдам</OperationType>
    <DateBegin>2012-05-01</DateBegin>
    <DateEnd>2012-05-14</DateEnd>
    <Region>Московская область</Region>
    <City>Жуковский</City>
    <Square>700</Square>
    <Description>Сдаётся складское помещение 700 м2 в Жуковском.
    Потолки - 12 м, стеллажи, 5 автоматич. откатных ворот, охрана,
    видеонаблюдение,пожарная сигнализация, полный комплекс услуг
    (отв. хранение, сортировка, погрузочно-разгрузочн. работы, транспорт. компания.).
    Офисные помещения. Интернет, МГТС.</Description>
    <ContactPhone>8-921-655-23-03</ContactPhone>
    <Price>294000</Price>
    <Images>
      <Image url=\"http://img.industry-soft.ru/591F0E40-2238-48DB-8F7B-4A4F3A0F2BA1.jpg\"/>
      <Image url=\"http://img.industry-soft.ru/95B5B3DA-2FCC-4126-9CD0-BA0B1A000313.jpg\"/>
    </Images>
    <AdStatus>Free</AdStatus>
  </Ad>
</Ads>
";
?>