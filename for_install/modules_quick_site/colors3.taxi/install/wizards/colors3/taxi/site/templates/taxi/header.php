<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
$curPage = $APPLICATION->GetCurPage(true);
?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <title><?$APPLICATION->ShowTitle();?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?$APPLICATION->ShowHead();?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/bootstrap/css/bootstrap.min.css');?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/bootstrap/css/bootstrap-responsive.min.css');?>
    <?$APPLICATION->SetAdditionalCSS('http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/ru/colorbox/colorbox.css');?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/style.css');?>

    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/thm_#COLOR#.css');?>
          
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700|PT+Sans+Narrow:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <link rel="shortcut icon" type="image/ico" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" type="image/ico" />
</head>
<body>
    <div id="panel" data-site="<?=SITE_DIR;?>"><?$APPLICATION->ShowPanel();?></div>
    <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		array("AREA_FILE_SHOW" => "file",
			"PATH" => SITE_DIR."include/map.php"),
		false);
	?>
	
	<script>
		window.order = "";
		window.crew = "";
		window.source = "";
		window.order_city = "";			
	</script>
	
    <header class="clearfix">
		<?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array("AREA_FILE_SHOW" => "file",
                "PATH" => SITE_DIR."include/app.php"),
            false);
        ?>
        <div class="top_border"></div>
        <div class="container rel">
            <div class="row">
    			<div class="span2 logo">
					<style type="text/css">
						@media (min-width: 767px) {
							.logo img{
								width: 100%;
							}
						}
                       </style>
                    <?if ($curPage == SITE_DIR."index.php"):?>                        
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array("AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/logo.php"),
							false);
						?>
                    <?else:?>
                        <style type="text/css">
                            @media (max-width: 767px) {
                                .logo {
                                    margin-top: 8px;
                                }
                            }
                        </style>
                        <a title="<?=GetMessage("COLORS3_TAXI_PEREYTI_NA_GLAVNUU_S")?>" href="<?=SITE_DIR?>">
							<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array("AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/logo.php"),
							false);
						?>
						</a>
                    <?endif?>
                </div>
                <div class="span10 head_right_bl clearfix">    			                    
                    <div class="row headertext">
                        <div class="span3 logo_text">
                            <p class="logo_txt">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array("AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/name.php"),
                                    false);
                                ?>
                            </p>
                        </div>	
                        <div class="span4 phone">	
                			<div class="clearfix phone">
                                <div class="phone_txt"><strong><?=GetMessage("COLORS3_TAXI_ZAKAZATQ_TAKSI")?><br /><?=GetMessage("COLORS3_TAXI_PO_TELEFONU")?></strong></div>
           			            <div class="phone_number">
                                    <p>
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:main.include",
                                            "",
                                            array("AREA_FILE_SHOW" => "file",
                                                "PATH" => SITE_DIR."include/code_tel.php"),
                                            false);
                                        ?>
                                    </p>
                                    <h3>
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:main.include",
                                            "",
                                            array("AREA_FILE_SHOW" => "file",
                                                "PATH" => SITE_DIR."include/tel.php"),
                                            false);
                                        ?>
                                    </h3>
                                </div>
                            </div>
            			</div>
            			<div class="span3 head_btn">
            				<!--a href="<?=SITE_DIR?>include/call.php" role="button" class="btn call_me thickbox" data-toggle="modal"><?=GetMessage("COLORS3_TAXI_PEREZVONITE_MNE")?></a-->
    						<a href="<?=SITE_DIR?>include/call.php" role="button" class="btn call_me colorbox_form"><?=GetMessage("COLORS3_TAXI_PEREZVONITE_MNE")?></a>
                            <?if ($curPage != SITE_DIR."index.php"):?>
                                <a class="btn btn-inverse" role="button" href="<?=SITE_DIR?>#order"><?=GetMessage("COLORS3_TAXI_ZAKAZATQ_TAKSI")?></a>
                            <?endif;?>
            			</div>  
                    </div>
                    <?
                        $APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", array(
                        	"ROOT_MENU_TYPE" => "top",
                        	"MENU_CACHE_TYPE" => "Y",
                        	"MENU_CACHE_TIME" => "36000000",
                        	"MENU_CACHE_USE_GROUPS" => "Y",
                        	"MENU_CACHE_GET_VARS" => array(
                        	),
                        	"MAX_LEVEL" => "1",
                        	"CHILD_MENU_TYPE" => "left",
                        	"USE_EXT" => "N",
                        	"DELAY" => "N",
                        	"ALLOW_MULTI_SELECT" => "N"
                        	),
                        	false
                        );
                    ?>
                </div>	
    		</div>
        </div>
    </header>


