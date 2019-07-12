<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мягкая мебель от производителя: диваны, кровати, угловые диваны, мебель в Санкт-Петербурге");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Мягкая мебель от производителя: диваны, кровати, угловые диваны, мебель в Санкт-Петербурге");
?> 
<div style="text-align: center; width: 50%; margin-bottom: 20px; float: left; margin-right: 20px; "> 
  <div><strong>Новая модель: </strong></div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:photo.random",
	"main",
	Array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCKS" => "#CATALOG_IBLOCK_ID#",
		"PARENT_SECTION" => "#SECTION_NEWPROD_ID#",
		"DETAIL_URL" => "#SITE_DIR#/catalog/#SECTION_NEWPROD_ID#/#ELEMENT_ID#/",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "180",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y"
	)
);?> </div>
 
<div style="text-align: center; margin-bottom: 20px; float: left; "> 
  <div><strong>Хит продаж: </strong></div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:photo.random",
	"main",
	Array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCKS" => "#CATALOG_IBLOCK_ID#",
		"PARENT_SECTION" => "#SECTION_HIT_ID#",
		"DETAIL_URL" => "#SITE_DIR#/catalog/#SECTION_HIT_ID#/#ELEMENT_ID#/",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "180",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y"
	)
);?> </div>
 <hr style="MARGIN: 20px 0px; CLEAR: both" /> 
<br />
 
<div class="h2">Новости / <a href="/news/" class="subhead" >Архив новостей</a></div>
 
<br />
 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"top",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "#SITE_DIR#/news/#ID#/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?><span style="font-size: 7.5pt; ">  </span></p>
<p><font size="2">Фабрика &quot;<strong>Мягкая мебель</strong>&quot; занимается производством и продажей мягкой мебели с 1993 года. 
    <br />
   </font></p>
 
<p><font size="2">В настоящее время продукция предприятия насчитывает более 50-ти видов изделий, среди которых угловые диваны, диваны-кровати, кресла, комплекты мягкой мебели. 
    <br />
   </font></p>
 
<p><font size="2">Фирма сотрудничает с крупными мебельными центрами Санкт-Петербурга: &quot;</font><a href="/contacts/3.php" >Аквилон</a><font size="2">&quot;, &quot;Мебель-Сити&quot;, &quot;Мебельный Континент&quot;, &quot;</font><a href="/contacts/4.php" >Мебель-Холл</a><font size="2">&quot;, осуществляет поставки в регионы России и за рубеж. </font><a id="bxid_184814" href="/company/" ><font size="2" color="#0000ff"> 
      <br />
     </font></a></p>
 
<p><a id="bxid_184814" href="/company/" ><font size="2" color="#0000ff">Фабрика мягкой мебели </font></a><font size="2"> неизменно работает над расширением модельного ряда и улучшением качества продукции, развитием региональной сети сбыта, участвует в выставках в России и за рубежом.</font></p>
 
<p><font size="2">Вы можете купить нашу </font><a id="bxid_65879" href="/catalog/stock.php" >мебель со склада<font size="2">,</font></a><font size="2">  распечатать купон на </font><a href="/discount_coupon/" >5% скидку</a><font size="2">. 
    <br />
   </font></p>
 
<p><font size="2">Возможно сделать любые </font><a id="bxid_37419" href="/wheretobuy/" ><font size="2" color="#0000ff">диваны на заказ</font></a><font size="2">. Также у нас продается мягкая мебель оптом.</font></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>