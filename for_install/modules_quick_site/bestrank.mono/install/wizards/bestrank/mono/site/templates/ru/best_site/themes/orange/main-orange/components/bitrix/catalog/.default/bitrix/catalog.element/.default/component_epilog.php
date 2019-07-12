<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");


	function ShowArticle(){
		global $APPLICATION;
		$str = "<div class=\"article\"></div>";
		return $str;
	}
	function ShowRating(){
		global $APPLICATION;
		$str = "<div class=\"rating\"></div>";
		return $str;
	}


$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/js/fancybox/jquery.fancybox-1.3.1.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/js/fancybox/jquery.fancybox-1.3.1.css');

$arResult = $arResult["ELEMENT"];


$filter_name = ($arParams["USE_FILTER"]=="Y" && strlen($arParams["FILTER_NAME"])>0) ? $arParams["FILTER_NAME"] : "arrFilter";
$arXMLIDs=array();
foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty) {
	//echo "<pre>";print_r($arProperty); echo "</pre>";


	if(is_array($arProperty["DISPLAY_VALUE"])){
		$display_value=array();
		foreach($arProperty["DISPLAY_VALUE"] as  $k=>$v){
			if(is_array($arProperty["VALUE"])){
				$display_value[] =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($arProperty["VALUE"][$k]))), $v);
			}else{
				$display_value[] =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($arProperty["VALUE"]))), $v);
			}
		}

	} else {
		if(is_array($arProperty["VALUE"])){
			$display_value=array();
			foreach($arProperty["VALUE"] as  $k=>$v)
				$display_value[] =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($v))), $arProperty["DISPLAY_VALUE"]);
		}
		else {
			$display_value =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($arProperty["VALUE"]))), $arProperty["DISPLAY_VALUE"]);
		}
	}

	$arResult["DISPLAY_PROPERTIES"][$pid]["DISPLAY_VALUE"]=$display_value;
	//echo "<pre>";print_r($display_value); echo "</pre>";

	if($arProperty["USER_TYPE"]=="ElementXmlID") {
		if(is_array($arProperty["VALUE"]))
			$arXMLIDs=array_merge($arXMLIDs, $arProperty["VALUE"]);
		else
			$arXMLIDs[]=$arProperty["VALUE"];
	}
}


//echo "<pre>"; print_r($arResult); echo "</pre>";

	if($arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]){?>

		<script type="text/javascript">
		$(document).ready(function() {
			$('.article').html('<?=$arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["NAME"]?>: <?=$arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["VALUE"]?>');
		});
		</script>


	<?}

?>


		<div id="ratings">
 
<?$APPLICATION->IncludeComponent("bitrix:iblock.vote", "stars", Array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],	// Тип инфоблока
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],	// Инфоблок
	"ELEMENT_ID" => $arResult["ID"],	// ID элемента
	"ELEMENT_CODE" => "",	// Код элемента
	"MAX_VOTE" => "5",	// Максимальный балл
	"VOTE_NAMES" => array(	// Подписи к баллам
		0 => "1",
		1 => "2",
		2 => "3",
		3 => "4",
		4 => "5",
	),
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"DISPLAY_AS_RATING" => $arParams["VOTE_DISPLAY_AS_RATING"],	// В качестве рейтинга показывать
	"CACHE_TYPE" => $arParams["CACHE_TYPE"],	// Тип кеширования
	"CACHE_TIME" => $arParams["CACHE_TIME"],	// Время кеширования (сек.)
	),
	false,
	array(
	"HIDE_ICONS" => "N"
	)
);?>

		</div>


		<script type="text/javascript">
		$(document).ready(function() {
			$('.rating').html($('#ratings').html());
			$('#ratings').html('');
		});
		</script>



<!--komplektaciya-->
<?
$arPropertyKompl = $arResult["DISPLAY_PROPERTIES"]["komplektaciya"];
unset($arResult['DISPLAY_PROPERTIES']["komplektaciya"]);
?>
<!-- recommend -->
<?
$arPropertyRecommend = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"];
unset($arResult['DISPLAY_PROPERTIES']["RECOMMEND"]);
?>
<?if(count($arPropertyRecommend["DISPLAY_VALUE"]) > 0):?>
		<script type="text/javascript">
		$(document).ready(function() {
			$('.recommend').html($('#recommends').html());
			$('#recommends').html('');
			$('#details').css('margin-right', '290px');
			$('.props').css('width', '262px');
			$('.recommend').css('display', 'block');
			$('#details').prepend($('#headers1').html());
			$('#headers1').html('');
		});
		</script>