<?if ($curPage == SITE_DIR."index.php"):?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array("AREA_FILE_SHOW" => "file",
            "PATH" => SITE_DIR."include/benefits.php"),
        false);
    ?>
    <div class="zakaz">
        <div class="container">
            <h2 class="zak_h2"><a name="order"></a><?=GetMessage("ORDER_TAXI");?></h2>
            <div class="row">
                    <?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "order", array(
	"IBLOCK_TYPE" => "orders",
	"IBLOCK_ID" => "#ORDERS_IBLOCK_ID#",
	"STATUS_NEW" => "ANY",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => GetMessage("COLORS3_TAXI_SPASIBO_VAS_ZAKAZ_P"),
	"USER_MESSAGE_ADD" => GetMessage("COLORS3_TAXI_SPASIBO_VAS_ZAKAZ_P"),
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "#PROP_1#",
		2 => "#PROP_2#",
		3 => "#PROP_3#",
		4 => "#PROP_4#",
		5 => "#PROP_5#",
		6 => "#PROP_6#",
		7 => "#PROP_7#",
		8 => "#PROP_8#",
		9 => "#PROP_9#",
		10 => "#PROP_10#",
		11 => "#PROP_11#",
		12 => "#PROP_12#",
		13 => "#PROP_13#",
		14 => "#PROP_14#",
		15 => "#PROP_15#",
		16 => "#PROP_16#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "#PROP_2#",		
		1 => "#PROP_7#",
		2 => "#PROP_12#",
	),
	"GROUPS" => array(
		0 => "2",
	),
	"STATUS" => "ANY",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => SITE_DIR,
	"CUSTOM_TITLE_NAME" => "",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => ""
	),
	false
);?>
                    
                    
                    <div class="span6 yamap ac">
                        <div class="pokazat"><a class="pokaz_karty active"><?=GetMessage("SHOW_ON_MAP");?></a></div>
                        <div id="map" style="width: 100%; height: 400px;"></div>
                        <div id="error"></div>
                    </div>
                </div>
                <div class="span car_clipped">
                    <img src="<?=SITE_TEMPLATE_PATH?>/i/thm_#COLOR#/src/car_clipped.png" alt="" />
                </div>
        </div>
    </div>

    <div class="clearfix corp">
        <div class="container">
                <div class="row-fluid">
                    <div class="span4 widget">
                        <div class="clearfix cont">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array("AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/about.php"),
                                false);
                            ?>
                        </div>
                    </div>
                    <div class="span4 widget">
                        <div class="clearfix cont">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                            	"AREA_FILE_SHOW" => "file",
                            	"PATH" => SITE_DIR."include/services.php",
                            	"EDIT_TEMPLATE" => ""
                            	),
                            	false
                            );
                            ?>
                        </div>        
                    </div>
                    <div class="span4 widget">
                        <?$APPLICATION->IncludeComponent("bitrix:news.list", "news", array(                            
                        	"IBLOCK_TYPE" => "news",
                            "IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
                            "NEWS_COUNT" => "1",
                            "SORT_BY1" => "ACTIVE_FROM",
                            "SORT_ORDER1" => "DESC",
                            "SORT_BY2" => "SORT",
                            "SORT_ORDER2" => "ASC",
                            "FILTER_NAME" => "",
                            "FIELD_CODE" => array(
                              0 => "",
                              1 => "",
                            ),
                            "PROPERTY_CODE" => array(
                              0 => "",
                              1 => "",
                            ),
                            "CHECK_DATES" => "Y",
                            "DETAIL_URL" => SITE_DIR."news/#ELEMENT_ID#/",
                            "AJAX_MODE" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "N",
                            "AJAX_OPTION_HISTORY" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "PREVIEW_TRUNCATE_LEN" => "",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "SET_TITLE" => "Y",
                            "SET_STATUS_404" => "Y",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                            "PARENT_SECTION" => "",
                            "PARENT_SECTION_CODE" => "",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => GetMessage("COLORS3_TAXI_NOVOSTI"),
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => "",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "DISPLAY_DATE" => "Y",
                            "DISPLAY_NAME" => "Y",
                            "DISPLAY_PICTURE" => "N",
                            "DISPLAY_PREVIEW_TEXT" => "Y",
                            "AJAX_OPTION_ADDITIONAL" => ""
                            ),
                            false
                          );?>
                    </div>
                </div>
        </div>
    </div>
<?else:?>
    <div class="conteiner container">
        <div class="row-fluid">
            <div class="clearfix span8" id="content">
                <div class="in clearfix">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR."include/marquee.php"
							),
							false,
							array(
							"ACTIVE_COMPONENT" => "N"
							)
						);
					?> 			
<?endif;?>