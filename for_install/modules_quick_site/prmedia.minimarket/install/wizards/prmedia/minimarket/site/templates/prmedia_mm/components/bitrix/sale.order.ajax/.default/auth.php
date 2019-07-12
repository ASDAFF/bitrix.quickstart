<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="content-split">
	<div class="content-split-left">
		<button class="content-split-button" data-cls="content-split-left"><span><?php echo GetMessage('STOF_2REG') ?></span></button>
		<div class="content-split-content">
			<h3><?php echo GetMessage('STOF_2REG') ?></h3>
			<p><?php echo GetMessage('STOF_LOGIN_PROMT') ?></p>
			<?
			$APPLICATION->IncludeComponent(
				"bitrix:main.include", "", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR . "include_areas/auth_form.php",
				"EDIT_TEMPLATE" => ""
				), false
			);
			?>
		</div>
	</div>
	<div class="content-split-right content-split-active">
		<button class="content-split-button" data-cls="content-split-right"><span><?php echo GetMessage('STOF_2NEW') ?></span></button>
		<div class="content-split-content">
			<h3><?php echo GetMessage('STOF_2NEW') ?></h3>
			<?
			$APPLICATION->IncludeComponent(
				"bitrix:main.include", "", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR . "include_areas/registration_form.php",
				"EDIT_TEMPLATE" => ""
				), false
			);
			?>
		</div>
	</div>
</div>