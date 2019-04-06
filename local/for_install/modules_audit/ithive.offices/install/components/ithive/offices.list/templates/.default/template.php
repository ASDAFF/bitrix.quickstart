<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!ereg("(.*),(.*)", $arParams["ICON_SIZE"])) $arParams["ICON_SIZE"] = '46,31';
if(!ereg("(.*),(.*)", $arParams["ICON_OFFSET"])) $arParams["ICON_OFFSET"] = '-15,-30';

if($arParams["INCLUDE_JQUERY"])
$APPLICATION->AddHeadString('
<script type="text/javascript" src="/bitrix/components/ithive/offices.list/js/jquery.js"></script>
');
$APPLICATION->AddHeadString('
<script type="text/javascript" src="http://api-maps.yandex.ru/1.1/index.xml?key='.$arParams["KEY"].'"></script>
<script type="text/javascript">
	jQuery().ready(function(){
		setTimeout(\'$("tr.result_item:nth-child(odd)").addClass("odd");$("tr.result_item").hover(function(){$(this).addClass("hover");},function(){$(this).removeClass("hover");});\',300);
		setTimeout(\'if(!$("#YMapsID").hasClass("YMaps")) $("<p></p>").appendTo("#YMapsID").addClass("dealer_invite").html("'.GetMessage("ITHIVE_OFFICES_NOTHINGS_FOUND").'").fadeIn(500);\',1000);
	});
</script>
');?>

<script type="text/javascript">
// Создание обработчика для события window.onLoad
YMaps.jQuery(function () {
	// Создание экземпляра карты и его привязка к созданному контейнеру
	var map = new YMaps.Map(YMaps.jQuery("#YMapsID")[0]);

	// Установка для карты ее центра и масштаба
	map.setCenter(new YMaps.GeoPoint(58.746114,60.223949), 3); // почти вся Россия без дальнего востока
//	map.setCenter(new YMaps.GeoPoint(<?list($lon,$lat)=explode(",",$arResult["ITEMS"][0]["PROPERTIES"]["YMAP_POINT"]["VALUE"]); echo $lat.",".$lon?>), 10);

	var toolbar = new YMaps.ToolBar([new YMaps.ToolBar.MoveButton(), new YMaps.ToolBar.MagnifierButton(), new YMaps.ToolBar.RulerButton()]);

	if (YMaps.location) {	// Если местоположение пользователя удалось определить
		place = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);

		// Создание кнопки показать все магазины
		var button_show_myplace = new YMaps.ToolBarButton({ 
			caption: "<?=GetMessage("ITHIVE_OFFICES_MY_LOCATION")?>", 
			hint: "<?=GetMessage("ITHIVE_OFFICES_SHOW_YOUR_LOCATION")?>"
		});
		
		var zoom = 10;

		// При щелчке на кнопке добавляется новая кнопка
		YMaps.Events.observe(button_show_myplace, button_show_myplace.Events.Click, function () {
			map.closeBalloon();
			if (YMaps.location.zoom) {
				zoom = YMaps.location.zoom;
			}
			map.setCenter(place, zoom);
			map.openBalloon(place, "<?=GetMessage("ITHIVE_OFFICES_YOUR_LOCATION")?>"
				+ (YMaps.location.country || "")
				+ (YMaps.location.region ? ", " + YMaps.location.region : "")
				+ (YMaps.location.city ? ", " + YMaps.location.city : "")
			)
		}, map);

		// Добавление кнопки на панель инструментов
		toolbar.add(button_show_myplace);
	}

	// Создание кнопки "Вся карта"
	var button_show_all = new YMaps.ToolBarButton({ 
		caption: "<?=GetMessage("ITHIVE_OFFICES_ENTIRE_MAP")?>", 
		hint: "<?=GetMessage("ITHIVE_OFFICES_SHOW_ALL_SHOPS")?>"
	});

	// При щелчке на кнопке устанавливается исходный центр и масштаб
	YMaps.Events.observe(button_show_all, button_show_all.Events.Click, function () {
		map.closeBalloon();
		map.setCenter(new YMaps.GeoPoint(58.746114,60.223949), 3);
	}, map);

	// Добавление кнопки "Вся карта" на панель инструментов
	toolbar.add(button_show_all);

	// Добавление панели инструментов на карту
	map.addControl(toolbar);

	// Создаем стиль
	var pin = new YMaps.Style();

	// Создаем стиль значка метки
	pin.iconStyle = new YMaps.IconStyle();
	pin.iconStyle.href = "<?=$arParams["ICON_FILE"]?>";

	pin.iconStyle.size = new YMaps.Point(<?=$arParams["ICON_SIZE"]?>);
	pin.iconStyle.offset = new YMaps.Point(<?=$arParams["ICON_OFFSET"]?>);

	// Создание группы объектов и добавление ее на карту
	var group = new YMaps.GeoObjectCollection(pin);
//	map.addControl(new YMaps.TypeControl());
	map.addControl(new YMaps.Zoom());	
	map.addControl(new YMaps.ScaleLine());
	map.enableScrollZoom();
	
<?foreach($arResult["ITEMS"] as $arItem):
$row++;
?>	group.add(createPlacemark(new YMaps.GeoPoint(<?list($lon,$lat)=explode(",",$arItem["PROPERTIES"]["YMAP_POINT"]["VALUE"]); echo $lat.",".$lon?>), 
		"<?=$arItem["PROPERTIES"]["NAME_SHORT"]["VALUE"]?> <?=$arItem["PROPERTIES"]["YMAP_PLACE"]["VALUE"]?>", 
		"<h2><?=$arItem["NAME"]?></h2><?if($arItem["PREVIEW_PICTURE"]["ID"]):?><img class='map_shop_img' src='<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>' width='<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>' height='<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>' alt='<?=$arItem["NAME"]?>' title='<?=$arItem["NAME"]?>' /><?endif;?><div class='map_shop_info'><div class='map_shop_adress'><?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"]?></div><div class='map_shop_phone'><?=$arItem["PROPERTIES"]["PHONES"]["VALUE"]?></div><div class='map_shop_anounce'><?=str_replace("\r", "", str_replace("\n", "", $arItem["PREVIEW_TEXT"]));?></div><?/*if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?><div class='map_shop_detailUrl'><a href='<?=$arItem["DETAIL_PAGE_URL"]?>'><?=GetMessage("ITHIVE_OFFICES_DETAIL")?></a></div><?endif*/?><?if($arItem["PROPERTIES"]["WWW"]["VALUE"]):?><div class='map_shop_www'><a href='<?="http://".str_replace("http://", "", $arItem["PROPERTIES"]["WWW"]["VALUE"])?>' target='_blank'><?=$arItem["PROPERTIES"]["WWW"]["VALUE"]?></a></div><?endif;?></div>", 
		'<?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"]?>', 
		'<?=$arItem["PROPERTIES"]["PHONES"]["VALUE"]?>', 
		'<?=($arItem["PROPERTIES"]["WWW"]["VALUE"]) ? "http://".str_replace("http://", "", $arItem["PROPERTIES"]["WWW"]["VALUE"]) : ""?>', 
		'<?=$arItem["DETAIL_PAGE_URL"]?>',
		'<?=$arItem["ID"]?>',
		'<?//=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?>'
	));
<?endforeach;?>

	map.addOverlay(group);

	// Создание управляющего элемента "Путеводитель по офисам"
	map.addControl(new OfficeNavigator(group));
});

// Функия создания метки
function createPlacemark (geoPoint, name, description, adress, phone, www, detailUrl, id, email) {
	var placemark = new YMaps.Placemark(geoPoint);
	placemark.name = name;
	placemark.description = description;
	placemark.adress = adress;
	placemark.phone = phone;
	placemark.www = www;
	placemark.detailUrl = detailUrl;
	placemark.id = id;
	placemark.email = email;

	return placemark;
}

// Управляющий элемент "Путеводитель по офисам", реализиует интерфейс YMaps.IControl
function OfficeNavigator (offices) {
	// Добавление на карту
	this.onAddToMap = function (map, position) {
			this.container = YMaps.jQuery(
'			<div class="scrollable"><table class="odd_grey_body" id="shop_search_result_table"></table></div>');
			this.map = map;
			this.position = position || new YMaps.ControlPosition(YMaps.ControlPosition.TOP_RIGHT, new YMaps.Size(10, 10));

			// Выставление необходимых CSS-свойств
/*			this.container.css({
				position: "absolute",
				zIndex: YMaps.ZIndex.CONTROL,
				background: '#fff',
				listStyle: 'none',
				padding: '10px',
				margin: 0
			});*/
			
			// Формирование списка офисов
			this._generateList();
			
			// Применение позиции к управляющему элементу
			this.position.apply(this.container);
			
			// Добавление на карту
			this.container.appendTo("div.shop_search_result");
	}

	// Удаление с карты
	this.onRemoveFromMap = function () {
		this.container.remove();
		this.container = this.map = null;
	};

	// Пока "летим" игнорируем клики по ссылкам
	this.isFlying = 0;

	// Формирование списка офисов
	this._generateList = function () {
		var _this = this;
		
		// Для каждого объекта вызываем функцию-обработчик
		offices.forEach(function (obj) {
			// Создание ссылки на объект
			var tr = YMaps.jQuery("<tr class='result_item'><?if(in_array("NAME_SHORT",$arParams["PROPERTY_CODE"])):?><td width='185'><?/*?><input type='hidden' id='hid_name' value='"+obj.name+"' /><input type='hidden' id='hid_id' value='"+obj.id+"' /><input type='hidden' id='hid_email' value='"+obj.email+"' /><?*/?>" + obj.name + "</td><?endif;if(in_array("ADDRESS",$arParams["PROPERTY_CODE"])):?><td>" + obj.adress + "</td><?endif;if(in_array("PHONES",$arParams["PROPERTY_CODE"])):?><td class='center' width='250'>" + obj.phone + "</td><?endif;if(in_array("WWW",$arParams["PROPERTY_CODE"])):?><td class='center' width='190'><a href='" + obj.www + "' target='_blank'>" + obj.www + "</a></td><?endif;?><?/*?><td class='center'><a class='send_mail' onclick='feedback_open($(this))' href='javascript:;'>&nbsp;</a></td><?*/?><?/*if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?><td class='center'><a href='" + obj.detailUrl + "' target='_top'><?=GetMessage("ITHIVE_OFFICES_DETAIL")?></a></td><?endif;*/?></tr>"),
				td = tr.find("td"); 
			
			// Создание обработчика щелчка по ссылке
			tr.bind("click", function () {
				if(!$(tr).hasClass("first"))
				{
					if (!_this.isFlying) {
						_this.isFlying = 1;
						_this.map.panTo(obj.getGeoPoint(), {
							flying: 1,
							callback: function () {
								if(_this.map.getZoom()<10) _this.map.setCenter(obj.getGeoPoint(),10);
								obj.openBalloon();
								_this.isFlying = 0;
							}
						});
					}
					return false;
				}
			});

			// Слушатели событий на открытие и закрытие балуна у объекта
			YMaps.Events.observe(obj, obj.Events.BalloonOpen, function () {
					$(tr).addClass("selected");
					$("div.YMaps-b-balloon-content > b").remove();
			});
			
			YMaps.Events.observe(obj, obj.Events.BalloonClose, function () {
					$(tr).removeClass("selected");
			});
			
			// Добавление ссылки на объект в общий список
			tr.appendTo(_this.container.find("#shop_search_result_table"));
		});
	};
}
</script>
    <div id="YMapsID"></div>
	<div class="clear"></div>
	<h2 class="margin-0-20-15-20"><?=GetMessage("ITHIVE_OFFICES_LIST_TITLE")?>:</h2>
	<div class="shop_search_result">
		<table class="odd_grey_head" id="shop_search_result_table">
			<tr class="result_head">
<?	
if(in_array("NAME_SHORT",$arParams["PROPERTY_CODE"])):?>				<td	width="185"><?=GetMessage("ITHIVE_OFFICES_NAME_SHORT")?></td><?endif;
if(in_array("ADDRESS",$arParams["PROPERTY_CODE"])):?>				<td><?=GetMessage("ITHIVE_OFFICES_ADDRESS")?></td><?endif;
if(in_array("PHONES",$arParams["PROPERTY_CODE"])):?>				<td	width="250"><?=GetMessage("ITHIVE_OFFICES_PHONES")?></td><?endif;
if(in_array("WWW",$arParams["PROPERTY_CODE"])):?>				<td	width="190"><?=GetMessage("ITHIVE_OFFICES_WWW")?></td><?endif;
/*if(in_array("EMAIL",$arParams["PROPERTY_CODE"])):?>				<td><a href="javascript:;" style="padding-right:4px;" class="send_mail">&nbsp;</a></td><?endif;*/?>
<?/*				<td class="center"><?=GetMessage("ITHIVE_OFFICES_DETAIL")?></td>*/?>
			</tr>
		</table>
	</div>