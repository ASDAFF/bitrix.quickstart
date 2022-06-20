<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];


$arIBlockType2 = CIBlockParameters::GetIBlockTypes();

$arIBlock2 = array();
$rsIBlock2 = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["ADD_IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr2=$rsIBlock2->Fetch())
	$arIBlock2[$arr2["ID"]] = "[".$arr2["ID"]."] ".$arr2["NAME"];

$arProperties = array(0 => GetMessage("NON"));
$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]));
while ($arr = $rsProp->Fetch()) $arProperties[$arr["ID"]] = "[" . ($arr["CODE"] ? $arr["CODE"] : $arr["ID"]) . "] " . $arr["NAME"];


$arProperties2 = array(0 => GetMessage("NON"));
foreach ($arCurrentValues["ADD_IBLOCK_ID"] As $addiblock){
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $addiblock));
	while ($arr = $rsProp->Fetch()) $arProperties2[$arr["ID"]] = "[" . ($arr["CODE"] ? $arr["CODE"] : $arr["ID"]) . "] " . $arr["NAME"];
}
$arComponentParameters = array(
	"GROUPS" => array(

		"DATA_SOURCE" => array(
			"NAME" => GetMessage("SUP_DATA_SOURCE"),
			'SORT' => '100'
		),
		"EXPERT_MODE" => array(
			"NAME" => GetMessage("EXPERT_MODE"),
			'SORT' => '120'
		),
		"SIMPLE_SETTINGS" => array(
			"NAME" => GetMessage("SIMPLE_SETTINGS"),
			'SORT' => '110'
		),

	),
	"PARAMETERS" => array(
		"ADD_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ADD_JQUERY"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		/*"DONT_USE_BASE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DONT_USE_BASE"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),*/

		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"IBLOCK_LINK_URL_DEFAULT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_LINK_URL_DEFAULT"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"IBLOCK_LINK" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_LINK"),
			"TYPE"              => "LIST",
			"MULTIPLE"          => "N",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"            => $arProperties,
			"REFRESH"           => "N",
		),
		"IBLOCK_ACT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ACT"),
			"TYPE"              => "LIST",
			"MULTIPLE"          => "N",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"            => $arProperties,
			"REFRESH"           => "N",
		),
		"IBLOCK_ACTVAL" => array(
			"PARENT"  => "BASE",
			"NAME"    => GetMessage("IBLOCK_ACTVAL"),
			"TYPE"    => "STRING",
			"DEFAULT" => ""),
		"DONT_USE_ADD_NOTE" => array(
			"TYPE"    => "CUSTOM",
			"JS_FILE" => "/bitrix/js/main/comp_props.js",
			"JS_EVENT" => "BxShowComponentNotes",
			"JS_DATA" => GetMessage("DONT_USE_ADD_NOTE"),
		),
		"DONT_USE_ADD" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("DONT_USE_ADD"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),

		"ADD_IBLOCK_TYPE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("ADD_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType2,
			"REFRESH" => "Y",
		),
		"ADD_IBLOCK_ID" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("ADD_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"MULTIPLE"          => "Y",
			"VALUES" => $arIBlock2,
			"REFRESH" => "Y",
		),

		"ADD_IBLOCK_LINK_URL_DEFAULT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_LINK_URL_DEFAULT"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
		"ADD_IBLOCK_LINK" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_LINK"),
			"TYPE"              => "LIST",
			"MULTIPLE"          => "N",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"            => $arProperties2,
			"REFRESH"           => "N",
		),
		"ADD_IBLOCK_ACT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("ADD_IBLOCK_ACT"),
			"TYPE"              => "LIST",
			"MULTIPLE"          => "N",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"            => $arProperties2,
			"REFRESH"           => "N",
		),
		"ADD_IBLOCK_ACTVAL" => array(
			"PARENT"  => "ADDITIONAL_SETTINGS",
			"NAME"    => GetMessage("ADD_IBLOCK_ACTVAL"),
			"TYPE"    => "STRING",
			"DEFAULT" => ""),
		
		"EXPERT_MODE_ON" => array(
			"PARENT" => "EXPERT_MODE",
			"NAME" => GetMessage("EXPERT_MODE_ON"),
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		
		"EXPERT_MODE_nav"                      => array(
			"PARENT"  => "SIMPLE_SETTINGS",
			"NAME"    => GetMessage("EXPERT_MODE_nav"),
			"TYPE"    => "CHECKBOX",
			"REFRESH" => "Y",
			"DEFAULT" => "N"),

		"EXPERT_MODE_navText_left"                      => array(
			"PARENT"  => "SIMPLE_SETTINGS",
			"NAME"    => GetMessage("EXPERT_MODE_navText_left"),
			"TYPE"    => "STRING",
			"DEFAULT" => GetMessage("EXPERT_MODE_navText_left_DEF")),

		"EXPERT_MODE_navText_right"                      => array(
			"PARENT"  => "SIMPLE_SETTINGS",
			"NAME"    => GetMessage("EXPERT_MODE_navText_right"),
			"TYPE"    => "STRING",
			"DEFAULT" => GetMessage("EXPERT_MODE_navText_right_DEF")),

		"MAX_HEIGHT_SLIDE"                      => array(
			"PARENT"  => "SIMPLE_SETTINGS",
			"NAME"    => GetMessage("MAX_HEIGHT_SLIDE"),
			"TYPE"    => "STRING",
			"DEFAULT" => "0"),
		"MAX_WIDTH_SLIDE"                      => array(
			"PARENT"  => "SIMPLE_SETTINGS",
			"NAME"    => GetMessage("MAX_WIDTH_SLIDE"),
			"TYPE"    => "STRING",
			"DEFAULT" => "0"),



	),
);
/*
if ($arCurrentValues['EXPERT_MODE_responsive']!='Y'){
	unset($arComponentParameters['PARAMETERS']['EXPERT_MODE_responsivebig'],
		$arComponentParameters['PARAMETERS']['EXPERT_MODE_responsivemed'],
		$arComponentParameters['PARAMETERS']['EXPERT_MODE_responsivemin']
	);
}
if ($arCurrentValues['EXPERT_MODE_responsive']=='Y'){
	unset($arComponentParameters['PARAMETERS']['EXPERT_MODE_items']
);
}*/
if ($arCurrentValues['IBLOCK_LINK_URL_DEFAULT']=='Y'){
	unset($arComponentParameters['PARAMETERS']['IBLOCK_LINK']
);
}

