<?php 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetTitle("Каталог");
?>
<div class="not-round link-mas">
<h3>Сожалеем, но ничего не найдено.</h3>
<?$APPLICATION->IncludeComponent("bitrix:menu", "notfound_catalog", array(
							"ROOT_MENU_TYPE" => "notfound",
							"MAX_LEVEL" => "1",
							"MENU_CACHE_TYPE" => "Y",
							"MENU_CACHE_TIME" => "36000000",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => array(
							),
						),
						false
					);?>
</div>