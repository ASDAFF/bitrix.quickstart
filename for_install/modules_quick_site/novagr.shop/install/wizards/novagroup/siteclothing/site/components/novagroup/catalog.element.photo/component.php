<?
    if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

    if( CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") ) {
    } else {
        die(GetMessage("MODULES_NOT_INSTALLED"));
    }

    $photo = new Novagroup_Classes_General_CatalogPhoto($arParams["CATALOG_ELEMENT_ID"],$arParams["CATALOG_IBLOCK_ID"],$arParams['PHOTO_ID']);
    $arResult = $photo -> getPhoto();
	
	if($arParams['I_FROM_CATALOG'] == "Y")
		{
		//deb($arResult);
		
		//return CFile::GetPath($arResult["PHOTO"]);
		$tmp = Novagroup_Classes_General_Main::MakeResizePicture($arResult["PHOTO"], array("WIDTH"=>$arParams['PHOTO_WIDTH'],"HEIGHT"=>$arParams['PHOTO_HEIGHT']));
		return $tmp['src'];
		}else
		$this -> IncludeComponentTemplate();
	
?>