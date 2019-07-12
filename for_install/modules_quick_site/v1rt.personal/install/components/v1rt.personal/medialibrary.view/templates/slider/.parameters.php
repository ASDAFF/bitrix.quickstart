<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arNivoEffects = array(
    "random"            => GetMessage("NIVO_EFFECT_RANDOM"),
    "fold"              => GetMessage("NIVO_EFFECT_FOLD"),
    "fade"              => GetMessage("NIVO_EFFECT_FADE"),
    "sliceDown"         => GetMessage("NIVO_EFFECT_SD"),
    "sliceDownLeft"     => GetMessage("NIVO_EFFECT_SDL"),
    "sliceUp"           => GetMessage("NIVO_EFFECT_SU"),
    "sliceUpLeft"       => GetMessage("NIVO_EFFECT_SUP"),
    "sliceUpDown"       => GetMessage("NIVO_EFFECT_SUD"),
    "sliceUpDownLeft"   => GetMessage("NIVO_EFFECT_SUDL")
);

$arTemplateParameters = array(
	"NIVO_EFFECT" => Array(
		"NAME" => GetMessage("NIVO_EFFECT"),
		"TYPE" => "LIST",
        "VALUES" => $arNivoEffects
	),
    
	"NIVO_ANIMSPEED" => Array(
		"NAME" => GetMessage("NIVO_ANIMSPEED"),
		"TYPE" => "TEXTBOX",
		"DEFAULT" => "500",
	),
    
	"NIVO_PAUSETIME" => Array(
		"NAME" => GetMessage("NIVO_PAUSETIME"),
		"TYPE" => "TEXTBOX",
		"DEFAULT" => "5000",
	),
    
	"NIVO_CONTROLNAV" => Array(
		"NAME" => GetMessage("NIVO_CONTROLNAV"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
	),
    
    "NIVO_PAUSEOFHOVER" => Array(
		"NAME" => GetMessage("NIVO_PAUSEOFHOVER"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
	),
    
    "NIVO_DIRNAV" => Array(
		"NAME" => GetMessage("NIVO_DIRNAV"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
	),
);
?>