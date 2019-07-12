<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Видеогалерея");
?>
Данный раздел предназначен для видеороликов. Чтобы в данном разделе разместить видео необходимо: в администрировании, в инфоблоке <b>Видеогалерея</b> в разделе <b>Школьные годы</b> добавить видео ролик, в карточке элемента в поле <b>Файл</b> загрузите видео.
<?$APPLICATION->IncludeComponent("bitrix:iblock.tv", "section_list", array(
	"IBLOCK_TYPE" => "photos",
	"IBLOCK_ID" => "#VIDEO_IBLOCK_ID#",
	"ALLOW_SWF" => "Y",
	"PATH_TO_FILE" => "#VIDEO_PROPERTY_ID#",
	"DURATION" => "",
	"WIDTH" => "600",
	"HEIGHT" => "450",
	"LOGO" => "/bitrix/components/bitrix/iblock.tv/templates/.default/images/logo.png",
	"SECTION_ID" => "",
	"SHOW_COUNTER_EVENT" => "Y",
	"STAT_EVENT" => "Y",
	"STAT_EVENT1" => "player",
	"STAT_EVENT2" => "start_playing",
	"DEFAULT_SMALL_IMAGE" => "/bitrix/components/bitrix/iblock.tv/templates/.default/images/default_small.png",
	"DEFAULT_BIG_IMAGE" => "/bitrix/components/bitrix/iblock.tv/templates/.default/images/default_big.png",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>