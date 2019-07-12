<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="<?=SITE_DIR?>favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/styles.css">
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/prettyPhoto.css">
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/owl.carousel.css">
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.custom-scrollbar.css">
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/fancySelect.css">
    <link href="<?=SITE_TEMPLATE_PATH?>/css/masterslider.css" type='text/css' rel="stylesheet"/>
    <link href='<?=SITE_TEMPLATE_PATH?>/css/ms-lightbox.css' rel='stylesheet' type='text/css'>

    <script type="text/javascript">
        var SITE_DIR = '<?=SITE_DIR?>';
        var INNET_CATALOG_COMPARE_LIST = new Object();
        var INNET_DELAY_LIST = new Object();
        var INNET_ADMIN = <?if ($USER->IsAdmin()){?>true<?}else{?>false<?}?>;
    </script>

	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.11.1.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.bxslider.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.prettyPhoto.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/owl.carousel.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/ion.rangeSlider.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.custom-scrollbar.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/fancySelect.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/masterslider.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/sly.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/plugins.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/script.js"></script>

    <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowHead();?>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<?
if ($APPLICATION->GetCurPage(false) === SITE_DIR) {
    $index = true;
} else {
    $no_index = true;
}

IncludeTemplateLangFile(__FILE__);
?>

<?if ($index):?>
    <style>
        .content {padding: 0 0 100px;}
    </style>
<?endif;?>

<div class="wrapper">
	<div class="header-block">
		<div class="header top default">
			<?/*<div class="lvl0">
				<div class="container clearfix">
					<div class="pull-left showmenu">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/top_menu.php", "EDIT_TEMPLATE" => "" ), false );?>
					</div>
                    <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "selection", Array(
                            "REGISTER_URL" => SITE_DIR . "auth/?register=yes",
                            "FORGOT_PASSWORD_URL" => "",
                            "PROFILE_URL" => SITE_DIR . "personal/profile/",
                            "SHOW_ERRORS" => "Y",
                        ),
                        false
                    );?>
				</div>
			</div>*/?>
			<div class="lvl1">
				<div class="inner in-row-mid">
                    <div class="col1">
                        <a href="<?=SITE_DIR?>">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/logo.php", "EDIT_TEMPLATE" => "" ), false );?>
                        </a>
                    </div>
					<div class="col2">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/slogan.php", "EDIT_TEMPLATE" => "" ), false );?>
                    </div>
					<div class="col3">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_1.php", "EDIT_TEMPLATE" => "" ), false );?>
					</div>
					<div class="col4">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_1.php", "EDIT_TEMPLATE" => "" ), false );?>
						<a class="btn popbutton" data-window="1"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/callback.php", "EDIT_TEMPLATE" => "" ), false );?></a>
					</div>
				</div>
			</div>
			<div class="lvl2">
				<div class="inner clearfix" style="position: relative;">
					<?$APPLICATION->IncludeComponent("bitrix:menu", "header", array(
							"ROOT_MENU_TYPE" => "top",
							"MENU_CACHE_TYPE" => "A",
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_USE_GROUPS" => "N",
							"MENU_CACHE_GET_VARS" => array(),
							"MAX_LEVEL" => "3",
							"CHILD_MENU_TYPE" => "top_child",
							"USE_EXT" => "N",
							"DELAY" => "N",
							"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>
					<div class="col2 flr"><a class="btn-search"></a></div>
					<?$APPLICATION->IncludeComponent("bitrix:search.title", "top_fixed", array(
							"NUM_CATEGORIES" => "1",
							"TOP_COUNT" => "10",
							"ORDER" => "date",
							"USE_LANGUAGE_GUESS" => "Y",
							"CHECK_DATES" => "Y",
							"SHOW_OTHERS" => "N",
							"PAGE" => SITE_DIR . "search/index.php",
							"CATEGORY_0_TITLE" => "",
							"CATEGORY_0" => array(
								0 => "no",
							),
							"CATEGORY_0_iblock_catalog" => array(
								0 => "all",
							),
							"SHOW_INPUT" => "Y",
							"INPUT_ID" => "title-search-input-fixed",
							"CONTAINER_ID" => "title-search-fixed"
						),
						false
					);?>
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_1.php", "EDIT_TEMPLATE" => "" ), false );?>
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_1.php", "EDIT_TEMPLATE" => "" ), false );?>
					<a class="btn popbutton callback" data-window="1"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/callback.php", "EDIT_TEMPLATE" => "" ), false );?></a>
				</div>
			</div>
			<div class="lvl3">
				<div class="inner">
					<?$APPLICATION->IncludeComponent("bitrix:search.title", "top", array(
							"NUM_CATEGORIES" => "1",
							"TOP_COUNT" => "10",
							"ORDER" => "date",
							"USE_LANGUAGE_GUESS" => "Y",
							"CHECK_DATES" => "Y",
							"SHOW_OTHERS" => "N",
							"PAGE" => SITE_DIR . "search/index.php",
							"CATEGORY_0_TITLE" => "",
							"CATEGORY_0" => array(
								0 => "no",
							),
							"CATEGORY_0_iblock_catalog" => array(
								0 => "all",
							),
							"SHOW_INPUT" => "Y",
							"INPUT_ID" => "title-search-input",
							"CONTAINER_ID" => "title-search"
						),
						false
					);?>
				</div>
			</div>
		</div><!-- .header -->
	</div>
    <div class="content">
        <?if ($index){?>
            <?$APPLICATION->IncludeComponent(
                "innet:main.slider",
                "innet",
                array(
                    "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                    "IBLOCK_ID" => "#INNET_IBLOCK_ID_SLIDER#",
                    "COUNT_ELEMENTS" => "10",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "INNET_SLIDE_PAUSE" => "4",
                    "INNET_SLIDE_SPEED" => "1",
                ),
                false
            );?>
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/advantages.php", "EDIT_TEMPLATE" => "" ), false );?>
            <?$GLOBALS["arrFilter"] = array("PROPERTY_KEY_SERVICE_VALUE" => "Y");?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "services_1",
                array(
                    "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                    "IBLOCK_ID" => "#INNET_IBLOCK_ID_SERVICES#",
                    "NEWS_COUNT" => "10",
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "",
                    "SORT_ORDER2" => "",
                    "FILTER_NAME" => "arrFilter",
                    "FIELD_CODE" => array(
                        0 => "ID",
                    ),
                    "PROPERTY_CODE" => array(),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "j F Y",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "SHOW_404" => "N",
                    "MESSAGE_404" => ""
                ),
                false
            );?>
        <?}?>

        <div class="inner">
            <?if ($no_index){?>
                <br/>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "innet",
                    array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "-"
                    ),
                    false
                );?>
                <h1 class="title" style="margin-top: 30px;"><span><?=$APPLICATION->ShowTitle(false)?></span></h1>
            <?}?>