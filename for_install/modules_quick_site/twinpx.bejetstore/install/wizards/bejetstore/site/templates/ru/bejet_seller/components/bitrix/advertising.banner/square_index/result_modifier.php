<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("advertising") && !empty($arResult["BANNER_PROPERTIES"])){
	if($arResult["BANNER_PROPERTIES"]["URL"]){
		if($arResult["BANNER_PROPERTIES"]["IMAGE_ID"]){
			$file_path = CFile::GetPath($arResult["BANNER_PROPERTIES"]["IMAGE_ID"]);
			$link = "/bitrix/rk.php?id=".$arResult["BANNER_PROPERTIES"]["ID"]."&event1=".$arResult["BANNER_PROPERTIES"]["STAT_EVENT_1"]."&event2=".$arResult["BANNER_PROPERTIES"]["STAT_EVENT_2"]."&event3=";
			$event3 = str_replace("#CONTRACT_ID#", $arResult["BANNER_PROPERTIES"]["CONTRACT_ID"], $arResult["BANNER_PROPERTIES"]["STAT_EVENT_3"]);
			$event3 = str_replace("#BANNER_ID#", $arResult["BANNER_PROPERTIES"]["ID"], $event3);
			$event3 = str_replace("#TYPE_SID#", $arResult["BANNER_PROPERTIES"]["TYPE_SID"], $event3);
			$event3 = str_replace("#BANNER_NAME#", $arResult["BANNER_PROPERTIES"]["NAME"], $event3);
			$event3 = urlencode($event3);
			$event3 .= "&goto=".urlencode($arResult["BANNER_PROPERTIES"]["URL"]);
			$link .= $event3;
			if($arResult["BANNER_PROPERTIES"]["URL_TARGET"]){
				$arResult["BANNER"] = '<a href="'.$link.'" target="'.$arResult["BANNER_PROPERTIES"]["URL_TARGET"].'" class="col-sm-4">
		<span style="background-image:url('.$file_path.'); height: 200px; display: block;"></span>
	</a>';
			}else{
				$arResult["BANNER"] = '<a href="'.$link.'" class="col-sm-4">
		<span style="background-image:url('.$file_path.'); height: 200px; display: block;"></span>
	</a>';
			}
		}
	}else{
		if($arResult["BANNER_PROPERTIES"]["IMAGE_ID"]){
			$file_path = CFile::GetPath($arResult["BANNER_PROPERTIES"]["IMAGE_ID"]);
			$arResult["BANNER"] = '<span class="col-sm-4">
			<span style="background-image:url('.$file_path.'); height: 200px; display: block;"></span>
		</span>';
		}
	}
}
?>