<div id="recommends">

	<div class="actions">
	<h5><?=$arPropertyRecommend["NAME"]?></h5>
		<?
		global $arRecPrFilter;
		$arRecPrFilter["XML_ID"] = $arPropertyRecommend["VALUE"];
		$APPLICATION->IncludeComponent("bitrix:catalog.section", "recommend", Array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],	// Тип инфоблока
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],	// Инфоблок
	"SECTION_ID" => "",	// ID раздела
	"SECTION_CODE" => "",	// Код раздела
	"SECTION_USER_FIELDS" => array(	// Свойства раздела
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",	// По какому полю сортируем элементы
	"ELEMENT_SORT_ORDER" => "desc",	// Порядок сортировки элементов
	"ELEMENT_SORT_FIELD2" => "id",	// Поле для второй сортировки элементов
	"ELEMENT_SORT_ORDER2" => "desc",	// Порядок второй сортировки элементов
	"FILTER_NAME" => "arRecPrFilter",	// Имя массива со значениями фильтра для фильтрации элементов
	"INCLUDE_SUBSECTIONS" => "A",	// Показывать элементы подразделов раздела
	"SHOW_ALL_WO_SECTION" => "Y",	// Показывать все элементы, если не указан раздел
	"HIDE_NOT_AVAILABLE" => "N",	// Не отображать товары, которых нет на складах
	"PAGE_ELEMENT_COUNT" => count($arPropertyRecommend["DISPLAY_VALUE"]),	// Количество элементов на странице
	"LINE_ELEMENT_COUNT" => "3",	// Количество элементов выводимых в одной строке таблицы
	"PROPERTY_CODE" => array(	// Свойства
		0 => "",
		1 => "",
	),
	"OFFERS_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"OFFERS_SORT_FIELD" => "sort",
	"OFFERS_SORT_ORDER" => "asc",
	"OFFERS_SORT_FIELD2" => "id",
	"OFFERS_SORT_ORDER2" => "desc",
	"OFFERS_LIMIT" => "5",	// Максимальное количество предложений для показа (0 - все)
	"SECTION_URL" => "",	// URL, ведущий на страницу с содержимым раздела
	"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
	"BASKET_URL" => $arParams["BASKET_URL"],	// URL, ведущий на страницу с корзиной покупателя
	"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],	// Название переменной, в которой передается действие
	"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],	// Название переменной, в которой передается код товара для покупки
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// Название переменной, в которой передается количество товара
	"PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// Название переменной, в которой передается код группы
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => $arParams["CACHE_TIME"],	// Время кеширования (сек.)
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"META_KEYWORDS" => "-",	// Установить ключевые слова страницы из свойства
	"META_DESCRIPTION" => "-",	// Установить описание страницы из свойства
	"BROWSER_TITLE" => "-",	// Установить заголовок окна браузера из свойства
	"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
	"DISPLAY_COMPARE" => "N",	// Выводить кнопку сравнения
	"SET_TITLE" => "N",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
	"PRICE_CODE" => array(	// Тип цены
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",	// Использовать вывод цен с диапазонами
	"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],	// Выводить цены для количества
	"PRICE_VAT_INCLUDE" => "N",	// Включать НДС в цену
	"PRODUCT_PROPERTIES" => "",	// Характеристики товара
	"USE_PRODUCT_QUANTITY" => "N",	// Разрешить указание количества товара
	"CONVERT_CURRENCY" => "N",	// Показывать цены в одной валюте
	"OFFERS_CART_PROPERTIES" => "",
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "N",	// Выводить под списком
	"PAGER_TITLE" => "Товары",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
	"DISPLAY_IMG_WIDTH" => 80,	// Ширина изображения для превью
	"DISPLAY_IMG_HEIGHT" => 100,	// Высота изображения для превью
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);
		?>
	</div>
</div>
<?endif;?>


<div class="tabsblock">
	<div class="tabs">
		<?if ($arResult["DETAIL_TEXT"]):?>
		<a href="#" id="tab1" class="active"><span><?=GetMessage("CATALOG_FULL_DESC")?></span><span class="clr"></span></a>
		<?endif?>
		<?if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):
		unset($arResult['DISPLAY_PROPERTIES']["MORE_PHOTO"]);
		if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):?>
			<a href="#" id="tab2" class="<?=(!$arResult["DETAIL_TEXT"] ? 'active' : '')?>"><span><?=GetMessage("CATALOG_PROPERTIES")?></span><span class="clr"></span></a>
			<?endif?>
		<?endif?>
		<?if($arPropertyKompl){?>
		<a href="#" id="tab4" class="<?=(!$arResult["DETAIL_TEXT"]&&(!is_array($arResult['DISPLAY_PROPERTIES'] || count($arResult['DISPLAY_PROPERTIES']))== 0 ) ? 'active' : '')?>"><span><?=GetMessage("KOMPL")?></span><span class="clr"></span></a>
		<?}?>
		<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $arResult["ID"]):?>
		<a href="#" id="tab3" class="<?=(!$arResult["DETAIL_TEXT"]&&(!is_array($arResult['DISPLAY_PROPERTIES'] || count($arResult['DISPLAY_PROPERTIES']))== 0 )&&!$arPropertyKompl ? 'active' : '')?>"><span><?=GetMessage("CATALOG_REVIEWS")?></span><span class="clr"></span></a>
		<?endif?>
	</div>
	<div class="tabcontent">
		<?if($arResult["DETAIL_TEXT"]):?>
			<div class="cnt active detail_descr">
				<?echo $arResult["DETAIL_TEXT"];?>
			</div>
		<?endif?>

		<?if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):?>
		<div class="cnt <?=(!$arResult["DETAIL_TEXT"] ? 'active' : '')?>">
			<ul class="options">
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<li>
					<span><?=$arProperty["NAME"]?>:</span><b><?
					if(is_array($arProperty["DISPLAY_VALUE"])):
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					elseif($pid=="MANUAL"):
						?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
					else:
						echo $arProperty["DISPLAY_VALUE"];?>
						<?endif?></b>
				</li>
				<?endforeach?><li style="clear: both; float:none; height:0; background: none; border: none;"></li>
			</ul>
		</div>
		<?endif?>
		<?if($arPropertyKompl){?>
		<div class="cnt <?=(!$arResult["DETAIL_TEXT"]&&(!is_array($arResult['DISPLAY_PROPERTIES'] || count($arResult['DISPLAY_PROPERTIES']))== 0 ) ? 'active' : '')?>">
			<p><?=$arPropertyKompl["VALUE"]?></p>
		</div>
		<?}?>
		<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $arResult["ID"]):?>
		<div  class="cnt <?=(!$arResult["DETAIL_TEXT"]&&(!is_array($arResult['DISPLAY_PROPERTIES'] || count($arResult['DISPLAY_PROPERTIES']))== 0 )&&!$arPropertyKompl ? 'active' : '')?>">
			<?$APPLICATION->IncludeComponent(
			"bitrix:forum.topic.reviews",
			"",
			Array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
				"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
				"FORUM_ID" => $arParams["FORUM_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
				"SHOW_RATING" => "N",
				"SHOW_MINIMIZED" => "Y",
			),
			false
		);?>
		</div>
		<?endif?>
	</div>
