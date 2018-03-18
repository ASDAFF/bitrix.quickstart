<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die() ?>
									</div>
									<?$APPLICATION->IncludeComponent(
										"bitrix:main.include",
										"",
										Array(
											"AREA_FILE_SHOW" => "page",
											"AREA_FILE_SUFFIX" => "foot",
											"AREA_FILE_RECURSIVE" => "Y",
											"EDIT_TEMPLATE" => ""
										),
									false
									);?>
								</div>
								<div class="ca"></div>
							</div>
						</div>						
						<div id="main-round-bottom" class="round">
							<div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div>
						</div>
					</div>
				</div>
			</div>
			<div id="foot">
				<div id="footwidther">
					<?$APPLICATION->IncludeFile(
						SITE_TEMPLATE_PATH . "/include_areas/copyright.php",
						Array(),
						Array("MODE"=>"html")
					);?>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
						"ROOT_MENU_TYPE"	=>	"top",
						"MAX_LEVEL"	=>	"1",
						"MENU_CACHE_TYPE" => "A",
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_USE_GROUPS" => "N",
						"MENU_CACHE_GET_VARS" => Array()
						)
					);?>
					<?$APPLICATION->IncludeFile(
						SITE_TEMPLATE_PATH . "/include_areas/developer.php",
						Array(),
						Array("MODE"=>"text")
					);?>
				</div>
			</div>
		</div>
	</body>
</html>