if ($arCurrentValues['ADD_IBLOCK_LINK_URL_DEFAULT']=='Y'){
	unset($arComponentParameters['PARAMETERS']['ADD_IBLOCK_LINK']
);
}

if ($arCurrentValues['EXPERT_MODE_nav']!='Y'){
	unset($arComponentParameters['PARAMETERS']['EXPERT_MODE_navText_left'],
		$arComponentParameters['PARAMETERS']['EXPERT_MODE_navText_right']
	);
}


if ($arCurrentValues['DONT_USE_BASE']=='Y'){

	unset($arComponentParameters['PARAMETERS']['IBLOCK_TYPE'],
		$arComponentParameters['PARAMETERS']['IBLOCK_ID'],
		$arComponentParameters['PARAMETERS']['SECTION_ID'],
		$arComponentParameters['PARAMETERS']['SECTION_CODE'],
		$arComponentParameters['PARAMETERS']['IBLOCK_LINK']
	);

}

if ($arCurrentValues['DONT_USE_ADD']=='Y'){

	unset($arComponentParameters['PARAMETERS']['ADD_IBLOCK_TYPE'],
		$arComponentParameters['PARAMETERS']['ADD_IBLOCK_ID'],
		$arComponentParameters['PARAMETERS']['ADD_IBLOCK_LINK'],
		$arComponentParameters['PARAMETERS']['ADD_IBLOCK_LINK_URL_DEFAULT'],
		$arComponentParameters['PARAMETERS']['ADD_IBLOCK_ACT'],
		$arComponentParameters['PARAMETERS']['ADD_IBLOCK_ACTVAL']
	);

}


