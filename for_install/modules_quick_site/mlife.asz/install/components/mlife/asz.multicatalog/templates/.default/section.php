<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->SetViewTarget("filter");?>
<div class="wrapfilterRight">
<div class="catalogFilter">
<?if($arParams["USE_FILTER_SUPER"]=="Y"){?>
<?$paransFilter = array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"FILTER_ID" => $arResult["VARIABLES"]["FILTER_ID"],
		"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
	);
	foreach($arParams as $key=>$par){
		if(strpos($key,"D_PROP")!==false) {
			$paransFilter[$key] = $par; 
		}
	}
	?>
<?$APPLICATION->IncludeComponent(
	"mlife:asz.multicatalog.seffilter", 
	".default", 
	$paransFilter,
	$component
);?>
<script>
setTimeout(function(){
	$('.catalogFilter').css({'height':$('.catalogFilter .filter').height()+'px'});
},500);
window.setFilter = function(id){
	$('.catalogFilter').append('<div class="preload"><div class="load"></div></div>');
	$.ajax({
		 url: $("#"+id).val(),
		 data: {ajaxfilter: 1},
		 dataType : "html",
		 type: "POST",
		 success: function (data, textStatus) {
			setTimeout(function(){
				$('.catalogFilter').html(data);
				$('.catalogFilter').css({'height':$('.catalogFilter .filter').height()+'px'});
			},100);
		}
	});
};
</script>
<?}else{?>
<?$APPLICATION->IncludeComponent(
	"mlife:asz.multicatalog.filter",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
	),
$component
);?>
<?}?>
</div>
</div>
<?$this->EndViewTarget();?>
<div class="wrapSectionsList">
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"",
	Array(
		"VIEW_MODE" => "TILE",
		"SHOW_PARENT_NAME" => "Y",
		"HIDE_SECTION_NAME" => "N",
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => array("NAME", "PICTURE"),
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	),
$component
);?>
</div>
<div class="sortBlock">
	<div class="sortWrap">
	<?  // Elements sort
	$arAvailableSort = array(
		"name" => Array("NAME", "ASC",GetMessage("MLIFE_ASZ_CATALOG_T_1")),
		"price" => Array('PRICE.PRICEVAL', "ASC", GetMessage("MLIFE_ASZ_CATALOG_T_2")),
		"kol" => Array('KOL.KOL', "DESC", GetMessage("MLIFE_ASZ_CATALOG_T_3")),
	);
	$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "NAME";
	$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];
	?>
	<?foreach ($arAvailableSort as $key => $val):
	$selected = ($sort == $val[0]) ? ' active' : '';
	$newSort = ($sort == $val[0]) ? ($sort_order == 'desc' ? 'asc' : 'desc') : $arAvailableSort[$key][1];
	?>
	<a class="sorter<?=$selected?> order_<?=$newSort?>" href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>"><?=$val[2]?></a>
	<?endforeach;?>
	</div>
</div>
<div class="catalogMainwrap">
<?$APPLICATION->IncludeComponent(
	"mlife:asz.multicatalog.section",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"ELEMENT_SORT_FIELD" => $sort,
		"ELEMENT_SORT_ORDER" => $sort_order,
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_BY" => "Y",
		"HIDE_QUANT" => "Y",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "Y",
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"PROPERTY_CODE_LABEL" => $arParams["PROPERTY_CODE_LABEL"],
		"PRICE" => $arParams["PRICE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"ZAKAZ" => $arParams["ZAKAZ"],
	),
$component
);?>
</div>
<?if($arParams["USE_FILTER_SUPER"]=="Y"){?>
<?
if($arResult["VARIABLES"]["FILTER_ID"]){
	global $SEO_TEMPLATE;
	foreach($SEO_TEMPLATE as $key=>&$val){
		if($key=="TEMPLATE_TITLE") {
			$val = str_replace("this.CUR",$APPLICATION->GetPageProperty("title"),$val);
			if($val) $APPLICATION->SetPageProperty("title", $val);
		}elseif($key=="TEMPLATE_KEY"){
			$val = str_replace("this.CUR",$APPLICATION->GetPageProperty("keywords"),$val);
			if($val) $APPLICATION->SetPageProperty("keywords", $val);
		}elseif($key=="TEMPLATE_DESC"){
			$val = str_replace("this.CUR",$APPLICATION->GetPageProperty("description"),$val);
			if($val) $APPLICATION->SetPageProperty("description", $val);
		}else{
			$val = str_replace("this.CUR","",$val);
		}
	}
}
?>
<?}?>