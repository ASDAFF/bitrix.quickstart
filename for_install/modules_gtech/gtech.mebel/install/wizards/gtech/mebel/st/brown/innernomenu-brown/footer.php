<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>

</div>

<div class="shadow-top"></div>

<div class="footer"><div class="footer-inner">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td rowspan="2" valign="top">
				<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/footer-address.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
			</td>
			<td valign="middle" width="200px" height="40px">
				<a style="text-decoration:none; font-size:14px; color:#fff;" href="http://g-tech.su">Разработано «G-tech»</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/footer-social.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
			</td>
		</tr>
	</table>
</div></div>

</body>
</html>