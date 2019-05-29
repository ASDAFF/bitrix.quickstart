<?
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_NAME"] = "Экспорт в систему авито(\"Гаражи и стоянки\")";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_CATEGORY"] = "Категория объявления<br><b>Возможные значения: Гаражи и стоянки</b><br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_REGION"] = "Дата конца экспозиции объявления<br>(Значение из Справочника регионов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_SUBWAY"] = "Станция метро<br>(Значение из Cправочника метро)";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_GARAGE_CATEGORY_VALUE"] = "Гаражи и стоянки";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_OPERATIONTYPE'] = "Тип объявления<br><b>Возможные значения: Продам, Сдам<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_LOCALITY'] = "Город или населенный пункт, уточнение";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_STREET'] = "Наименование улицы, на которой<br>находится объект объявления";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_SQUARE'] = "Площадь в кв. м.<br><b class='required'>Обязательный элемент</b>";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_CITY'] = "Город или населенный пункт, в котором находится объект объявления<br>см. Местоположение объявления<br><b class='required'>Обязательный элемент</b>";

$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_IMAGE'] = "Изображения";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_OBJECTTYPE'] = "Вид объекта<br><b>Возможные значения:<ul>
<li>Гараж</li>
<li>Машиноместо</li>
</ul></b>
<br><b class='required'>Обязательный элемент</b>";

$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_OPERATIONTYPE_VALUE'] = "Продам";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_OBJECTSUBTYPE'] = "Тип гаража,<br> только если вид объекта — Гараж<br><b>Возможные значения:<ul>
<li>Железобетонный</li>
<li>Кирпичный</li>
<li>Металлический</li>
</ul></b>";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_OBJECTSUBTYPEMACHINE'] = "Тип машиноместа,<br> только если вид объекта — Машиноместо<br><b>Возможные значения:<ul>
<li>Многоуровневый паркинг</li>
<li>Подземный паркинг</li>
<li>Крытая стоянка</li>
<li>Открытая стоянка</li>
</ul></b>";
$MESS['ACRIT_EXPORTPRO_AVITO_GARAGE_FIELD_SECURED'] = "Охраняемое место или гараж";

$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GARAGE_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GARAGE_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_GARAGE_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads target=\"Avito.ru\" formatVersion=\"2\">
    <Ad>
        <Id>51</Id>
        <Category>Гаражи и стоянки</Category>
        <OperationType>Сдам</OperationType>
        <ObjectType>Гараж</ObjectType>
        <ObjectSubtype>Железобетонный</ObjectSubtype>
        <DateBegin>2012-05-01</DateBegin>
        <DateEnd>2012-05-14</DateEnd>
        <Region>Санкт-Петербург</Region>
        <Subway>Парк Победы</Subway>
        <Street>Витебский 16</Street>
        <Square>20</Square>
        <Secured>Да</Secured>
        <Description>
          Сдам бетонный гараж.
          Отделка вагонкой, вентиляция, 220в, открытый стеллаж, хорошая охрана,
          на подъездах везде ровный асфальт, зимой регулярная уборка снега.
          Состояние гаража - отличное.
        </Description>
        <ContactPhone>8 911 521-89-14</ContactPhone>
        <Price>2000</Price>
        <Images>
            <Image url=\"http://img.industry-soft.ru/591F0E40-2238-48DB-8F7B-4A4F3A0F2BA1.jpg\"/>
            <Image url=\"http://img.industry-soft.ru/95B5B3DA-2FCC-4126-9CD0-BA0B1A000313.jpg\"/>
        </Images>
        <AdStatus>Premium</AdStatus>
    </Ad>
</Ads>
";
?>