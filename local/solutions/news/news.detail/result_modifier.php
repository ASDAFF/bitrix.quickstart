<? 




// сортировку берем из параметров компонента
$arSort = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
// выбрать нужно id элемента, его имя и ссылку. Можно добавить любые другие поля, например PREVIEW_PICTURE или PREVIEW_TEXT
$arSelect = array(
		"ID",
		"NAME",
		"DETAIL_PAGE_URL"
	);
// выбираем активные элементы из нужного инфоблока. Раскомментировав строку можно ограничить секцией
$arFilter = array (
		"IBLOCK_ID" => $arResult["IBLOCK_ID"],
		//"SECTION_CODE" => $arParams["SECTION_CODE"],
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);

// выбирать будем по 1 соседу с каждой стороны от текущего
$arNavParams = array(
		"nPageSize" => 1,
		"nElementID" => $arResult["ID"],
	);


$arItems = Array();
$arSort = array("ACTIVE_FROM"=>"DESC");
$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
while($obElement = $rsElement->GetNextElement())
		$arItems[] = $obElement->GetFields();



// возвращается от 1го до 3х элементов в зависимости от наличия соседей, обрабатываем эту ситуацию		
if(count($arItems)==3):
	$arResult["LINKED"]["TORIGHT"] = Array("NAME"=>$arItems[0]["NAME"], "URL"=>str_replace("section/post","section/0/post" , $arItems[0]["DETAIL_PAGE_URL"]));
	$arResult["LINKED"]["TOLEFT"] = Array("NAME"=>$arItems[2]["NAME"], "URL"=>str_replace("section/post","section/0/post" , $arItems[2]["DETAIL_PAGE_URL"]));
elseif(count($arItems)<3):
#	dr($arItems);
	if($arItems[0]["ID"]!=$arResult["ID"])
		$arResult["LINKED"]["TORIGHT"] = Array("NAME"=>$arItems[0]["NAME"], "URL"=>$arItems[0]["DETAIL_PAGE_URL"]);
	else
		$arResult["LINKED"]["TOLEFT"] = Array("NAME"=>$arItems[1]["NAME"], "URL"=>$arItems[1]["DETAIL_PAGE_URL"]);
endif;
// в $arResult["TORIGHT"] и $arResult["TOLEFT"] лежат массивы с информацией о соседних элементах


//обработка фотографик
foreach ($arResult["PROPERTIES"]["MORE_PHOTOS"]["VALUE"] as $key => $foto) {
	$file = CFile::GetFileArray($foto);
	$FOTOS[++$key]=$file['SRC'];

	$arResult["DETAIL_TEXT"] = str_replace("[$key]",'<a href="'.$file['SRC'].'" rel="group" class="fancybox"><img src="'.$file['SRC'].'" width="100" /></a>' , $arResult["DETAIL_TEXT"]);
}




//dr($FOTOS);




