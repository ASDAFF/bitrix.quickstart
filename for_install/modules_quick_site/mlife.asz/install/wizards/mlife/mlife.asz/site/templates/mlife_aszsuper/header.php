<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?
	IncludeTemplateLangFile(__FILE__);
	global $APPLICATION;
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.1.10.2.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.cookie.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/mlfslide.jquery.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.easing.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.flexmenu.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/templates.js');
	$APPLICATION->ShowHead();
	?>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
		<div id="site">
		<div class="mainTopper"><div class="wrap980">
			<div class="mlfKorz">
				<?$APPLICATION->IncludeComponent("mlife:asz.basket.small", "super", Array(
	
	),
	false
);?>
			</div>
			<div class="mlfNavMenu">
				<?
					$APPLICATION->IncludeComponent("bitrix:menu", "super", Array(
	"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => "",
		"MAX_LEVEL" => "3",	
		"CHILD_MENU_TYPE" => "lefto",
		"USE_EXT" => "Y",	
		"DELAY" => "N",	
	),
	false
);
					?>
			</div>
		</div></div>
		
		<div class="wrap">
			<div class="mlfShap">
				<div class="leftShapWrap">
					<div class="mlfLogo">
						<?
					$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."/include_areas/logo_super.php",
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
				<div class="rightShapWrap">
					<div class="blockPhEm">
						<div class="phone"><?
					$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."/include_areas/phone.php",
						"EDIT_TEMPLATE" => "",
						),
						false,
						array(
						"HIDE_ICONS" => "N"
						)
					);
					?></div>
						<div class="email"><?
					$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."/include_areas/email.php",
						"EDIT_TEMPLATE" => "",
						),
						false,
						array(
						"HIDE_ICONS" => "N"
						)
					);
					?></div>
					</div>
					<div class="blockRight">
					<div class="zakazMenu">
						<?
					$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"zakaz", 
	array(
		"ROOT_MENU_TYPE" => "zakaz",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
	),
	false
);
					?>
					</div>
					<div class="searchBlock">
					<?
							$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."/include_areas/search_block.php",
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
				</div>
			</div>
			<div class="bottomShap">
				<?
					$APPLICATION->IncludeComponent("bitrix:menu", "bottomshap", Array(
	"ROOT_MENU_TYPE" => "bottomshap",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => "",
		"MAX_LEVEL" => "4",
		"CHILD_MENU_TYPE" => "lefto",
		"USE_EXT" => "Y",
		"DELAY" => "N",
	),
	false
);
					?>
			</div>
			
			<div class="contus">
				<div class="rightBlock">
					
					<?$APPLICATION->ShowViewContent('filter');?>
					<div class="contact"><?
							$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."/include_areas/right_contact.php",
								"EDIT_TEMPLATE" => "",
								),
								false,
								array(
								"HIDE_ICONS" => "N"
								)
							);
							?>
					</div>
					<?if($vk){?>
					<div class="vkWrap">
					<?
							$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."/include_areas/vk.php",
								"EDIT_TEMPLATE" => "",
								),
								false,
								array(
								"HIDE_ICONS" => "N"
								)
							);
							?>
					
					</div>
					<?}?>
				</div>
				<div class="contwp">
				<div class="operafix">.... ...... ........ ....... ........ ......... .....
.... ...... ........ ....... ........ ......... .....
.... ...... ........ ....... ........ ......... .....
</div>
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "kroshke", array(
	"START_FROM" => "0",
	"PATH" => "",
	"SITE_ID" => "s3"
	),
	false
);?>