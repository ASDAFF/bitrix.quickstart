<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
  </td>
  <td width="20px"><br/>
    <!--[if IE 7]>
	<div style="float: left; margin-left: 20px;"><br/></div><![endif]-->
  </td>
  <td width="250px" style="width: 250px;">
    <?$APPLICATION->IncludeComponent("bitrix:search.title", "template1", array(
	"NUM_CATEGORIES" => "3",
	"TOP_COUNT" => "5",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "Y",
	"SHOW_OTHERS" => "Y",
	"PAGE" => SITE_DIR."search/index.php",
	"CATEGORY_0_TITLE" => "",
	"CATEGORY_0" => array(
	),
	"CATEGORY_1_TITLE" => "",
	"CATEGORY_1" => array(
	),
	"CATEGORY_2_TITLE" => "",
	"CATEGORY_2" => array(
	),
	"CATEGORY_OTHERS_TITLE" => "",
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "title-search-input",
	"CONTAINER_ID" => "title-search"
	),
	false
);?>
    <?$APPLICATION->IncludeComponent("bitrix:news.list", "template1", array(
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => "news",
	"NEWS_COUNT" => "2",
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
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
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
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<br/>
<?$APPLICATION->IncludeComponent("bitrix:main.include");?>
<br/><br/>
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "page",
	"AREA_FILE_SUFFIX" => "inc2",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
  </td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="965px" height="30px" style="border-top:solid 1px #ccc;"><tr>
<td bgcolor="#ffffff" align="left" style="padding-left:10px;">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => SITE_DIR."includes/bitrixcopy.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</td>
<td bgcolor="#ffffff" width="300px" align="right" style="padding-right:10px;">
<a style="text-decoration:none; font-size:10px; font-family:tahoma;" href="http://g-tech.su">
<?=GetMessage("GTECH_COPYRIGHT")?>
</a>
</td>
</tr></table>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<td width="11px"><img src="<?=SITE_TEMPLATE_PATH?>/images/lb_border.png"></td>
<td bgcolor="#ffffff" style="font-size:2px;">&nbsp;</td>
<td width="11px" height="11px"><img src="<?=SITE_TEMPLATE_PATH?>/images/rb_border.png"></td>
</tr></table>
  </div>
  <div class="bottom1">&nbsp;</div>
</div>
</body>
</html>