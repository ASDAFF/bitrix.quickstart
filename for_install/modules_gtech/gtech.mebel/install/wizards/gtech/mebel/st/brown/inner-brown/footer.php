<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
		</td>
		<td width="50px" valign="top" align="center">
			<img src="<?=SITE_TEMPLATE_PATH?>/images/vdelimiter.jpg" border="0">
		</td>
		<td valign="top" align="left" width="250px">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "rightmenu", Array(
	"ROOT_MENU_TYPE" => "left",	// ��� ���� ��� ������� ������
	"MENU_CACHE_TYPE" => "A",	// ��� �����������
	"MENU_CACHE_TIME" => "3600",	// ����� ����������� (���.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
	"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
	"MAX_LEVEL" => "1",	// ������� ����������� ����
	"CHILD_MENU_TYPE" => "left",	// ��� ���� ��� ��������� �������
	"USE_EXT" => "N",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
	"DELAY" => "N",	// ����������� ���������� ������� ����
	"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
	),
	false
);?>
		</td>
	</tr></table>
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
				<a style="text-decoration:none; font-size:14px; color:#fff;" href="http://g-tech.su">����������� �G-tech�</a>
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