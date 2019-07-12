<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>				
		</div>
	</div>
	<div class="foot">
		<div class="left">
			<?
							$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."/include_areas/foot_left.php",
								"EDIT_TEMPLATE" => "",
								),
								false,
								array(
								"HIDE_ICONS" => "N"
								)
							);
							?>
		</div>
		<div class="right">
				<?
							$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."/include_areas/foot_right.php",
								"EDIT_TEMPLATE" => "",
								),
								false,
								array(
								"HIDE_ICONS" => "N"
								)
							);
							?>
		</div>
	</div>
</div></div>
</body>
</html>