<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<footer id="footer">
			<div class="fcontent">
				<?$APPLICATION->IncludeComponent(
					"bitrix:menu", "footer", Array(
					"ROOT_MENU_TYPE" => "top",
					"MAX_LEVEL" => "1",
					"CHILD_MENU_TYPE" => "top",
					"USE_EXT" => "Y",
					"DELAY" => "Y",
					"ALLOW_MULTI_SELECT" => "N",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_USE_GROUPS" => "N",
					"MENU_CACHE_GET_VARS" => array()
					)
				);?>
				<div class="contacts">
					<div class="copy">&copy;&nbsp;<a href="<?php echo SITE_DIR ?>"><?
$APPLICATION->IncludeComponent(
  "bitrix:main.include", "",
  array(
  "AREA_FILE_SHOW" => "file",
  "PATH" => SITE_DIR . "include_areas/sitename_footer.php"
  ), false);
?></a>&nbsp;&mdash;&nbsp;<?
$APPLICATION->IncludeComponent(
  "bitrix:main.include", "",
  array(
  "AREA_FILE_SHOW" => "file",
  "PATH" => SITE_DIR . "include_areas/slogan_footer.php"
  ), false);
?></div>
					<p><?
$APPLICATION->IncludeComponent(
  "bitrix:main.include", "",
  array(
  "AREA_FILE_SHOW" => "file",
  "PATH" => SITE_DIR . "include_areas/phonelabel_footer.php"
  ), false);
?> <a href="callto:<?
$APPLICATION->IncludeComponent(
  "bitrix:main.include", "",
  array(
  "AREA_FILE_SHOW" => "file",
  "PATH" => SITE_DIR . "include_areas/phone_footer.php"
  ), false);
?>"><?
$APPLICATION->IncludeComponent(
  "bitrix:main.include", "",
  array(
  "AREA_FILE_SHOW" => "file",
  "PATH" => SITE_DIR . "include_areas/phone_footer.php"
  ), false);
?></a></p>
				</div>
			</div>
		</footer>
		<div id="input_prototype">
			<span class="input-radio"><span class="input-radio-status"></span></span>
			<span class="input-checkbox"></span>
		</div>
	</div>
</body>
</html>