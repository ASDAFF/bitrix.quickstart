<div class="gallery">
<h2><a href="about/mediagallery/">Фотогалерея</a> <i class="line"></i></h2>
<div class="galleryWrap">
    <div id="photo">
<?$APPLICATION->IncludeComponent("bitrix:gallery.view", ".default", array(
	"IBLOCK_TYPE" => "photos",
	"IBLOCK_ID" => "#PHOTOGALLERY_IBLOCK_ID#",
	"SECTION_ID" => "",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"INCLUDE_SUBSECTIONS" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600000",
	"GALLERY_ID" => "main",
	"GALLERY_SKIN" => "jcarousel-skin-tango",
	"GALLERY_CSS" => "",
	"SMALL_IMAGE_WIDTH" => "70",
	"SMALL_IMAGE_HEIGHT" => "70",
	"SHOW_BIG_IMAGE" => "N",
	"BIG_IMAGE_WIDTH" => "555",
	"BIG_IMAGE_HEIGHT" => "1200",
	"USE_PRELOADER" => "N",
	"SHOW_IMAGE_CAPTIONS" => "N"
	),
	false
);?>
</div>
</div>
</div>