<div class="section <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["PRODUCTS_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["PRODUCTS_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_PRODUCTS_BG", "white-bg", SITE_ID))?> clearfix">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 class="text-center">Catalog</h1>
				<div class="separator"></div>
				<p class="lead text-center mb-40">Lorem ipsum dolor sit amet laudantium molestias similique.<br> Quisquam incidunt ut laboriosam.</p>
				<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog", Array(
						"VIEW_MODE" => "TEXT",
						"SHOW_PARENT_NAME" => "Y",
						"IBLOCK_TYPE" => "#IBLOCK_TYPE_CATALOG#",
						"IBLOCK_ID" => "#IBLOCK_ID_CATALOG#",
						"SECTION_ID" => $_REQUEST["SECTION_ID"],
						"SECTION_CODE" => "",
						"SECTION_URL" => "#SITE_DIR#catalog/#SECTION_CODE#/",
						"COUNT_ELEMENTS" => "N",
						"TOP_DEPTH" => "1",
						"SECTION_FIELDS" => array(),
						"SECTION_USER_FIELDS" => array(),
						"ADD_SECTIONS_CHAIN" => "N",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"CACHE_NOTES" => "",
						"CACHE_GROUPS" => "Y",
						"HIDE_SECTION_NAME" => "N"
					),
					false
				);?>
			</div>
		</div>
	</div>
</div>