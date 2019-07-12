<?

	IncludeTemplateLangFile(__FILE__);

	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'misc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?$APPLICATION->ShowTitle()?></title>
		<?$APPLICATION->ShowHead();?>
		<link href="<?=SITE_TEMPLATE_PATH?>/css/nivo-slider.css" type="text/css" rel="stylesheet" />
		<link href="<?=SITE_TEMPLATE_PATH?>/js/fb/jquery.fancybox-1.3.4.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="/bitrix/js/socialservices/css/ss.css" />

		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui-1.9.2.custom.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jcarousel.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.nivo.slider.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/fb/jquery.fancybox-1.3.4.pack.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/prettyPhoto/jquery.prettyPhoto.js"></script>

		<script type="text/javascript">var lang_in_cart = '<?=GetMessage("IN_CART")?>'; var lang_cart_products = '<?=GetMessage("IN_CART_PRODUCTS")?>';</script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
	</head>




	<body>
		<div id="panel"><?$APPLICATION->ShowPanel();?></div>
		<div id="main">

				<?if($USER->IsAuthorized()){?>
					<div id="top-panel">
						<span id="lnk-cab"><i></i><a href="<?=SITE_DIR?>personal/"><?=GetMessage("LK")?></a></span>


						<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "basket_small", array(
								"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
								"PATH_TO_PERSONAL" => SITE_DIR."personal/",
								"SHOW_PERSONAL_LINK" => "Y"
								),
								false
						);?>

						<a href="<?=SITE_DIR?>personal/index.php?logout=yes" id="logout"></a>
						<input type="hidden" id="authorized" value="1" />
					</div>
				<?}else{?>
					<div id="top-panel" class="tp-unlogged">
						<input type="hidden" id="authorized" value="" />

						<span id="reg-lnk"><a href="<?=SITE_DIR?>register/"><?=GetMessage("REG")?></a></span>
						<span id="lnk-cab"><i></i><a href="<?=SITE_DIR?>personal/" id="loginFancy"><?=GetMessage("LOG")?></a></span>


						<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "basket_small", array(
								"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
								"PATH_TO_PERSONAL" => SITE_DIR."personal/",
								"SHOW_PERSONAL_LINK" => "Y"
								),
								false
						);?>




					</div>

				<?}?>


			<div id="header">
				<a href="<?=SITE_DIR?>" id="logo" class="logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/logo.php"), false);?></a>


				<form action="<?=SITE_DIR?>catalog/" method="get">
					<div id="search">
						<input name="q" id="s-inp" type="text" value="<?=GetMessage("SEARCH")?>" onfocus="if (this.value == '<?=GetMessage("SEARCH")?>') {this.value = ''; this.className = 'black'}" onblur="if (this.value == '') {this.value = '<?=GetMessage("SEARCH")?>'; this.className = ''}" />
						<input type="hidden" name="how" value="r" />
					</div>
				</form>

				<div id="phones">
					<i></i>
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/phone.php"), false);?>
				</div>

				<div id="mess">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/contacts_im.php"), false);?>
				</div>


                <?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
						"ROOT_MENU_TYPE" => "top",	// ��� ���� ��� ������� ������
						"MENU_CACHE_TYPE" => "Y",	// ��� �����������
						"MENU_CACHE_TIME" => "36000000",	// ����� ����������� (���.)
						"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
						"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
						"MAX_LEVEL" => "1",	// ������� ����������� ����
						"USE_EXT" => "Y",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
						"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
						),
						false
					);
				?>



				<div id="work-info">
					<div>
						<div>
							<p><i></i><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/work_info.php"), false);?></p>
						</div>
					</div>
				</div>

							<?$APPLICATION->IncludeComponent("bitrix:news.list", "index-banner", Array(
									"IBLOCK_TYPE" => "news",	// ��� ��������������� ����� (������������ ������ ��� ��������)
									"IBLOCK_ID" => "#TOPBANNERS_IBLOCK_ID#",	// ��� ��������������� �����
									"NEWS_COUNT" => "1",	// ���������� �������� �� ��������
									"SORT_BY1" => "RAND",	// ���� ��� ������ ���������� ��������
									"SORT_ORDER1" => "ASC",	// ����������� ��� ������ ���������� ��������
									"FILTER_NAME" => "",	// ������
									"FIELD_CODE" => array(	// ����
										0 => "",
										1 => "",
									),
									"PROPERTY_CODE" => array(	// ��������
										0 => "LINK",
										1 => "",
									),
									"DISPLAY_BLOCK_HTML_ID" => "top-banner",
									"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
									"DETAIL_URL" => "",	// URL �������� ���������� ��������� (�� ��������� - �� �������� ���������)
									"AJAX_MODE" => "N",	// �������� ����� AJAX
									"AJAX_OPTION_SHADOW" => "Y",
									"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
									"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
									"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
									"CACHE_TYPE" => "A",	// ��� �����������
									"CACHE_TIME" => "5",	// ����� ����������� (���.)
									"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
									"CACHE_GROUPS" => "Y",	// ��������� ����� �������
									"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
									"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
									"DISPLAY_PANEL" => "N",
									"SET_TITLE" => "N",	// ������������� ��������� ��������
									"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
									"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
									"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// �������� ������, ���� ��� ���������� ��������
									"PARENT_SECTION" => "",	// ID �������
									"PARENT_SECTION_CODE" => "",	// ��� �������
									"DISPLAY_NAME" => "Y",	// �������� �������� ��������
									"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
									"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
									"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
									"PAGER_TEMPLATE" => "",	// �������� �������
									"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",	// ����� ����������� ������� ��� �������� ���������
									"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
									"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
									),
									false
								);
								?>

			</div>

				<?$APPLICATION->IncludeComponent("bitrix:news.list", "index-banner", Array(
									"IBLOCK_TYPE" => "news",	// ��� ��������������� ����� (������������ ������ ��� ��������)
									"IBLOCK_ID" => "#BANNERS_IBLOCK_ID#",	// ��� ��������������� �����
									"NEWS_COUNT" => "1",	// ���������� �������� �� ��������
									"SORT_BY1" => "RAND",	// ���� ��� ������ ���������� ��������
									"SORT_ORDER1" => "ASC",	// ����������� ��� ������ ���������� ��������
									"FILTER_NAME" => "",	// ������
									"FIELD_CODE" => array(	// ����
										0 => "",
										1 => "",
									),
									"PROPERTY_CODE" => array(	// ��������
										0 => "LINK",
										1 => "",
									),
									"DISPLAY_BLOCK_HTML_ID" => "main-banner",
									"DISPLAY_BLOCK_HTML_BGR" => "#0f1722",
									"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
									"DETAIL_URL" => "",	// URL �������� ���������� ��������� (�� ��������� - �� �������� ���������)
									"AJAX_MODE" => "N",	// �������� ����� AJAX
									"AJAX_OPTION_SHADOW" => "Y",
									"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
									"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
									"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
									"CACHE_TYPE" => "A",	// ��� �����������
									"CACHE_TIME" => "5",	// ����� ����������� (���.)
									"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
									"CACHE_GROUPS" => "Y",	// ��������� ����� �������
									"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
									"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
									"DISPLAY_PANEL" => "N",
									"SET_TITLE" => "N",	// ������������� ��������� ��������
									"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
									"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
									"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// �������� ������, ���� ��� ���������� ��������
									"PARENT_SECTION" => "",	// ID �������
									"PARENT_SECTION_CODE" => "",	// ��� �������
									"DISPLAY_NAME" => "Y",	// �������� �������� ��������
									"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
									"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
									"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
									"PAGER_TEMPLATE" => "",	// �������� �������
									"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",	// ����� ����������� ������� ��� �������� ���������
									"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
									"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
									),
									false
								);
								?>



			<div id="content">

				<div id="left-col">


					<?$APPLICATION->IncludeComponent("bitrix:menu", "left", Array(
						"ROOT_MENU_TYPE" => "left",	// ��� ���� ��� ������� ������
						"MENU_CACHE_TYPE" => "Y",	// ��� �����������
						"MENU_CACHE_TIME" => "36000000",	// ����� ����������� (���.)
						"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
						"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
						"MAX_LEVEL" => "1",	// ������� ����������� ����
						"USE_EXT" => "Y",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
						"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
						),
						false
					);
					?>







								<?
									$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "vendors", array(
										"IBLOCK_TYPE_ID" => "catalog",
										"IBLOCK_ID" => "#BRAND_IBLOCK_ID#",
										"ELEMENT_SORT_FIELD" => "NAME",
										"ELEMENT_SORT_ORDER" => "asc",
										"ELEMENT_COUNT" => "100",
										"FLAG_PROPERTY_CODE" => "",
										"OFFERS_LIMIT" => "5",
										"ACTION_VARIABLE" => "action",
										"PRODUCT_ID_VARIABLE" => "id",
										"PRODUCT_QUANTITY_VARIABLE" => "quantity",
										"PRODUCT_PROPS_VARIABLE" => "prop",
										"SECTION_ID_VARIABLE" => "SECTION_ID",
										"CACHE_TYPE" => "A",
										"CACHE_TIME" => "180",
										"CACHE_GROUPS" => "N",
										"DISPLAY_COMPARE" => "N",
										"PRICE_CODE" => array(
										),
										"USE_PRICE_COUNT" => "N",
										"SHOW_PRICE_COUNT" => "1",
										"PRICE_VAT_INCLUDE" => "Y",
										"PRODUCT_PROPERTIES" => array(
											0 => "MANUFACTURER"
										),
										),
										false
									);
									?>








				</div>

				<div id="cols-wrapper">





