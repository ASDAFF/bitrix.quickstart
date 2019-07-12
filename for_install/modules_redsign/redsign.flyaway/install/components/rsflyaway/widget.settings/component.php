<?if(!defined("B_PROLOG_INCLUDED"));

if(isset($_REQUEST["AJAX_CALL"]) && $_REQUEST["AJAX_CALL"]=="Y")
{
	define('PUBLIC_AJAX_MODE', true);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	global $APPLICATION, $USER;

	$fromModule = "N";

	$optionForm = COption::GetOptionString('redsign.flyaway', 'optionFrom', 'module');
	if($optionForm == "module") {
		$fromModule = "Y";
		if(!$USER->IsAdmin()) {
			return;
		}
	}

	if($fromModule == "Y") {
		echo "module";
		if(!$USER->IsAdmin()) {
			die();
		}

		if(CModule::IncludeModule('redsign.flyaway')) {
			RsFlyaway::saveSettings();
		}
	} else {
		if(isset($_REQUEST['gencolor'])) $_SESSION['gencolor'] = $_REQUEST['gencolor'];
		if(isset($_REQUEST['secondColor'])) $_SESSION['secondColor'] = $_REQUEST['secondColor'];
		if(isset($_REQUEST['openMenuType'])) $_SESSION['openMenuType'] = $_REQUEST['openMenuType'];
		if(isset($_REQUEST['presets'])) $_SESSION['presets'] = $_REQUEST['presets'];
		if(isset($_REQUEST['bannerType'])) $_SESSION['bannerType'] = $_REQUEST['bannerType'];
		if(isset($_REQUEST['filterSide'])) $_SESSION['filterSide'] = $_REQUEST['filterSide'];
		$_SESSION['Fichi'] = $_REQUEST['Fichi']=='Y' ? 'Y' : 'N';
		$_SESSION['SmallBanners'] = $_REQUEST['SmallBanners']=='Y' ? 'Y' : 'N';
		$_SESSION['New'] = $_REQUEST['New']=='Y' ? 'Y' : 'N';
		$_SESSION['PopularItem'] = $_REQUEST['PopularItem']=='Y' ? 'Y' : 'N';
		$_SESSION['Service'] = $_REQUEST['Service']=='Y' ? 'Y' : 'N';
		$_SESSION['AboutAndReviews'] = $_REQUEST['AboutAndReviews']=='Y' ? 'Y' : 'N';
		$_SESSION['News'] = $_REQUEST['News']=='Y' ? 'Y' : 'N';
		$_SESSION['Partners'] = $_REQUEST['Partners']=='Y' ? 'Y' : 'N';
		$_SESSION['Gallery'] = $_REQUEST['Gallery']=='Y' ? 'Y' : 'N';
	}
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");

	die();
}
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$fromModule = "N";

$optionForm = COption::GetOptionString('redsign.flyaway', 'optionFrom', 'module');
if($optionForm == "module") {
	$fromModule = "Y";
	if(!$USER->IsAdmin()) {
		return;
	}
}

$arFirst = array(
	/*"menuType" => array(
		"BLOCK_NAME" => "RS.FLW.HEAD_TYPE",
		"VALUES" => array(
			0 => "type1",
			1 => "type2",
			2 => "type3",
		),
		"CHECKED" => rsFlyaway::getSettings('menuType', 'type1'),
	),*/
	"openMenuType" => array(
		"VALUES" => array(
			0 => array(
				"val" =>"type1",
				"name" => "RS.FLW.STEP_MENU",
			),
			1 => array(
				"val" =>"type2",
				"name" => "RS.FLW.SHUTTER_MENU",
			),
			2 => array(
				"val" =>"type3",
				"name" => "RS.FLW.SIDE_MENU",
			)
		),
		"BLOCK_NAME" => "RS.FLW.OPEN_TYPE_MENU",
		"CHECKED" => rsFlyaway::getSettings('openMenuType', 'type1'),
	),
	"presets" => array(
		"BLOCK_NAME" => "RS.FLW.PRESETS_MENU",
		"VALUES" => array(
			0 => "preset_1",
			1 => "preset_2",
			2 => "preset_3",
			3 => "preset_4",
			4 => "preset_5",
			5 => "preset_6",
			6 => "preset_7",
			7 => "preset_8",
			8 => "preset_9",
		),
		"NUM" => "Y",
		"CHECKED" => rsFlyaway::getSettings('presets', 'preset_1'),
	),
	"sidemenuType" => array(
		"BLOCK_NAME" => "RS.FLW.SIDEMENU_TYPE",
		"VALUES" => array(
			0 => array(
				"val" =>"light",
				"name" => "RS.FLW.SIDEMENU_LIGHT",
			),
			1 => array(
				"val" =>"dark",
				"name" => "RS.FLW.SIDEMENU_DARK",
			)
		),
		"CHECKED" => rsFlyaway::getSettings('sidemenuType', 'dark'),
		'STICKY_HEADER' => array(
			'TYPE' => "SWITCH",
			"BLOCK_NAME" => "RS.FLW.STICKY_HEADER",
			"VAL" => rsFlyaway::getSettings('StickyHeader', 'Y')
		),
	)
);

