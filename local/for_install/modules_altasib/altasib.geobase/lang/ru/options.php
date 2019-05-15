<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2015 ALTASIB
 */ 

$CookiePX = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");

$MESS['ALTASIB_IS'] = "Магазин готовых решений для 1С-Битрикс";
$MESS['ALTASIB_GEOBASE_DESCR'] = 'Модуль получает местоположение пользователя по его IP-адресу и сохраняет эти данные в сессию и, если установлено, в cookies.<br/><br/>
<b>Информация для разработчиков</b><br/>
Данные хранятся в cookies в виде JSON-закодированных массивов: '.$CookiePX.'_ALTASIB_GEOBASE и '.$CookiePX.'_ALTASIB_GEOBASE_CODE,
и в виде обычных массивов: $_SESSION["ALTASIB_GEOBASE"] и $_SESSION["ALTASIB_GEOBASE_CODE"] - автоопределенный и указанный пользователем соответственно.
<br/><br/><div id="altasib_description_open_btn">
	<span class="altasib_description_open_text">Читать дальше</span>
</div>
<div id="altasib_description_full">
Получить данные можно так:
<pre>
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetAddres();
	print_r($arData);
}
// для получения данных КЛАДР, определенных автоматически по местоположению:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetCodeByAddr();
	print_r($arData);
}
// для получения данных КЛАДР, заданных пользователем:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetDataKladr();
	print_r($arData);
}
// для получения местоположения из <a href="/bitrix/admin/sale_location_admin.php">списка местоположений</a>, установленных на сайте:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetBXLocations();
	print_r($arData);
}
// Взять данные из cookies:
$arDataC = CAltasibGeoBase::deCodeJSON($APPLICATION->get_cookie("ALTASIB_GEOBASE_CODE"));
print_r($arDataC);
</pre>
События модуля:<br/><br/>
<table class="internal" width="100%">
	<tbody>
		<tr>
			<th>Событие</th>
			<th>Вызывается</th>
			<th>Метод</th>
			<th>С версии</th>
		</tr>
		<tr>
			<td>OnAfterSetSelectCity</td>
			<td>После выбора города пользователем</td>
			<td>CAltasibGeoBase::SetCodeKladr
			<br/>CAltasibGeoBase::SetCodeMM</td>
			<td>1.1.3</td>
		</tr>
	</tbody>
</table>
<br/>
Также имеется js-событие <b>onAfterSetCity</b>, вызываемое после выбора (установки) города пользователем.<br/>Входные параметры обработчика события: 
(string name, string id, string full_name, string data);<br/>
<pre>// Пример перезагрузки страницы по выбору города:
BX.addCustomEvent("onAfterSetCity", function(city, city_id, full_name){
    location.reload();
});
</pre><br/><br/>
<div id="altasib_description_close_btn">
	<span class="altasib_description_open_text">Свернуть</span>
</div>
</div>
';
$MESS['ALTASIB_GEOBASE_SET_COOKIE']			= "Сохранять в cookies информацию о местоположении:";
$MESS['ALTASIB_GEOBASE_SET_TIMEOUT']		= "Время выполнения скрипта (с):";
$MESS['ALTASIB_TAB_BD_DATA']				= "Данные";
$MESS['ALTASIB_TAB_TITLE_DATA']				= "Источники определения местоположения, поддерживаемые модулем";
$MESS['ALTASIB_GEOBASE_DB_UPDATE_IPGEOBASE'] = "Обновление баз данных модуля";
$MESS['ALTASIB_TAB_BD_CITIES']				= "Избранные города";
$MESS['ALTASIB_TAB_TITLE_DB_CITIES']		= "Добавление и редактирование списка избранных городов модуля";
$MESS['ALTASIB_TITLE_LOAD_FILE']			= "Загрузка архива:";
$MESS['ALTASIB_TITLE_UNPACK_FILE']			= "Распаковка архива:";
$MESS['ALTASIB_TITLE_DB_UPDATE']			= "Обновление базы данных (<a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>):";
$MESS['ALTASIB_NOTICE_UPDATE_AVAILABLE']	= "Доступен обновленный архив данных с сайта <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>.";
$MESS['ALTASIB_NOTICE_UPDATE_NOT_AVAILABLE'] = "Доступных обновлений на сайте <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> нет.";
$MESS['ALTASIB_NOTICE_DBUPDATE_SUCCESSFUL']	= "Обновление базы данных с сайта <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> успешно завершено.";
$MESS['ALTASIB_GEOBASE_GET_UPDATE']			= "Проверять наличие обновлений архивов БД местоположений на сайте <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> автоматически:";
$MESS["ALTASIB_NOTICE_UPDATE_MANUAL_MODE"]	= "Для проверки обновлений с сайта <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> нажмите кнопку \"Проверить обновления\"";