</div>




<?if (CModule::IncludeModule('sale'))
{
	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"PRODUCT_ID" => $arResult['ID'],
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
		),
		false,
		false,
		array()
	);

	if ($arBasket = $dbBasketItems->Fetch())
	{
		$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
		$arNotify = array();
		if (strlen($notifyOption) > 0)
			$arNotify = unserialize($notifyOption);
		if($arBasket["DELAY"] == "Y")
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_CART_DELAY")."')});</script>\r\n";
		elseif ($arNotify[SITE_ID]['use'] == 'Y' && $arBasket["SUBSCRIBE"] == "Y")
			echo "<script type=\"text/javascript\">$(function() {disableAddToSubscribe('catalog_add2cart_link', '".GetMessage("CATALOG_IN_SUBSCRIBE")."')});</script>\r\n";
		elseif($arResult["CAN_BUY"] == "N"  && $arBasket["SUBSCRIBE"] == "N")
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_CART")."')});</script>\r\n";
	}
}

if ($arParams['USE_COMPARE'])
{
	if (isset(
		$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arResult['ID']]
	))
	{
		echo '<script type="text/javascript">$(function(){disableAddToCompare(BX(\'catalog_add2compare_link\'), \'detail\', \''.GetMessage("CATALOG_IN_COMPARE").'\', \'\');})</script>';
	}
}
?>