if ($arCurrentValues['EXPERT_MODE_ON']=='Y'){

	$arComponentParameters['PARAMETERS'] = array_merge(
		$arComponentParameters['PARAMETERS'],
		[

			"EXPERT_MODE_loop"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_loop"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),

			"EXPERT_MODE_margin"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_margin"),
				"TYPE"    => "STRING",
				"DEFAULT" => "10"),
			"EXPERT_MODE_center"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_center"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_mouseDrag"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_mouseDrag"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),
			"EXPERT_MODE_touchDrag"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_touchDrag"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),
			"EXPERT_MODE_pullDrag"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_pullDrag"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),
			"EXPERT_MODE_freeDrag"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_freeDrag"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_stagePadding"                  => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_stagePadding"),
				"TYPE"    => "STRING",
				"DEFAULT" => "0"),
			/*"EXPERT_MODE_merge"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_merge"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),*/
			/*"EXPERT_MODE_mergeFit"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_mergeFit"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),*/

				"EXPERT_MODE_autoHeight"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_autoHeight"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
				"EXPERT_MODE_startPosition"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_startPosition"),
					"TYPE"    => "STRING",
					"DEFAULT" => "0"),
				"EXPERT_MODE_URLhashListener"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_URLhashListener"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),

			/*"EXPERT_MODE_rewind"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_rewind"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),*/


			/*"EXPERT_MODE_slideBy"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_slideBy"),
				"TYPE"    => "STRING",
				"DEFAULT" => "1"),*/
				"EXPERT_MODE_slideTransition"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_slideTransition"),
					"TYPE"    => "STRING",
					"DEFAULT" => "``"),
				"EXPERT_MODE_dots"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_dots"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "Y"),
				"EXPERT_MODE_dotsEach"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_dotsEach"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "Y"),
				"EXPERT_MODE_dotsData"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_dotsData"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
				"EXPERT_MODE_lazyLoad"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_lazyLoad"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
			/*"EXPERT_MODE_lazyLoadEager"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_lazyLoadEager"),
				"TYPE"    => "STRING",
				"DEFAULT" => "0"),*/
				"EXPERT_MODE_autoplay"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_autoplay"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
				"EXPERT_MODE_autoplayTimeout"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_autoplayTimeout"),
					"TYPE"    => "STRING",
					"DEFAULT" => "5000"),
				"EXPERT_MODE_autoplayHoverPause"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_autoplayHoverPause"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "Y"),
				"EXPERT_MODE_smartSpeed"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_smartSpeed"),
					"TYPE"    => "STRING",
					"DEFAULT" => "250"),
				"EXPERT_MODE_fluidSpeed"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_fluidSpeed"),
					"TYPE"    => "STRING",
					"DEFAULT" => "300"),
				"EXPERT_MODE_autoplaySpeed"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_autoplaySpeed"),
					"TYPE"    => "STRING",
					"DEFAULT" => "250"),
				"EXPERT_MODE_navSpeed"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_navSpeed"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
				"EXPERT_MODE_dotsSpeed"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_dotsSpeed"),
					"TYPE"    => "CHECKBOX",
					"DEFAULT" => "N"),
			/*"EXPERT_MODE_dragEndSpeed"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_dragEndSpeed"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),*/
			/*"EXPERT_MODE_callbacks"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_callbacks"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),*/
			/*"EXPERT_MODE_video"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_video"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_videoHeight"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_videoHeight"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_videoWidth"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_videoWidth"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_animateOut"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_animateOut"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_animateIn"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_animateIn"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_fallbackEasing"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_fallbackEasing"),
				"TYPE"    => "STRING",
				"DEFAULT" => "swing"),
			"EXPERT_MODE_info"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_info"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),*/
			/*"EXPERT_MODE_nestedItemSelector"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_nestedItemSelector"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "N"),
			"EXPERT_MODE_itemElement"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_itemElement"),
				"TYPE"    => "STRING",
				"DEFAULT" => "div"),
			"EXPERT_MODE_stageElement"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_stageElement"),
				"TYPE"    => "STRING",
				"DEFAULT" => "div"),*/
				"EXPERT_MODE_navContainer"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_navContainer"),
					"TYPE"    => "STRING",
					"DEFAULT" => "N"),
				"EXPERT_MODE_dotsContainer"                      => array(
					"PARENT"  => "EXPERT_MODE",
					"NAME"    => GetMessage("EXPERT_MODE_dotsContainer"),
					"TYPE"    => "STRING",
					"DEFAULT" => "N"),
			/*"EXPERT_MODE_checkVisible"                      => array(
				"PARENT"  => "EXPERT_MODE",
				"NAME"    => GetMessage("EXPERT_MODE_checkVisible"),
				"TYPE"    => "CHECKBOX",
				"DEFAULT" => "Y"),*/
			]);
}
?>