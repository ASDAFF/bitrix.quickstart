<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<div class="r_side">
	<div class="header">
    	<img src="<?=SITE_TEMPLATE_PATH?>/images/ic_new.png"> <a href="/catalog/#SECTION_NEWPROD_ID#/"><b>НОВИНКИ</b></a> &nbsp; 
    	<img src="<?=SITE_TEMPLATE_PATH?>/images/ic_lider.png"> <a href="/catalog/#SECTION_HIT_ID#/"><b>ЛИДЕРЫ</b></a>
    </div>      
	<?$APPLICATION->IncludeComponent("bitrix:menu", "vertical", array(
		"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "4",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"ALLOW_MULTI_SELECT" => "N"
		),
		false
	);?>
</div> 