$MESS["ALTASIB_CHECK_UPDATES"]				= "Проверка наличия обновлений на сайте <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>...";
$MESS["ALTASIB_GEOBASE_SOURCE"]				= "Источник определения местоположения:";
$MESS["ALTASIB_GEOBASE_NOT_USING"]			= "Не использовать локальные БД";
$MESS["ALTASIB_GEOBASE_LOCAL_DB"]			= "Локальная база ipgeobase.ru";
$MESS["ALTASIB_GEOBASE_STATISTIC"]			= "Веб-аналитика 1С-Битрикс";
$MESS["ALTASIB_GEOBASE_SOURCE_MM"]			= "Локальная база maxmind.com";
$MESS["ALTASIB_GEOBASE_IPGEOBASE_MM"]		= "Локальные базы ipgeobase.ru и maxmind.com";

$MESS["ALTASIB_GEOBASE_UPDATE"]				= "Обновить";
$MESS["ALTASIB_GEOBASE_CHECK_UPDATE"]		= "Проверить обновления";

$MESS["ALTASIB_GEOBASE_WIN_YOUR_CITY_ENABLE"]	= "Включить <b>автоматический показ</b> всплывающего окна \"Ваш город\":";
$MESS["ALTASIB_GEOBASE_ONLY_SELECT_CITIES"]		= 'Использовать только города из <a title="Избранные города" onclick="tabControl.SelectTab(\'edit3\'); return false;">списка избранных городов</a>, без поля поиска:';


$MESS['ALTASIB_TITLE_CITIES_LIST']	= "Список городов";
$MESS['ALTASIB_TABLE_CITY_DELETE']	= "Удалить";
$MESS['ALTASIB_TABLE_CITY_ADD']		= "Добавить";
$MESS['ALTASIB_INP_CITY_ADD']		= "Добавление города в список избранных городов:";
$MESS['ALTASIB_INP_ENTER_CITY']		= "Введите название города";
$MESS['ALTASIB_TABLE_CITY_NAME']	= "Имя города";
$MESS['ALTASIB_TABLE_CITY_CODE']	= "Код н/п";
$MESS['ALTASIB_TABLE_DISTRICT']		= "Район";
$MESS['ALTASIB_TABLE_REGION']		= "Регион";
$MESS['ALTASIB_TABLE_COUNTRY_CODE']	= "Код страны";
$MESS['ALTASIB_TABLE_COUNTRY']		= "Cтрана";
$MESS['ALTASIB_TABLE_CITY_ACT']		= "Действие";
$MESS['ALTASIB_GEOBASE_AUTO_DISPLAY']	= "Автоматический показ";
$MESS['ALTASIB_GEOBASE_GLOBAL_COMPONENTS']	= "Общие настройки компонентов";
$MESS['ALTASIB_GEOBASE_LOCATIONS']	= "Настройки замены местоположения в модуле Интернет-магазин";
$MESS['ALTASIB_GEOBASE_YOUR_CITY_DESCR'] = "\"Ваш город\" - компонент, выводящий всплывающее окно с возможностью подтверждения города посетителя, определенного по его IP адресу, а также ссылку для изменения";
$MESS['ALTASIB_GEOBASE_YOUR_CITY_TEMPLATES'] = "Шаблон компонента \"Ваш город\", подключаемого автоматически:";
$MESS['ALTASIB_GEOBASE_POPUP_BACK'] = "Затемнять фон при выводе всплывающих окон:";
$MESS['ALTASIB_GEOBASE_REGION_DISABLE'] = "Не выводить названия региона и района:";


$MESS['ALTASIB_GEOBASE_SELECT_CITY_DESCR'] = "\"Выбор города\" - компонент, выводящий ссылку на открытие всплывающего окна для выбора и сохранения города посетителя";
$MESS['ALTASIB_GEOBASE_SELECT_CITY_TEMPLATES'] = "Шаблон компонента \"Выбор города\", подключаемый при вызове компонента автоматически:";

