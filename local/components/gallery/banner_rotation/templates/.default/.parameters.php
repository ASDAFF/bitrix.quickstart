<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPagerPositions = array(
	'top_left' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPLEFT"),
	'top_right' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPRIGHT"),
	'bottom_left' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMLEFT"),	
	'bottom_right' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMRIGHT"),
);	

if ($arCurrentValues["PAGER_STYLE"] == 'thumbs') {
	$arPagerPositions = array(
		//'left' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPLEFT"),
		//'right' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPRIGHT"),
		'bottom' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMCENTER"),
	);	
}

if ($arCurrentValues["PAGER_STYLE"] == 'amazon') {
	$arPagerPositions = array(
		'top' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPCENTER"),
	);	
}

if ($arCurrentValues["PAGER_STYLE"] == 'bulls') {
	$arPagerPositions = array(
		'top_left' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPLEFT"),
		'top_center' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPCENTER"),
		'top_right' => GetMessage("BEONO_BANNER_PAGER_POSITION_TOPRIGHT"),
		'bottom_left' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMLEFT"),	
		'bottom_center' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMCENTER"),
		'bottom_right' => GetMessage("BEONO_BANNER_PAGER_POSITION_BOTTOMRIGHT"),
	);	
}

$arTemplateParameters = array(
	"JQUERY" => array( 
		"NAME" => GetMessage('BEONO_BANNER_JQUERY'),
		"PARENT" => "VISUAL",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",	
	),
	"PAGER_STYLE" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_PAGER_STYLE"),
		"TYPE" => "LIST",
		"VALUES" => array( 
			'text' => GetMessage("BEONO_BANNER_STYLE_TEXT"),
			'digits' => GetMessage("BEONO_BANNER_STYLE_DIGITS"),
			'bulls' => GetMessage("BEONO_BANNER_STYLE_BULLS"),
			'arrows' => GetMessage("BEONO_BANNER_STYLE_ARROWS"),
			'arrows_edge' => GetMessage("BEONO_BANNER_STYLE_ARROWSEDGE"),
			'thumbs' => GetMessage("BEONO_BANNER_STYLE_THUMBS"),
			'amazon' => GetMessage("BEONO_BANNER_STYLE_AMAZON"),
			'none' => GetMessage("BEONO_BANNER_STYLE_NONE"),
		),
		"REFRESH" => "Y",
		"DEFAULT" => ''
	),
	"PAGER_ORIENT" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_PAGER_ORIENT"),
		"TYPE" => "LIST",
		"VALUES" => array(
			'horizontal' => GetMessage("BEONO_BANNER_PAGER_ORIENT_H"),
			'vertical' => GetMessage("BEONO_BANNER_PAGER_ORIENT_V")
		),
		"DEFAULT" => ''
	),
	"PAGER_POSITION" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_PAGER_POSITION"),
		"TYPE" => "LIST",
		"VALUES" => $arPagerPositions,
		"DEFAULT" => ''
	),
	"WIDTH" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_WIDTH"),
		"TYPE" => "STRING",
		"VALUES" => "",
		"DEFAULT" => '100%'
	),
	"HEIGHT" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_HEIGHT"),
		"TYPE" => "STRING",
		"VALUES" => "",
		"DEFAULT" => '200px'
	),
	"EFFECT" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_EFFECT"),
		"TYPE" => "LIST",
		"VALUES" => array(
			'fade' => GetMessage("BEONO_BANNER_EFFECT_FADE"),
			'slide_h' => GetMessage("BEONO_BANNER_EFFECT_SLIDE_H"),
			'slide_v' => GetMessage("BEONO_BANNER_EFFECT_SLIDE_V")
		), 
		"DEFAULT" => ''
	),	
	"TRANSITION_SPEED" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_TRANSITION_SPEED"),
		"TYPE" => "STRING",
		"VALUES" => "",
		"DEFAULT" => '300'
	),
	"TRANSITION_INTERVAL" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_TRANSITION_INTERVAL"),
		"TYPE" => "STRING",
		"VALUES" => "",
		"DEFAULT" => '5000'
	),
	"STOP_ON_FOCUS" => Array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("BEONO_BANNER_STOP_ON_FOCUS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'N'
	),
);

if ($arCurrentValues['SOURCE'] == 'medialib') {
	unset($arTemplateParameters['PAGER_STYLE']['VALUES']['thumbs']);	
}

if (in_array($arCurrentValues["PAGER_STYLE"], array('thumbs', 'none'))) {
	unset($arTemplateParameters['PAGER_ORIENT']);	
}

if (in_array($arCurrentValues["PAGER_STYLE"], array('arrows', 'arrows_edge', 'amazon'))) {
	unset($arTemplateParameters['PAGER_ORIENT'], $arTemplateParameters['PAGER_POSITION']);	
}

?>