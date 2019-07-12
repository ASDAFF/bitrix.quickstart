<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(is_array($arResult["DETAIL_PICTURE"]))
{
	$arFileTmp = CFile::ResizeImageGet(
		$arResult['DETAIL_PICTURE'],
		array("width" => 350, 'height' => 1000),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		false
	);
	$arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

	$arResult['DETAIL_PICTURE_350'] = array(
		'SRC' => $arFileTmp["src"],
		'WIDTH' => IntVal($arSize[0]),
		'HEIGHT' => IntVal($arSize[1]),
	);
}

if(is_array($arResult['MORE_PHOTO']) && count($arResult['MORE_PHOTO']) > 0)
{
	unset($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']);

	foreach ($arResult['MORE_PHOTO'] as $key => $arFile)
	{
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => 50, 'height' => 50),
			BX_RESIZE_IMAGE_EXACT,
			false
		);
		$arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);
		$arFile['PREVIEW_WIDTH'] = IntVal($arSize[0]);
		$arFile['PREVIEW_HEIGHT'] = IntVal($arSize[1]);

		$arFile['SRC_PREVIEW'] = $arFileTmp['src'];
		$arResult['MORE_PHOTO'][$key] = $arFile;
	}
}

$arResult["RESPONSIBLE"] = Array();

if(isset($arResult["DISPLAY_PROPERTIES"]["RESPONSIBLE"]["VALUE"]) && intVal($arResult["PROPERTIES"]["RESPONSIBLE"]["VALUE"]))
{
 unset($arResult["DISPLAY_PROPERTIES"]["RESPONSIBLE"]);
 
 $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_EMAIL", "PREVIEW_TEXT");
 $arFilter = Array("IBLOCK_CODE"=>"teachers_".SITE_ID, "ID"=>$arResult["PROPERTIES"]["RESPONSIBLE"]["VALUE"], "ACTIVE"=>"Y");
 $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
 while($ob = $res->GetNextElement()){  
    $arFields = $ob->GetFields(); 
    //echo '<pre>';print_r($arFields);echo '</pre>';
    
      if($arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]))
      {
       $arFileTmp = CFile::ResizeImageGet(
              $arFile,
              array("width" => 50, 'height' => 50),
              BX_RESIZE_IMAGE_EXACT,
              false
          );
          $arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

          $arUser['PERSONAL_PHOTO'] = array(
              'SRC' => $arFileTmp["src"],
              'WIDTH' => IntVal($arSize[0]),
              'HEIGHT' => IntVal($arSize[1]),
          );
      }
        $arUser["MAIL"] = $arFields["PROPERTY_EMAIL_VALUE"];
        $arUser["NAME"] = $arFields["NAME"];
  $arUser["TEXT"] = $arFields["PREVIEW_TEXT"];
    
 }
 

  
  $arResult["RESPONSIBLE"] = $arUser;
 
}
?>