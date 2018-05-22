<?
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_NAME"] = "Экспорт в систему авито (Мебель и интерьер)";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_ID"] = "Уникальный идентификатор объявления<br>(строка не более 100 символов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_ADSTATUS"] = "Статус объявления. Возможные значения:<br><b>Free, Premium, VIP, Highlight, PushUp, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_ALLOWEMAIL"] = "Возможность написать сообщение по объявлению через сайт — одно из значений списка: Да, Нет. Примечание: значение по умолчанию — Да.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_MANAGERNAME"] = "Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_CONTACTPHONE"] = "Контактный телефон, если не указан,<br>подставляется из данных клиента.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_REGION"] = "Город или населенный пункт,<br>в котором находится объект объявления<br>(Значение из Справочника городов)<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_CITY"] = "Город или населенный пункт, в котором находится объект объявления — в соответствии со значениями из справочника.<br>
Элемент обязателен для всех регионов, кроме Москвы и Санкт-Петербурга.<br>
Справочник является неполным. Если требуемое значение в нем отсутствует, то укажите ближайший к вашему объекту пункт из справочника, а точное название населенного пункта — в элементе Street.";

$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_DISTRICT"] = "Район города — в соответствии со значениями из справочника.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_CATEGORY"] = "Категория — строка Мебель и интерьер.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_GOODSTYPE"] = "Вид товара — одно из значений списка:<br><br>
Компьютерные столы и кресла,<br>
Кровати, диваны и кресла,<br>
Кухонные гарнитуры,<br>
Освещение,<br>
Подставки и тумбы,<br>
Предметы интерьера, искусство,<br>
Столы и стулья,<br>
Текстиль и ковры,<br>
Шкафы и комоды,<br>
Другое.";


$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_TITLE"] = "Название объявления — строка до 50 символов.<br><br>Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля; и не используйте слово продам.";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_DESCRIPTION"] = "Описание ";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_PRICE"] = "Цена в рублях";
$MESS["ACRIT_EXPORTPRO_AVITO_FURNITURE_FIELD_IMAGE"] = "Изображения";

$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_FURNITURE_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_FURNITURE_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_FURNITURE_EXAMPLE"] = "
<?xml version=\"1.0\"?>
<Ads formatVersion=\"3\" target=\"Avito.ru\">
    <Ad>
        <Id>mebel_i_interer001</Id>
        <DateBegin>2015-11-27</DateBegin>
        <DateEnd>2079-08-28</DateEnd>
        <AdStatus>TurboSale</AdStatus>
        <AllowEmail>Да</AllowEmail>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
        <District>Ленинский</District>
        <Category>Мебель и интерьер</Category>
        <GoodsType>Столы и стулья</GoodsType>
        <Title>Комплект мебели ручной работы</Title>
        <Description>Франция, дуб, ручная работа, 1850-1860 гг. 
Викторианский стиль.
Длина 157 см - раздвинутый, 110 см - сдвинутый, ширина - 125 см, высота - 73 см</Description>
        <Price>25000</Price>
        <Images>
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg\" />
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg\" />
        </Images>
    </Ad>
    <Ad>
        <Id>mebel_i_interer003</Id>
        <AdStatus>Free</AdStatus>
        <AllowEmail>Нет</AllowEmail>
        <Region>Москва</Region>
        <Category>Мебель и интерьер</Category>
        <GoodsType>Кровати, диваны и кресла</GoodsType>
        <Title>Кровать ручной работы</Title>
        <Description>Тестовое объявление. Кровать ручной работы под заказ не дорого. Кожзам и цвет на ваше усмотрение.</Description>
    </Ad>
<Ad>
        <Id>mebel_i_interer005</Id>
        <AdStatus>Free</AdStatus>
        <AllowEmail>Нет</AllowEmail>
        <Region>Санкт-Петербург</Region>
        <Category>Мебель и интерьер</Category>        
        <GoodsType>Кухонные гарнитуры</GoodsType>        
        <Title>Megaros duca d\"este кухня Италия</Title>
        <Description>Тестовое объявление. Предлагаем купить у нас кухню .
Ознакомьтесь с характеристиками мебели ниже:
* Фабрика:    Megaros
* Стиль:    Классика
* Уровень цен*:    Высокий
* делимся дилерской скидкой
* просчет и оплата в солидном салоне
* своя транспортная компания.
Распродажа новой итальянской и испанской мебели после закрытия элитного мебельного салона! Оригинал 100%! Цены значительно ниже рынка!
Абсолютно новая элитная кухня (кухонный гарнитур) Vismap Nicole, сделано в Италии.
Фасады глянцевый лак желтого цвета, столешница Corian by DuPont, черная с блестками, интегрированная мойка на две чаши, барная стойка вторым ярусом.
Кухонный гарнитур соответствует представленному на схеме.
Размеры кухни: левая стена 182 см., самая длинная стена 364 см., правая часть 273 см., передняя часть 216 см.
Возможна доставка.
В наличии мебель элитных фабрик Италии и Испании по очень низким ценам! Звоните и заходите на наш сайт!
</Description>
        <Price>2500000</Price>
    </Ad>        
</Ads>
";
?>