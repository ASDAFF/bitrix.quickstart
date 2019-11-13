<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<br/>
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
);?><br/>
    <?$APPLICATION->IncludeComponent("bitrix:menu", "template1");?>
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