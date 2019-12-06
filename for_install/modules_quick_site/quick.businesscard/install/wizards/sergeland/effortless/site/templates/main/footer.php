<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?IncludeTemplateLangFile(__FILE__);?>
	<footer class="<?=(!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_FOOTER_BG", "dark", SITE_ID))?>">
		<div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<div class="footer-content">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "#SITE_DIR#include/contacts-footer.php", 
								)
							);?>
						</div>
					</div>
					<div class="col-md-4 hidden-xs hidden-sm">
						<div class="footer-content">
							<div class="row col-lg-offset-1">
								<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
										"AREA_FILE_SHOW" => "file",
										"PATH" => "#SITE_DIR#include/vk.php", 
									)
								);?>
							</div>
						</div>
					</div>
					<div class="col-md-4 hidden-xs hidden-sm">
						<div class="footer-content">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "#SITE_DIR#include/form-footer.php", 
								)
							);?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="subfooter">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "#SITE_DIR#include/copyright.php",
							)
						);?>
					</div>
					<div class="col-md-6 hidden-xs hidden-sm">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
							"ROOT_MENU_TYPE" => "bottom",
							"MENU_CACHE_TYPE" => "A",
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => "",
							"MAX_LEVEL" => "1",
							"CHILD_MENU_TYPE" => "",
							"USE_EXT" => "N",
							"DELAY" => "N",
							"ALLOW_MULTI_SELECT" => "N",
						),
						false
						);?>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>
<div class="scrollToTop"><i class="icon-up-open-big"></i></div>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/form-feedback-modal.php",
	)
);?>
<?if($USER->IsAdmin() || $demo=false):?>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/switcher.php",
		"DEMO" => $demo,
	)
);?>
<?endif?>
</body>
</html>