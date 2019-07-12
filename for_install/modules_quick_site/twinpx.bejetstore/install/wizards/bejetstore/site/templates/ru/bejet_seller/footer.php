<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<hr class="i-size-L">
</div>
<div id="b-float-phone">
	<div class="b-float-phone__bg"></div>
	<span class="b-float-phone__content">
		<span class="glyphicon glyphicon-earphone"></span>
		<span class="b-float-phone__text"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></span>
	</span>
</div>
<footer class="bj-page-footer">
	<div class="container-fluid">
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?>
		<hr>
		<div class="row">
<?
			$APPLICATION->IncludeComponent("bitrix:menu", "bottom_menu", array(
				"ROOT_MENU_TYPE" => "bottom",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "bottom",
				"USE_EXT" => "N",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>
<?
			$APPLICATION->IncludeComponent("bitrix:menu", "bottom_menu", array(
				"ROOT_MENU_TYPE" => "bottom_second",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "bottom_second",
				"USE_EXT" => "N",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>
<?
			$APPLICATION->IncludeComponent(
				"bitrix:menu", 
				"bottom_menu", 
				array(
					"ROOT_MENU_TYPE" => "bottom_third",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "1",
					"CHILD_MENU_TYPE" => "bottom_third",
					"USE_EXT" => "N",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>
<?
			$APPLICATION->IncludeComponent("bitrix:menu", "payment_menu", array(
				"ROOT_MENU_TYPE" => "payment",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "payment",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?><hr class="visible-xs-block">
<?
			$APPLICATION->IncludeComponent("bitrix:menu", "bottom_social", array(
				"ROOT_MENU_TYPE" => "bottom_social",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "bottom_social",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>
		</div>
	</div>
</footer>
<div class="container-fluid bj-footer-bitrix-btn">
	<div class="bj-footer-bitrix-btn__inner"><div id="bx-composite-banner" style="position: relative;"></div></div>
</div>
</body>
</html>