<? if(strpos($APPLICATION->GetCurDir(), "about")): ?>

		<div id="right-wide-col">
			<div id="breadcrumbs">
					<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(), false); ?>
					&rarr; <strong><?$APPLICATION->ShowTitle();?></strong>
			</div>

			<div class="text">
				<h1><?$APPLICATION->ShowTitle();?></h1>



<? elseif(!strpos($APPLICATION->GetCurDir(), "catalog")): ?>

					<div id="right-col">
                        <?$APPLICATION->IncludeComponent("bitrix:catalog.smart.filter", "index", array(
									"IBLOCK_TYPE" => "catalog",
									"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
									"SECTION_ID" => "",
									"FILTER_NAME" => "arrFilter",
									"CACHE_TYPE" => "A",
									"CACHE_TIME" => "36000000",
									"CACHE_GROUPS" => "N",
									"SAVE_IN_SESSION" => "N",
									"PRICE_CODE" => array(
										0 => "BASE",
									)
									),
									false
								);?>







					</div>

					<div id="center-col">

						<? if( INDEX_PAGE != "Y" ): ?>
							<div id="breadcrumbs">
								<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(), false); ?>
								&rarr; <strong><?$APPLICATION->ShowTitle();?></strong>
							</div>
						<? endif; /* if( INDEX_PAGE != "Y" ): */ ?>


                        <? if( INDEX_PAGE == "Y" ): ?>
                            <? $APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "index-slider", array(
								"IBLOCK_TYPE_ID" => "catalog",
								"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "asc",
								"ELEMENT_COUNT" => "9",
								"FLAG_PROPERTY_CODE" => "NEWPRODUCT",
								"OFFERS_LIMIT" => "14",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "180",
								"CACHE_GROUPS" => "N",
								"DISPLAY_COMPARE" => "N",
								"PRICE_CODE" => array(
									0 => "BASE",
								),
								"USE_PRICE_COUNT" => "N",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"PRODUCT_PROPERTIES" => array(
									0 => "MANUFACTURER",
									1 => "ARTNUMBER",

								),
								"DISPLAY_IMG_WIDTH" => "140",
								"DISPLAY_IMG_HEIGHT" => "210",
								"DISPLAY_BLOCK_TITLE" => GetMessage("NEW"),
								"DISPLAY_BLOCK_ICO" => "ico_news.png",
								"SHARPEN" => "30"
								),
								false
							);
							?>

							<? $APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "index-slider", array(
								"IBLOCK_TYPE_ID" => "catalog",
								"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "asc",
								"ELEMENT_COUNT" => "8",
								"FLAG_PROPERTY_CODE" => "SALELEADER",
								"OFFERS_LIMIT" => "14",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "180",
								"CACHE_GROUPS" => "N",
								"DISPLAY_COMPARE" => "N",
								"PRICE_CODE" => array(
									0 => "BASE",
								),
								"USE_PRICE_COUNT" => "N",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"PRODUCT_PROPERTIES" => array(
									0 => 'ARTNUMBER'
								),
								"DISPLAY_IMG_WIDTH" => "140",
								"DISPLAY_IMG_HEIGHT" => "210",
								"DISPLAY_BLOCK_TITLE" => GetMessage("LEADERS"),
								"DISPLAY_BLOCK_ICO" => "ico_hits.png",
								"SHARPEN" => "30"
								),
								false
							);
							?>


							<? $APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "index-slider", array(
								"IBLOCK_TYPE_ID" => "catalog",
								"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "asc",
								"ELEMENT_COUNT" => "8",
								"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
								"OFFERS_LIMIT" => "14",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "180",
								"CACHE_GROUPS" => "N",
								"DISPLAY_COMPARE" => "N",
								"PRICE_CODE" => array(
									0 => "BASE",
								),
								"USE_PRICE_COUNT" => "N",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"PRODUCT_PROPERTIES" => array(
									0 => 'ARTNUMBER'
								),
								"DISPLAY_IMG_WIDTH" => "140",
								"DISPLAY_IMG_HEIGHT" => "210",
								"DISPLAY_BLOCK_TITLE" => GetMessage("SPECIALOFFER"),
								"DISPLAY_BLOCK_ICO" => "ico_spec.png",
								"SHARPEN" => "30"
								),
								false
							);
							?>



							<div class="center-banner">

								<?$APPLICATION->IncludeComponent("bitrix:news.list", "index-slider", Array(
									"IBLOCK_TYPE" => "news",	// ��� ��������������� ����� (������������ ������ ��� ��������)
									"IBLOCK_ID" => "#SLIDER_IBLOCK_ID#",	// ��� ��������������� �����
									"NEWS_COUNT" => "6",	// ���������� �������� �� ��������
									"SORT_BY1" => "ACTIVE_FROM",	// ���� ��� ������ ���������� ��������
									"SORT_ORDER1" => "DESC",	// ����������� ��� ������ ���������� ��������
									"SORT_BY2" => "SORT",	// ���� ��� ������ ���������� ��������
									"SORT_ORDER2" => "ASC",	// ����������� ��� ������ ���������� ��������
									"FILTER_NAME" => "",	// ������
									"FIELD_CODE" => array(	// ����
										0 => "",
										1 => "",
									),
									"PROPERTY_CODE" => array(	// ��������
										0 => "LINK",
										1 => "",
									),
									"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
									"DETAIL_URL" => "",	// URL �������� ���������� ��������� (�� ��������� - �� �������� ���������)
									"AJAX_MODE" => "N",	// �������� ����� AJAX
									"AJAX_OPTION_SHADOW" => "Y",
									"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
									"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
									"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
									"CACHE_TYPE" => "A",	// ��� �����������
									"CACHE_TIME" => "36000000",	// ����� ����������� (���.)
									"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
									"CACHE_GROUPS" => "Y",	// ��������� ����� �������
									"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
									"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
									"DISPLAY_PANEL" => "N",
									"SET_TITLE" => "N",	// ������������� ��������� ��������
									"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
									"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
									"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// �������� ������, ���� ��� ���������� ��������
									"PARENT_SECTION" => "",	// ID �������
									"PARENT_SECTION_CODE" => "",	// ��� �������
									"DISPLAY_NAME" => "Y",	// �������� �������� ��������
									"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
									"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
									"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
									"PAGER_TEMPLATE" => "",	// �������� �������
									"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",	// ����� ����������� ������� ��� �������� ���������
									"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
									"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
									),
									false
								);
								?>

							</div>





										<?
										$APPLICATION->IncludeComponent("bitrix:news.list", "index-bottom", Array(
											"IBLOCK_TYPE" => "news",	// ��� ��������������� ����� (������������ ������ ��� ��������)
											"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",	// ��� ��������������� �����
											"NEWS_COUNT" => "3",	// ���������� �������� �� ��������
											"SORT_BY1" => "ACTIVE_FROM",	// ���� ��� ������ ���������� ��������
											"SORT_ORDER1" => "DESC",	// ����������� ��� ������ ���������� ��������
											"SORT_BY2" => "SORT",	// ���� ��� ������ ���������� ��������
											"SORT_ORDER2" => "ASC",	// ����������� ��� ������ ���������� ��������
											"FILTER_NAME" => "",	// ������
											"FIELD_CODE" => array(),
											"PROPERTY_CODE" => array(),
											"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
											"DETAIL_URL" => "",	// URL �������� ���������� ��������� (�� ��������� - �� �������� ���������)
											"AJAX_MODE" => "N",	// �������� ����� AJAX
											"AJAX_OPTION_SHADOW" => "Y",
											"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
											"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
											"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
											"CACHE_TYPE" => "A",	// ��� �����������
											"CACHE_TIME" => "36000000",	// ����� ����������� (���.)
											"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
											"CACHE_GROUPS" => "Y",	// ��������� ����� �������
											"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
											"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
											"DISPLAY_PANEL" => "N",
											"SET_TITLE" => "N",	// ������������� ��������� ��������
											"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
											"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
											"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
											"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// �������� ������, ���� ��� ���������� ��������
											"PARENT_SECTION" => "",	// ID �������
											"PARENT_SECTION_CODE" => "",	// ��� �������
											"DISPLAY_NAME" => "Y",	// �������� �������� ��������
											"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
											"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
											"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
											"PAGER_TEMPLATE" => "",	// �������� �������
											"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
											"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",	// ����� ����������� ������� ��� �������� ���������
											"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
											"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
											),
											false
										);
										?>



							<div class="color-sep">
								<img src="<?=SITE_TEMPLATE_PATH?>/images/grad_line.png" alt="" />
								<div></div>
							</div>



                        <? endif; /* if( INDEX_PAGE == "Y" ): */ ?>

<? else: /* if(!strpos($APPLICATION->GetCurDir(), "catalog")) - �������� �������� */ ?>






<? endif; ?>





