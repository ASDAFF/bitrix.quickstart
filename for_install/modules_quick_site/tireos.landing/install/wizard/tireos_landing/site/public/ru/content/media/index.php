<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Видео и аудио");
?>
<h2>Видео</h2>
<?$APPLICATION->IncludeComponent("bitrix:iblock.tv", ".default", array(
	"IBLOCK_TYPE" => "services",
	"IBLOCK_ID" => "4",
	"PATH_TO_FILE" => "15",
	"DURATION" => "16",
	"SECTION_ID" => "9",
	"ELEMENT_ID" => "15",
	"WIDTH" => "400",
	"HEIGHT" => "300",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600"
	),
	false
);?>
<br />

<h2>Аудио (MP3)</h2>
<?$APPLICATION->IncludeComponent("bitrix:player", ".default", array(
	"PATH" => "/bitrix/sounds/main/bitrix_tune_mobile.mp3",
	"WIDTH" => "400",
	"HEIGHT" => "24",
	"AUTOSTART" => "N",
	"REPEAT" => "N",
	"VOLUME" => "90",
	"ADVANCED_MODE_SETTINGS" => "N",
	"PLAYER_TYPE" => "auto",
	"USE_PLAYLIST" => "N",
	"PREVIEW" => "",
	"DISPLAY_CLICK" => "play",
	"MUTE" => "N",
	"HIGH_QUALITY" => "Y",
	"BUFFER_LENGTH" => "10",
	"DOWNLOAD_LINK" => "",
	"DOWNLOAD_LINK_TARGET" => "_self",
	"LOGO" => "",
	"FULLSCREEN" => "Y",
	"SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins",
	"SKIN" => "bitrix.swf",
	"CONTROLBAR" => "bottom",
	"WMODE" => "transparent",
	"HIDE_MENU" => "N"
	),
	false
);?>
<br />

<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>