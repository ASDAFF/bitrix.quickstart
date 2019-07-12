<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if( CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") ) {
} else {
	die(GetMessage("MODULES_NOT_INSTALLED"));
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if ($arParams["SHOW_SUBSCRIBED"] != "Y") $arParams["SHOW_SUBSCRIBED"] = "N";

if (empty($arParams["PRICE_ID"])) $arParams["PRICE_ID"] = false;

$arParams["SIZES_CODES"] = array(
		"REAL_BREAST_GRASP" => GetMessage("REAL_BREAST_GRASP"),
		"REAL_GRASP_WAIST" => GetMessage("REAL_GRASP_WAIST"),
		"REAL_GRASP_HIPS" => GetMessage("REAL_GRASP_HIPS"),
		"REAL_SHOULDER_LENGTH" => GetMessage("REAL_SHOULDER_LENGTH"),
		"REAL_BELT_LENGTH" => GetMessage("REAL_BELT_LENGTH"),
		"REAL_SLEEVE_LENGTH" => GetMessage("REAL_SLEEVE_LENGTH"),
		"REAL_SHOULDER_WIDTH" => GetMessage("REAL_SHOULDER_WIDTH"),
		"REAL_LENGTH_INNER_SE" => GetMessage("REAL_LENGTH_INNER_SE"),
		"REAL_GRASP_TROUSER_L" => GetMessage("REAL_GRASP_TROUSER_L"),
		"REAL_WIDTH_BAG" => GetMessage("REAL_WIDTH_BAG"),
		"REAL_TALL_BAG" => GetMessage("REAL_TALL_BAG"),
		"REAL_DEPTH" => GetMessage("REAL_DEPTH"),
		"REAL_LENGTH_HANDLE" => GetMessage("REAL_LENGTH_HANDLE"),
		"REAL_LENGTH_ACC" => GetMessage("REAL_LENGTH_ACC"),
		"REAL_WIDTH_ACC" => GetMessage("REAL_WIDTH_ACC"),
		"REAL_TALL_SHO" => GetMessage("REAL_TALL_SHO"),
		"REAL_LENGTH_INSOLE" => GetMessage("REAL_LENGTH_INSOLE"),
		"REAL_WIDTH_INSOLE" => GetMessage("REAL_WIDTH_INSOLE"),
		"REAL_HEIGHT_HEEL" => GetMessage("REAL_HEIGHT_HEEL"),
		"REAL_HEIGHT_PLATFORM" => GetMessage("REAL_HEIGHT_PLATFORM"),
		"REAL_GRASP_TOP" => GetMessage("REAL_GRASP_TOP"),
		"REAL_GRASP_HEAD" => GetMessage("REAL_GRASP_HEAD"),
		"REAL_LENGTH_GLO" => GetMessage("REAL_LENGTH_GLO"),
		"REAL_GRASP_PALM" => GetMessage("REAL_GRASP_PALM"),
		"REAL_GRASP_WRIST" => GetMessage("REAL_GRASP_WRIST"),
		"REAL_DIAMETER_DIAL" => GetMessage("REAL_DIAMETER_DIAL"),
		"REAL_LENGTH_CANE" => GetMessage("REAL_LENGTH_CANE"),
		"REAL_DIAMETER_DOME" => GetMessage("REAL_DIAMETER_DOME")
);

if ($arParams['ELEMENT_ID'] < 1) {
	
	//$arResult['ELEMENTS'] - пустой - в шаблоне выведена 404 ошибка
	
} else {

	$arrayGroupCanEdit = array(1);
	//if (!empty($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"])) $arrayGroupCanEdit[] = $arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"];
	
	// если пользователь входит в группу Администраторы интернет-магазина [5]
	// то показываем карандашик для редактирования
	//$arParams['SHOW_EDIT_BUTTON'] = "N";

	// Если нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
	if ($this->StartResultCache(false, $arParams['SHOW_EDIT_BUTTON']))
	{
		require(dirname(__FILE__) . "/getElement.php");
		
		$arResult['ELEMENTS'] = $arElements;

		$this->SetResultCacheKeys(array(
				"IBLOCK_ID",
				"ID",
				"IBLOCK_SECTION_ID",
				"NAME",
				"LIST_PAGE_URL",
				"PROPERTIES",
				"SECTION",
				"SHOW_EDIT_BUTTON"
		));		
		
		$this -> IncludeComponentTemplate();
		
	}
	// end work with cache

}
?>