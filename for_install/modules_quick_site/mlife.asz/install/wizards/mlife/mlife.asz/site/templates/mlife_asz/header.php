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
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.popup.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.easing.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/templates.js');
	$APPLICATION->ShowHead();
	?>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
		<div id="site"><div class="wrap">
			<div class="mlfTopPanel">
				<?
					$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"topshap", 
	array(
		"ROOT_MENU_TYPE" => "topmenu",
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
			<div class="mlfShap">
				<div class="korzWrap">
					<div class="mlfKorz">
						<?$APPLICATION->IncludeComponent(
								"mlife:asz.basket.small",
								"",
								Array(
								),
							false
							);?>
					</div>
				</div>
				<div class="leftShapWrap">
					<div class="mlfLogo">
						<?
					$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."/include_areas/logo.php",
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
			<div class="mlfNavMenu">
				<?
					$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"horizontal", 
	array(
		"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "lefto",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
	),
	false
);
					?>
			</div>
			
			<div class="contus">
				<div class="rightBlock">
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