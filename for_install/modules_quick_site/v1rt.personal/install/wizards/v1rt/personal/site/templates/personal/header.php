<?
IncludeTemplateLangFile(__FILE__);
if(!IsModuleInstalled("v1rt.personal")) die('<p style="color:tomato;">'.GetMessage("V1RT_PERSONAL_INSTALL").'</p>');
if(!CModule::IncludeModule("v1rt.personal")) die();

$type = COption::GetOptionString("v1rt.personal", "v1rt_personal_type_header");
if($type == 1)
{
    $image = COption::GetOptionString("v1rt.personal", "v1rt_personal_header_image");
    if($image == "")
        $type = 2;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link href="<?=SITE_TEMPLATE_PATH?>/css-js/reset.css" rel="stylesheet" type="text/css" />
    <script src="<?=SITE_TEMPLATE_PATH?>/css-js/jquery-1.8.2.min.js" type="text/javascript"></script>    
    <?$APPLICATION->ShowHead()?>
    <title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>
<?$APPLICATION->ShowPanel();?>
<table class="table-wrapper">
	<tr>
		<td class="logo-menu"><a href="<?=SITE_DIR?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/logo.#EXT#" class="logo" alt=""/></a>
        <?$APPLICATION->IncludeComponent("bitrix:menu", "v1rt.personal.top", Array(
       	        "ROOT_MENU_TYPE" => "top",
               	"MENU_CACHE_TYPE" => "N",
               	"MENU_CACHE_TIME" => "3600",
               	"MENU_CACHE_USE_GROUPS" => "Y",
               	"MENU_CACHE_GET_VARS" => "",
               	"MAX_LEVEL" => "1",
               	"CHILD_MENU_TYPE" => "left",
               	"USE_EXT" => "N",
               	"DELAY" => "N",
               	"ALLOW_MULTI_SELECT" => "N",
           	),
           	false
        );?>	
        </td>
	</tr>
	<tr>
		<td class="header">
        <?if($type == 0):?>
            <?$APPLICATION->IncludeComponent("v1rt.personal:medialibrary.view", "slider", array(
            	"FOLDERS" => "#MEDIA_FOLDER_SLIDER#",
            	"VARIABLE" => "",
            	"COUNT_IMAGE" => "5",
            	"RANDOM" => "Y",
            	"TITLE" => "N",
            	"CACHE_TYPE" => "A",
            	"CACHE_TIME" => "3600",
            	"PAGE_NAV_MODE" => "N",
            	"ELEMENT_PAGE" => "5",
            	"PAGER_SHOW_ALL" => "N",
            	"PAGER_SHOW_ALWAYS" => "N",
            	"PAGER_TITLE" => "",
            	"PAGER_TEMPLATE" => "",
            	"RESIZE_MODE" => "F",
            	"RESIZE_MODE_W" => "980",
            	"RESIZE_MODE_H" => "300",
            	"PAGE_LINK" => "",
            	"PAGE_LINK_TEXT" => "",
            	"LOAD_JS" => "Y",
            	"NIVO_EFFECT" => "random",
            	"NIVO_ANIMSPEED" => "500",
            	"NIVO_PAUSETIME" => "5000",
            	"NIVO_CONTROLNAV" => "N",
            	"NIVO_PAUSEOFHOVER" => "N",
            	"NIVO_DIRNAV" => "N"
            	),
            	false
            );?>
        <?elseif($type == 1):?>
            <img src="<?=$image?>" width="980" alt=""/>
        <?endif;?>
        </td>
	</tr>
	<tr>
		<td class="center">
			<table class="center-wrapper">
				<tr>
					<td class="sidebar"><p class="ph1">#NAME_SECTION_LAST#</p>
                        <?$APPLICATION->IncludeComponent("bitrix:news.list", "v1rt.personal.news.last", Array(
                        	"IBLOCK_TYPE" => "personal",
                        	"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
                        	"NEWS_COUNT" => "1",
                        	"SORT_BY1" => "ACTIVE_FROM",
                        	"SORT_ORDER1" => "DESC",
                        	"SORT_BY2" => "SORT",
                        	"SORT_ORDER2" => "ASC",
                        	"FILTER_NAME" => "",
                        	"FIELD_CODE" => array(),
                        	"PROPERTY_CODE" => array(),
                        	"CHECK_DATES" => "Y",
                        	"DETAIL_URL" => "",
                        	"AJAX_MODE" => "N",
                        	"AJAX_OPTION_JUMP" => "N",
                        	"AJAX_OPTION_STYLE" => "Y",
                        	"AJAX_OPTION_HISTORY" => "N",
                        	"CACHE_TYPE" => "A",
                        	"CACHE_TIME" => "36000000",
                        	"CACHE_FILTER" => "N",
                        	"CACHE_GROUPS" => "Y",
                        	"PREVIEW_TRUNCATE_LEN" => "",
                        	"ACTIVE_DATE_FORMAT" => "d.m.Y",
                        	"SET_TITLE" => "N",
                        	"SET_STATUS_404" => "Y",
                        	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        	"ADD_SECTIONS_CHAIN" => "N",
                        	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        	"PARENT_SECTION" => "",
                        	"PARENT_SECTION_CODE" => "",
                        	"DISPLAY_TOP_PAGER" => "N",
                        	"DISPLAY_BOTTOM_PAGER" => "N",
                        	"PAGER_TITLE" => "",
                        	"PAGER_SHOW_ALWAYS" => "N",
                        	"PAGER_TEMPLATE" => "",
                        	"PAGER_DESC_NUMBERING" => "N",
                        	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        	"PAGER_SHOW_ALL" => "N",
                        	"DISPLAY_DATE" => "Y",
                        	"DISPLAY_NAME" => "Y",
                        	"DISPLAY_PICTURE" => "N",
                        	"DISPLAY_PREVIEW_TEXT" => "N",
                        	"AJAX_OPTION_ADDITIONAL" => "",
                        	),
                        	false
                        );?>
						<p><a href="#SITE_DIR#news/"><?=GetMessage("ALL")?> #NAME_SECTION_ALL# &gt;&gt;</a></p>
						<p class="ph1"><?=GetMessage("LAST_COMMENT")?></p>
						<?$APPLICATION->IncludeComponent("bitrix:news.list", "v1rt.personal.comment.last", array(
                        	"IBLOCK_TYPE" => "personal",
                        	"IBLOCK_ID" => "#COMMENTS_IBLOCK_ID#",
                        	"NEWS_COUNT" => "2",
                        	"SORT_BY1" => "ACTIVE_FROM",
                        	"SORT_ORDER1" => "DESC",
                        	"SORT_BY2" => "SORT",
                        	"SORT_ORDER2" => "ASC",
                        	"FILTER_NAME" => "",
                        	"FIELD_CODE" => array(),
                        	"PROPERTY_CODE" => array(
                        		0 => "ID_RECORD",
                        	),
                        	"CHECK_DATES" => "Y",
                        	"DETAIL_URL" => "",
                        	"AJAX_MODE" => "N",
                        	"AJAX_OPTION_JUMP" => "N",
                        	"AJAX_OPTION_STYLE" => "Y",
                        	"AJAX_OPTION_HISTORY" => "N",
                        	"CACHE_TYPE" => "A",
                        	"CACHE_TIME" => "36000000",
                        	"CACHE_FILTER" => "N",
                        	"CACHE_GROUPS" => "Y",
                        	"PREVIEW_TRUNCATE_LEN" => "",
                        	"ACTIVE_DATE_FORMAT" => "d.m.Y",
                        	"SET_TITLE" => "N",
                        	"SET_STATUS_404" => "Y",
                        	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        	"ADD_SECTIONS_CHAIN" => "N",
                        	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        	"PARENT_SECTION" => "",
                        	"PARENT_SECTION_CODE" => "",
                        	"DISPLAY_TOP_PAGER" => "N",
                        	"DISPLAY_BOTTOM_PAGER" => "N",
                        	"PAGER_TITLE" => "",
                        	"PAGER_SHOW_ALWAYS" => "N",
                        	"PAGER_TEMPLATE" => "",
                        	"PAGER_DESC_NUMBERING" => "N",
                        	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        	"PAGER_SHOW_ALL" => "N",
                        	"AJAX_OPTION_ADDITIONAL" => ""
                        	),
                        	false
                        );?>
<p class="ph1"><?=GetMessage("RANDOM_IMG")?></p>
                        <div>
                            <?$APPLICATION->IncludeComponent("v1rt.personal:medialibrary.view", "preview", array(
                            	"FOLDERS" => "#MEDIA_FOLDER_MOSCOW#",
                            	"VARIABLE" => "",
                            	"COUNT_IMAGE" => "9",
                            	"RANDOM" => "Y",
                            	"TITLE" => "N",
                            	"CACHE_TYPE" => "N",
                            	"CACHE_TIME" => "3600",
                            	"PAGE_NAV_MODE" => "N",
                            	"ELEMENT_PAGE" => "5",
                            	"PAGER_SHOW_ALL" => "N",
                            	"PAGER_SHOW_ALWAYS" => "N",
                            	"PAGER_TITLE" => "",
                            	"PAGER_TEMPLATE" => "",
                            	"RESIZE_MODE" => "F",
                            	"RESIZE_MODE_W" => "70",
                            	"RESIZE_MODE_H" => "70",
                            	"PAGE_LINK" => "",
                            	"PAGE_LINK_TEXT" => "",
                            	"LOAD_JS" => "Y"
                            	),
                            	false
                            );?>
                        </div></td>
					<td class="content">
                    <h1><?$APPLICATION->ShowTitle(false)?></h1>