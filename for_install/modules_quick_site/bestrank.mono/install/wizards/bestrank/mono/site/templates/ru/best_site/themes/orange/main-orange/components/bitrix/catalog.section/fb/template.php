<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]))
	$APPLICATION->SetTitle($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]);
elseif (!empty($arResult["NAME"]))
	$APPLICATION->SetTitle($arResult["NAME"]);

if (!empty($arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"]))
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"]);
else
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["NAME"]);?>

<? //echo "<pre>"; print_r($GLOBALS[$arParams["FILTER_NAME"]]); echo "</pre>"; ?>


<?
$arrFilter=urlencode(serialize($GLOBALS[$arParams["FILTER_NAME"]]));
$pageAjax = $APPLICATION->GetCurPageParam("ajax=Y", array("ajax")); 
$pageAjax = SITE_DIR."catalog/catalog_section.php?PAGEN_1=";
//echo $pageAjax ;
?>

<div id="catalog_section_0"></div>

<script type="text/javascript">

var cnt = 0;
var pos = 0;
var params = new Array();
params = {iblock_id: '<?=$arParams['IBLOCK_ID']?>', section_id : '<?=$arResult['ID']?>', filter : '<?=$arrFilter?>', inline : '<?=$arParams["LINE_ELEMENT_COUNT"]?>', sort_order : '<?=$_REQUEST['order']?>'};

$(window).scroll(function (event) {
	var pageY = $(window).scrollTop(); 
	var innerHeight = $(window).height();
	var documentHeight = $(document).height();
	var dHeight = $('#catalog_section_'+cnt).height();
	var dTop;
	if ($('#catalog_section_'+cnt))	
		dTop = $('#catalog_section_'+cnt).position().top;

	if (pageY > pos && pageY + innerHeight > dTop +dHeight || cnt==0 ) {
		cnt++;

		$.ajax({ 
			url: '<?=$pageAjax?>'+cnt ,
			type: "POST",
			data: params,
			success: function(data) {  
				addRow(data); 
				pos = pageY;	
			}
		}); 
	}
});

$(document).ready(function() {
	var dHeight = $('#catalog_section_'+cnt).height();
	var dTop = $('#catalog_section_'+cnt).position().top;
	var innerHeight = $(window).height();
	if(dHeight +dTop <= innerHeight || cnt==0 ){
		setAjaxRequest(); 
	}
});

function setAjaxRequest(){
	cnt++;

	$.ajax({ 
		url: '<?=$pageAjax?>'+cnt ,
		//url: '<?=$templateFolder?>/catalog_section.php?PAGEN_1='+cnt  ,
		type: "POST",
		data: params,
		success: function(data) {  
			addRow(data);
			var dHeight = $('#catalog_section_'+cnt).height();
			var dTop = $('#catalog_section_'+cnt).position().top;
			var innerHeight = $(window).height();
			if(dHeight +dTop <= innerHeight && data.length>0){
				setAjaxRequest(); 
			}				
		}
	}); 
}

function addRow(content){
	$('<div id="catalog_section_'+cnt+'"></div>').insertAfter('#catalog_section_'+(cnt-1));
	setContent('#catalog_section_'+cnt, content);		
}

function setContent(elem, content) {
	$(elem).hide();
	$(elem).html(content);
	$(elem).show( "slow");
}
</script>

