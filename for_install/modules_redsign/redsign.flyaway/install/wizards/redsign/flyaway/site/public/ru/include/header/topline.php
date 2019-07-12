<div class="row">
  <div class="col-sm-2 col-xs-4 col-md-5 col-lg-5">
    <?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"top_line", 
	array(
		"ROOT_MENU_TYPE" => "top_menu",
		"CHILD_MENU_TYPE" => "",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"IBLOCK_ID" => "",
		"PRICE_CODE" => "",
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CONVERT_CURRENCY" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quan",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPONENT_TEMPLATE" => "top_line"
	),
	false
);?>
  </div>
  <div class="col-sm-10 col-xs-8 col-md-7 col-lg-7">
    <a href="#SITE_DIR#forms/recall/" class="headline-call JS-Popup-Ajax popup_link" title="Cвяжитесь с нами"><span>Свяжитесь с нами</span></a>
    <a class="hidden-xs hidden-sm hidden-md headline-download" href="#SITE_DIR#file.pdf">Скачать каталог в PDF</a>
    <?$APPLICATION->IncludeComponent(
      "redsign:autodetect.location",
      "inheader",
      array(
        "RSLOC_INCLUDE_JQUERY" => "N",
        "RSLOC_LOAD_LOCATIONS" => "Y",
        "COMPONENT_TEMPLATE" => "inheader",
        "RSLOC_LOAD_LOCATIONS_CNT" => "5"
      ),
      false
    );
    $APPLICATION->ShowViewContent("location");
    ?>
    <?$APPLICATION->IncludeComponent(
      "bitrix:system.auth.form",
      "inheader",
      array(
        "REGISTER_URL" => "#SITE_DIR#auth/",
        "PROFILE_URL" => "#SITE_DIR#personal/profile/"
      )
    );?>
  </div>
</div>