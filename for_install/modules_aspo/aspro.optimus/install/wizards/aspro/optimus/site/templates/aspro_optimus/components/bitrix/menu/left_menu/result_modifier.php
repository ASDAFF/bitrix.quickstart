<?$arResult = COptimus::getChilds2($arResult);

if($arResult){
	foreach($arResult as $key=>$arItem){
		if($arItem["CHILD"]){
			$arResult[$key]["CHILD"]=COptimus::unique_multidim_array($arItem["CHILD"], "TEXT");
		}
	}
}?>