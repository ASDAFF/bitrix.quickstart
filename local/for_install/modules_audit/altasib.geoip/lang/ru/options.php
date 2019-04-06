<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Andrew N. Popov                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
$MESS['ALTASIB_IS'] = "Магазин готовых решений для 1С-Битрикс";
//$MESS ['ALTASIB_IS'] = "Shop complete solutions for 1C-Bitrix";
$MESS['ALTASIB_GEOIP_DESCR'] = "Модуль получает местоположение пользователя по его IP, и сохраняет эти данные в cookies. <br /><br />
Для разработчиков: данные хранятся  виде сериализованного массива в переменной куки ПРЕФИКС_GEOIP,
и в виде обычного массива - в \$_SESSION[GEOIP]<br /><br />
Получить данные можно так:<br />
if(CModule::IncludeModule(\"altasib.geoip\"))<br />
{<br />
\$arData = ALX_GeoIP::GetAddr();<br />
print_r(\$arData);<br />
}<br />


";
$MESS['ALTASIB_GEOIP_SET_COOKIE'] = "Cохранять в cookies информацию о местоположении";
?>
