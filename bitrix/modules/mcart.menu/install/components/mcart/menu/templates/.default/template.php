<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
if (!jQuery){
<?$APPLICATION->AddHeadScript("https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");?>
}
</script>
<?if (empty($arResult))
	return;

$arHiddenItemsSelected = array();
$sumHiddenCounters = 0;
$arHiddenItemsCounters = array();
$arAllItemsCounters = array();
?>
<?/*echo "<pre>";print_r($arResult);echo "</pre>";//*/?>
<div id="MCArt_bot">
	<div class="MCArt_bot__flag"></div>
	<ul id="MCArt_bot__topLevelMenu">
<?foreach($arResult["TITLE_ITEMS"] as $title => $arTitleItem){
	if (is_array($arResult["SORT_ITEMS"][$title]["show"]) || is_array($arResult["SORT_ITEMS"][$title]["hide"])){
		$hideOption ="N";
		$SubItemSelected = true;
		$disabled=false;?>
			<li id="id_<?=$arTitleItem["PARAMS"]["menu_item_id"]?>">
				<a href="<?=($arTitleItem["LINK"]=="/index.php") ? "/" : $arTitleItem["LINK"]?>"><span><?echo $arTitleItem["TEXT"]?></span></a>
			<?if($arTitleItem["IS_PARENT"]!=""){?>
			<div class="MCArt_bot__subLevelMenuBox">
				<?
				$status="show";
				$count_circle=0;
				$cnt_men=0;
				foreach($arResult["SORT_ITEMS"][$title][$status] as $arItem){
					if($cnt_men<24){
						if($count_circle==0){?>				
							<ul class="MCArt_bot__subLevelMenu">
						<?}
						if ($arItem["PERMISSION"] > "D")
						{
							$couterId = "";
							$counter = 0;
							if (array_key_exists("counter_id", $arItem["PARAMS"]) && strlen($arItem["PARAMS"]["counter_id"]) > 0)
							{
								$couterId = $arItem["PARAMS"]["counter_id"] == "live-feed" ? "**" : $arItem["PARAMS"]["counter_id"];
								$counter = isset($GLOBALS["LEFT_MENU_COUNTERS"]) && array_key_exists($couterId, $GLOBALS["LEFT_MENU_COUNTERS"]) ? $GLOBALS["LEFT_MENU_COUNTERS"][$couterId] : 0;
								if ($couterId == "crm_cur_act")
								{
									$counterCrm = (isset($GLOBALS["LEFT_MENU_COUNTERS"]) && array_key_exists("CRM_**", $GLOBALS["LEFT_MENU_COUNTERS"]) ? intval($GLOBALS["LEFT_MENU_COUNTERS"]["CRM_**"]) : 0);
									$counterAct = $counter;
									$counter += $counterCrm;
								}
							}

							if ($couterId == "bp_tasks" && IsModuleInstalled("bitrix24"))
							{
								$showMenuItem = CUserOptions::GetOption("bitrix24", "show_bp_in_menu", false);
								if ($showMenuItem === false && $counter > 0)
								{
									CUserOptions::SetOption("bitrix24", "show_bp_in_menu", true);
									$showMenuItem = true;
								}

								if ($showMenuItem === false)
									continue;
							}

							if ($couterId)
							{
								$arAllItemsCounters[$couterId] = $counter;
							}
							?>
							<li id="<?if ($title!= "menu-favorites" && in_array($arItem["PARAMS"]["menu_item_id"],$arResult["ALL_FAVOURITE_ITEMS_ID"])) echo "hidden_"; echo $arItem["PARAMS"]["menu_item_id"]?>">
								<a href="<?=($arItem["LINK"]=="/index.php") ? "/" : $arItem["LINK"]?>">
									<?=$arItem["TEXT"]?>
								</a>
							</li>
							<?
						}
						$count_circle++;
						
							
						if($count_circle==6){?>
							</ul>					
							<?$count_circle=0;
						}
					
					$cnt_men++;
					}
				}?>	
			</div>
			<?}?>
			</li>
			<?if (IsSubItemSelected($arResult["SORT_ITEMS"][$title]["hide"])) $arHiddenItemsSelected[] = $arTitleItem["PARAMS"]["menu_item_id"];?>
	<?}
}?>
	</ul>
</div>