$MESS['ALTASIB_GEOBASE_ONLINE_ENABLE'] = "Использовать онлайн сервисы <a href='http://ipgeobase.ru/' target='_blank' title='География российских и украинских IP-адресов. Поиск местонахождения (города) IP-адреса'>ipgeobase.ru</a> и <a href='http://geoip.elib.ru/' target='_blank' title='Определение географических координат по IP адресу'>geoip.elib.ru</a>:";

$MESS ['ALTASIB_GEOBASE_SITES'] = "Cайты, на страницах которых задействовать автоматический показ:";
$MESS ['ALTASIB_GEOBASE_TEMPLATE'] = "Шаблоны сайта, в которых задействовать автопоказ:";

$MESS['ALTASIB_GEOBASE_SECTION_LINK'] = "В каких разделах заменять строку \"Местоположение\" для страницы оформления заказа (список через запятую, пустое значение означает 'отображать без ограничений')<br />Пример: <i>'/personal/order/make/, /personal/'</i>";
$MESS['ALTASIB_GEOBASE_SALE_LOCATION'] = "Местоположение страны по умолчанию:";
$MESS['ALTASIB_GEOBASE_URL_NOT_FOUND'] = "Запрашиваемый URL адрес удаленного сервера не найден.";
$MESS['ALTASIB_GEOBASE_SET_SQL'] = "Добавлять к длинным SQL-запросам строку \"SET SQL_BIG_SELECTS=1\":";
$MESS['ALTASIB_GEOBASE_RUSSIA'] = "Россия";
$MESS['ALTASIB_GEOBASE_RF'] = "Российская Федерация";

$MESS['ALTASIB_GEOBASE_JQUERY'] = "Подключать jQuery:";
$MESS['ALTASIB_GEOBASE_JQUERY_NOT'] = "На сайте уже подключен jQuery";
$MESS['ALTASIB_GEOBASE_JQUERY_YES'] = "Да, подключать";

$MESS['ALTASIB_GEOBASE_FIELD_LOC_IND'] = "Идентификатор элемента поля ввода местоположения физического лица на странице оформления заказа:";
$MESS['ALTASIB_GEOBASE_FIELD_LOC_LEG'] = "Идентификатор элемента поля ввода местоположения юридического лица на странице оформления заказа:";

$MESS['ALTASIB_NOTICE_MM_UPDATE_AVAILABLE'] = "Доступен обновленный архив данных GeoLite с сайта <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a>.";
$MESS['ALTASIB_NOTICE_MM_UPDATE_NOT_AVAILABLE'] = "Доступных обновлений на сайте <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> не найдено.";
$MESS['ALTASIB_NOTICE_MM_DBUPDATE_SUCCESSFUL'] = "Обновление файла базы данных GeoLite с сайта <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> успешно завершено.";
$MESS['ALTASIB_GEOBASE_MM_GET_UPDATE'] = "Проверять наличие обновлений GeoLite базы на сайте <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> автоматически:";
$MESS["ALTASIB_NOTICE_MM_UPDATE_MANUAL_MODE"] = "Для проверки обновлений с сайта <a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a> нажмите кнопку \"Проверить обновления\"";

$MESS['ALTASIB_TITLE_MM_DB_UPDATE'] = "Обновление базы данных (<a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a>):";
$MESS["ALTASIB_CHECK_MM_UPDATES"] = "Проверка наличия обновлений на сайте <a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a>...";

$MESS['ALTASIB_GEOBASE_DEMO_MODE'] = "Модуль работает в демонстрационном режиме. <a target='_blank' href='http://marketplace.1c-bitrix.ru/tobasket.php?ID=#MODULE#'>Купить версию без ограничений</a>";
$MESS['ALTASIB_GEOBASE_DEMO_EXPIRED'] = "Демонстрационный период работы модуля закончился. <a target='_blank' href='http://marketplace.1c-bitrix.ru/tobasket.php?ID=#MODULE#'>Купить модуль</a>";
$MESS['ALTASIB_GEOBASE_NF'] = "Модуль #MODULE# не найден";

$MESS['ALTASIB_GEOBASE_AUTODETECT_EN'] = "Добавлять автоматически определенный город к списку избранных городов:";
$MESS['ALTASIB_GEOBASE_CITIES_WORLD_ENABLE'] = "Показывать города мира в строке поиска компонента \"Выбор города\":";
?>