$gencolor = rsFlyaway::getSettings('gencolor', 'ffe062');
list($rr,$gg,$bb) = sscanf($gencolor, '%2x%2x%2x');
$arResult['SETTINGS']["COLORS"]['GEN_COLOR']['NAME'] = "RS.FLW.GEN_COLOR";
$arResult['SETTINGS']["COLORS"]['GEN_COLOR']['HEX'] = $gencolor;
$arResult['SETTINGS']["COLORS"]['GEN_COLOR']['RGB']['R'] = $rr;
$arResult['SETTINGS']["COLORS"]['GEN_COLOR']['RGB']['G'] = $gg;
$arResult['SETTINGS']["COLORS"]['GEN_COLOR']['RGB']['B'] = $bb;

$secondColor = rsFlyaway::getSettings('secondColor', '555555');
list($rr,$gg,$bb) = sscanf($secondColor, '%2x%2x%2x');
$arResult['SETTINGS']["COLORS"]['SECOND_COLOR']['NAME'] = "RS.FLW.SECOND_COLOR";
$arResult['SETTINGS']["COLORS"]['SECOND_COLOR']['HEX'] = $secondColor;
$arResult['SETTINGS']["COLORS"]['SECOND_COLOR']['RGB']['R'] = $rr;
$arResult['SETTINGS']["COLORS"]['SECOND_COLOR']['RGB']['G'] = $gg;
$arResult['SETTINGS']["COLORS"]['SECOND_COLOR']['RGB']['B'] = $bb;

/*$arResult['SETTINGS']["CHECKBOX"]['blackMode']["VAL"] = rsFlyaway::getSettings('blackMode', 'N');
$arResult['SETTINGS']["CHECKBOX"]['blackMode']["NAME"] = "RS.FLW.BLACK_MODE";*/

$arResult["LEFT_SETTINGS"] = array(
	0 => array(
		"TYPE" => "RADIO",
		"VAL" => $arFirst,
		"BLOCK_NAME" => "RS.FLW.MAIN_SETTINGS_MENU",
	),
	2 => array(
		"TYPE" => "COLOR",
		"VAL" => $arResult['SETTINGS']["COLORS"],
		"BLOCK_NAME" => "RS.FLW.SETTING_COLOR",
	),
	3 => array(
		"TYPE" => "CHECKBOX",
		"VAL" => $arResult['SETTINGS']["CHECKBOX"],
	),
);

$arSecond = array(
	"bannerType" => array(
		"VALUES" => array(
			0 => array(
				"val" =>"type1",
				"name" => "RS.FLW.BANNER_TYPE1",
			),
			1 => array(
				"val" =>"type2",
				"name" => "RS.FLW.BANNER_TYPE2",
			),
			2 => array(
				"val" =>"type3",
				"name" => "RS.FLW.BANNER_TYPE3",
			),
			3 => array(
				"val" =>"type4",
				"name" => "RS.FLW.BANNER_TYPE4",
			),
			4 => array(
				"val" =>"type5",
				"name" => "RS.FLW.BANNER_TYPE5",
			),
		),
		"PIC" => "Y",
		"CHECKED" => rsFlyaway::getSettings('bannerType', 'type1'),
	),

);
$arThird = array(
	"filterSide" => array(
		"VALUES" => array(
			0 => array(
				"val" =>"left",
				"name" => "RS.FLW.FILTER_TYPE1",
			),
			1 => array(
				"val" =>"right",
				"name" => "RS.FLW.FILTER_TYPE2",
			),
		),
		"CHECKED" => rsFlyaway::getSettings('filterSide', 'left'),
	),
);

$arSwitch = array(
	"Fichi" => array(
		"val"=> rsFlyaway::getSettings('Fichi', 'Y'),
		"name" => "RS.FLW.FICHI",
	),
	"SmallBanners" => array(
		"val"=> rsFlyaway::getSettings('SmallBanners', 'Y'),
		"name" => "RS.FLW.SMALLBANNERS",
	),
	"New" => array(
		"val"=> rsFlyaway::getSettings('New', 'Y'),
		"name" => "RS.FLW.NEW",
	),
	"PopularItem" => array(
		"val"=> rsFlyaway::getSettings('PopularItem', 'Y'),
		"name" => "RS.FLW.POPULARITEM",
	),
	"Service" => array(
		"val"=> rsFlyaway::getSettings('Service', 'Y'),
		"name" => "RS.FLW.SERVICE",
	),
	"AboutAndReviews" => array(
		"val"=> rsFlyaway::getSettings('AboutAndReviews', 'Y'),
		"name" => "RS.FLW.ABOUTANDREVIEWS",
	),
	"News" => array(
		"val"=> rsFlyaway::getSettings('News', 'Y'),
		"name" => "RS.FLW.NEWS",
	),
	"Partners" => array(
		"val"=> rsFlyaway::getSettings('Partners', 'Y'),
		"name" => "RS.FLW.PARTNERS",
	),
	"Gallery" => array(
		"val"=> rsFlyaway::getSettings('Gallery', 'Y'),
		"name" => "RS.FLW.GALLERY",
	),
);


$arResult["RIGHT_SETTINGS"] = array(
	0 => array(
		"TYPE" => "RADIO",
		"VAL" => $arSecond,
		"BLOCK_NAME" => "RS.FLW.BANNER_TYPE",
	),
	1 => array(
		"TYPE" => "RADIO",
		"VAL" => $arThird,
		"BLOCK_NAME" => "RS.FLW.FILTER_TYPE",
	),
	2 => array(
		"TYPE" => "SWITCH",
		"VAL" => $arSwitch,
		"BLOCK_NAME" => "RS.FLW.CONTROL_MAIN_BLOCK"
	),
);

$this->IncludeComponentTemplate();
