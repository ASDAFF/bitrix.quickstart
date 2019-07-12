<?
 foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		if(is_array($arItem["PREVIEW_PICTURE"]))
  {
   $arFileTmp = CFile::ResizeImageGet(
			 $arItem["PREVIEW_PICTURE"],
			 array("width" => 43, "height" => 100),
			 BX_RESIZE_IMAGE_PROPORTIONAL,
			 false
		 );
		 $arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);
   $arItem["PREVIEW_PICTURE"]['WIDTH'] = IntVal($arSize[0]);
		 $arItem["PREVIEW_PICTURE"]['HEIGHT'] = IntVal($arSize[1]);
	 	$arItem["PREVIEW_PICTURE"]['SRC'] = $arFileTmp['src'];
   $arResult['ITEMS'][$key]["PREVIEW_PICTURE"] = $arItem["PREVIEW_PICTURE"];
  }
	}
?>