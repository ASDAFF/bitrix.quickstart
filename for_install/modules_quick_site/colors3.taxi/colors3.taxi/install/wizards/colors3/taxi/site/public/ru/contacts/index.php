<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords_inner", "Контакты");
$APPLICATION->SetPageProperty("title", "Контакты");
$APPLICATION->SetPageProperty("keywords", "Контакты");
$APPLICATION->SetPageProperty("description", "Контакты");
$APPLICATION->SetTitle("Контакты");
$APPLICATION->SetPageProperty("CONTACTS_PAGE_TPL", "YES");
?> 
<h1><?=$APPLICATION->GetTitle('title')?></h1>
 
<h3>Заказ такси: </h3>
 
<ul> 
  <li> (3412) 123-123 (многоканальный) </li>
 
  <li> (3412) 321-321 (многоканальный) </li>
 
  <li> (3412) 311-111 (экстренный вызов)</li>
 </ul>
 
<p> Для обратного звонка на&nbsp;мобильный телефон дождитесь первого гудка и&nbsp;положите трубку</p>
 
<h3>Офис </h3>
 
<ul> 
  <li> <nobr>Тел: (3412) 90-41-93</nobr> </li>
 
  <li>Email: <a href="mailto:tatyana@udmtaxi.ru" ><span>tatyana@123taxi.ru</span></a></li>
 
  <li>Факс: (3412) 90-41-90 </li>
 </ul>
 
<h3>Фактический адрес:</h3>
 
<p> г.&nbsp;Ижевск, ул.&nbsp;Лихвинцева, д. 49, &laquo;Такси 123&raquo;</p>
 
<h2>Схема проезда </h2>
 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	".default",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:3:{s:10:\"yandex_lat\";s:7:\"55.7383\";s:10:\"yandex_lon\";s:7:\"37.5946\";s:12:\"yandex_scale\";i:10;}",
		"MAP_WIDTH" => "100%",
		"MAP_HEIGHT" => "500",
		"CONTROLS" => array(0=>"ZOOM",1=>"MINIMAP",2=>"TYPECONTROL",3=>"SCALELINE",),
		"OPTIONS" => array(0=>"ENABLE_DBLCLICK_ZOOM",1=>"ENABLE_DRAGGING",),
		"MAP_ID" => ""
	)
);?></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>