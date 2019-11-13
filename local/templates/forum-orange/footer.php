<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<br/>
  </td>
</tr>
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