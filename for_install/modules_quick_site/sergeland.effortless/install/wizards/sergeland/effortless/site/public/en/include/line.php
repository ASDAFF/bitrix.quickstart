<div class="header-top-line">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-4 hidden-xs">
				<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => "#SITE_DIR#include/line-social.php",
					)
				);?>
			</div>
			<div class="col-md-6 col-sm-4 col-xs-12">
				<div class="text-center">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/line-email.php",
						)
					);?>
				</div>
			</div>
			<div class="col-md-3 col-sm-4 hidden-xs">
				<div class="text-right">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/line-skype.php",
						)
					);?>
				</div>
			</div>
		</div>
	</div>
</div>