<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
	if( CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") ) {
	} else {
		die(GetMessage("MODULES_NOT_INSTALLED"));
	}
	
	if (!empty($arParams['MICRODATA']['elmid']) && !empty($arParams['CATALOG_IBLOCK_ID']))
	{
		$photo = new Novagroup_Classes_General_CatalogPhoto(
			$arParams['MICRODATA']['elmid'],
			$arParams["CATALOG_IBLOCK_ID"],
			$arParams['MICRODATA']['imgid'],
			$arParams["IS_IMAGERY"]
		);
		$arResult = $photo -> getPhoto();
		$arParams['MICRODATA']['imgid'] = $arResult['PHOTO'];
	}
	$rsFile = CFile::GetById($arParams['MICRODATA']['imgid']);
	$arFile = $rsFile -> Fetch();
	$orgPhotoRelPath = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
	$orgPhotoAbsPath = $_SERVER['DOCUMENT_ROOT'].$orgPhotoRelPath;
	$arParams['MICRODATA']['imgsrc'] = $orgPhotoRelPath;
	
	if ( file_exists($orgPhotoAbsPath) && ((int)$arParams['MICRODATA']['imgid']) > 0)
	{
		$resPhotoRelPath = "/upload/resize_cache_imagick/"
			.$arParams['MICRODATA']['imgid']."/"
			.$arParams['ATTRIBUTES']['width']."x".$arParams['ATTRIBUTES']['height'].".jpg";
		$resPhotoAbsPath = $_SERVER['DOCUMENT_ROOT'].$resPhotoRelPath;
		
		if ( file_exists($resPhotoAbsPath) )
		{
			$arResult = array('src' => $resPhotoRelPath);
		// if not in cache resize
		}else $arResult = false;
	}else{
		global $CACHE_MANAGER;
		$CACHE_MANAGER -> ClearByTag("catalog.list.".$arParams['MICRODATA']['elmid'][ $key ]);
		$arResult = array('src' => SITE_TEMPLATE_PATH."/images/nophoto.png");
	}
	$this -> IncludeComponentTemplate();
?>