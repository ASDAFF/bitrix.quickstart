<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	
	<div class="mlfQuest"><div class="wrap980">
		<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/vopros.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
	</div></div>
	
	<div class="mlfFoot"><div class="wrap980">
		<div class="phoneBottom">
			<div class="phone"> <?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/phone.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?></div>
			<div class="zvonok"><?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/zvonok.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?></div>
		</div>
		<div class="developer">
			<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/mlife.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
		</div>
	</div></div>
	
	</div></div>
</div>
</body>
</html>