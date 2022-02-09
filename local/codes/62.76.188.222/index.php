<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<!-- подключение нужных файлов (begin) -->
<link rel="stylesheet" href="/slider/b-slider.css">
<script src="/slider/slider.js"></script>
<!-- подключение нужных файлов (end) -->
<div class="b-slider__wrapper">
    <div class="b-slider__body">
        <div id="b-photo__section" class="b-slider__section active">
            <div class="b-slide__main m-photo_1">
                <div class="b-text_1">При покупке<br />зеркального<br />фотоаппарата</div>
                <div class="b-text_2">скидка на<br />объектив или вспышку</div>
                <div class="b-text_3">-10%</div>
                <img src="slider/b-img_2.png" alt="" class="b-img_2" />
                <img src="slider/b-img_1.png" alt="" class="b-img_1" />
                <img src="slider/b-img_3.png" alt="" class="b-img_4" />
                <img src="slider/b-img_3.png" alt="" class="b-img_3" />
                <img src="slider/b-img_5.png" alt="" class="b-img_5" />
                                                                    
                <div class="b-text_4">Время принять<br />профессиональное решение</div>
                <div class="b-text_5">Быстрая и мощная, SMART-камера NX300<br />поймает самые интересные моменты<br/ >жизни и позволит поделиться ими<br />с друзьями в миг.</div>
            </div>
            <div class="b-slide__small m-photo_2">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2">Стильный<br />компактный<br /><span class="m-photo__h2_normal">IXUS 550 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
            <div class="b-slide__small m-photo_3">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2">Фотографируйте<br />даже под водой!<br /><span class="m-photo__h2_normal">POWERSHOT D10 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
            <div class="b-slide__small m-photo_4">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2">видеокамеры<br />высокой четкости<br /><span class="m-photo__h2_normal">LEGRIA HF M56 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
            <div class="b-slide__small m-photo_5">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2 m-black">Печать дома –<br />это просто!<br /><span class="m-photo__h2_normal">PIXMA iP4940 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
        </div>
        <div id="b-tech__section" class="b-slider__section">
            <div class="b-slide__main m-tech_1">
                <div class="m-tech-text_1">При покупке образца<br />встраиваемой техники</div>
                <div class="m-tech-text_2">-10%</div>
                                                                    
                <div class="m-tech-text_3">При покупке образца<br />встраиваемой техники</div>
                <div class="m-tech-text_4">-10%</div>
            </div>
            <div class="b-slide__small m-tech_2">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2 m-black">Полностью<br />автоматическая<br /><span class="m-photo__h2_normal">IQ 800 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
            <div class="b-slide__small m-tech_3">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2 m-black">Праздник вкуса!<br /><span class="m-photo__h2_normal">miele ST5200 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
            <div class="b-slide__small m-tech_4">
                <a href="#" class="b-slide__link">
                    <h2 class="m-photo__h2 m-black">Идеальный набор<br />для вашей кухни<br /><span class="m-photo__h2_normal">bosch - ERV100 <span class="b-slide__arrow">»</span></span></h2>
                </a>
            </div>
        </div>
    </div>
    <div class="b-slider__menu">
        <a href="#" class="b-slider-menu__item m-slider-menu__comp">Компьютерная техника</a>
        <a href="#" class="b-slider-menu__item">Автомобильная техника</a>
        <a href="#b-tech__section" class="b-slider-menu__item">Бытовая техника</a>
        <a href="#b-photo__section" class="b-slider-menu__item active">Фото, видео, сотовые</a>
    </div>
</div>
    <?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"interesting",
	Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "materials",
		"IBLOCK_ID" => "6",
		"NEWS_COUNT" => "4",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(),
		"PROPERTY_CODE" => array(),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N"
	)
);?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");


