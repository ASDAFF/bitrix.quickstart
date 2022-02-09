<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="sonet_log_filter_container" style="padding-bottom: 10px;">
	<div id="bx_sl_filter_hidden" class="sonet-log-filter" style="display: block;">
		<div class="sonet-log-filter-lt">
			<div class="sonet-log-filter-rt"></div>
		</div>
		<div id="bx_sl_filter_content">
			<?if ($arParams["SUBSCRIBE_ONLY"] == "Y"):?>
				<div class="sonet-log-settings"><a href="<?=$arResult["PATH_TO_SUBSCRIBE"]?>" class="sonet-log-title-button"><i class="sonet-log-settings-icon"></i><span class="sonet-log-settings-text"><?=GetMessage("SONET_C30_SETTINGS")?></span></a></div>
			<?endif;?>
			<div class="sonet-log-favorites"><a target="_self" href="<?=$GLOBALS["APPLICATION"]->GetCurPageParam("preset_filter_id=favorites", array("preset_filter_id"))?>" class="sonet-log-title-button<?=($arResult["PresetFilterActive"] == "favorites" ? " sonet-log-title-button-active" : "")?>"><?=GetMessage("SONET_C30_T_FAVORITES")?></a></div><?
			if ($arResult["PresetFilterActive"] != "favorites")
			{
				?><span class="sonet-log-filter-lamp <?=($arResult["IS_FILTERED"] ? "sonet-log-filter-lamp-a" : "sonet-log-filter-lamp-na") ?>"></span>
				<span class="sonet-log-filter-title"><?=GetMessage("SONET_C30_T_FILTER_TITLE");?></span><?if ($arParams["SUBSCRIBE_ONLY"] == "N" && !$arParams["SHOW_HIDDEN"]):?><span class="sonet-log-filter-title sonet-log-filter-title2"><?
					if (intval($arParams["CREATED_BY_ID"]) > 0)
					{
						$rsUser = CUser::GetByID($arParams["CREATED_BY_ID"]);
						if ($arUser = $rsUser->Fetch())
							echo GetMessage("SONET_C30_T_FILTER_CREATED_BY").": ".CUser::FormatName($arParams['NAME_TEMPLATE'], $arUser, ($arParams["SHOW_LOGIN"] != "N"));
					}
					elseif (intval($arParams["GROUP_ID"]) > 0)
						echo GetMessage("SONET_C30_T_FILTER_GROUP").": ".$arResult["Group"]["NAME"];
				?></span><?endif;?>
				<a id="sonet_log_filter_show" href="javascript:void(0)" onclick="__logFilterShow(); return false;"><?=GetMessage("SONET_C30_T_FILTER_SHOW");?></a><?
			}
			else
			{
				?><span class="sonet-log-filter-title"><a target="_self" class="sonet-log-title-button" href="<?=$GLOBALS["APPLICATION"]->GetCurPageParam("", array("preset_filter_id"))?>"><?=GetMessage("SONET_C30_T_BACK_TO_LOG");?></a></span><?
			}
		?></div>
		<div class="sonet-log-filter-lb">
			<div class="sonet-log-filter-rb"></div>
		</div>
	</div><?
	if ($arResult["PresetFilterActive"] != "favorites")
	{
		?>
		<div id="bx_sl_filter" class="sonet-log-filter" style="display: none;">
			<div class="sonet-log-filter-lt">
				<div class="sonet-log-filter-rt"></div>
			</div>
			<div id="bx_sl_filter_content">
				<?if ($arParams["SUBSCRIBE_ONLY"] == "Y"):?>
					<div class="sonet-log-settings"><a href="<?=$arResult["PATH_TO_SUBSCRIBE"]?>" class="sonet-log-title-button"><i class="sonet-log-settings-icon"></i><span class="sonet-log-settings-text"><?=GetMessage("SONET_C30_SETTINGS")?></span></a></div>
				<?endif;?>
				<span class="sonet-log-filter-lamp <?=($arResult["IS_FILTERED"] ? "sonet-log-filter-lamp-a" : "sonet-log-filter-lamp-na") ?>"></span>
				<span class="sonet-log-filter-title"><?=GetMessage("SONET_C30_T_FILTER_TITLE");?></span>
				<a id="sonet_log_filter_hide" href="javascript:void(0)" onclick="__logFilterShow(); return false;"><?=GetMessage("SONET_C30_T_FILTER_HIDE");?></a>
			<div class="sonet-log-filter-line"></div>
			<form method="GET" name="log_filter">
			<script type="text/javascript">
				var arFltFeaturesID = new Array();
			</script>
			<div class="log-filter-title"><?=GetMessage("SONET_C30_T_FILTER_FEATURES_TITLE")?></div>
			<?
			$bCheckedAll = true;

			foreach ($arResult["ActiveFeatures"] as $featureID => $featureName):

				if (!$featureName)
						$featureName = GetMessage(toUpper("SONET_C30_T_FEATURE_".$arParams["ENTITY_TYPE"]."_".$featureID));

					if (array_key_exists("flt_event_id", $_REQUEST) && in_array($featureID, $_REQUEST["flt_event_id"]) || empty($arParams["EVENT_ID"]) || in_array("all", $arParams["EVENT_ID"]))
						$bChecked = true;
					else
					{
						$bChecked = false;
						$bCheckedAll = false;
					}
					?><span class="sonet-log-filter-feature"><script type="text/javascript">arFltFeaturesID.push('<?=$featureID?>');</script><span class="sonet-log-filter-checkbox"><input type="checkbox" id="flt_event_id_<?=$featureID?>" name="flt_event_id[]" value="<?=$featureID?>" <?=($bChecked ? "checked" : "")?> onclick="__logFilterClick('<?=$featureID?>')"></span><label for="flt_event_id_<?=$featureID?>" class="sonet-log-filter-label"><?=$featureName?></label></span><?

			endforeach;
			?>
			<div class="sonet-log-filter-line"></div>
			<table cellspacing="0" border="0">
			<tr>
				<td valign="top">
				<div style="width: 200px;">
					<div class="sonet-log-filter-createdby-title"><?=GetMessage("SONET_C30_T_FILTER_CREATED_BY");?>:</div>
					<?
					if (IsModuleInstalled("intranet")):
						?><?
						$GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
							'INPUT_NAME' => "flt_created_by_id",
							'INPUT_NAME_STRING' => "flt_created_by_string",
							'INPUT_NAME_SUSPICIOUS' => "flt_created_by_suspicious",
							'INPUT_VALUE_STRING' => htmlspecialcharsback($_REQUEST["flt_created_by_string"]),
							'EXTERNAL' => 'A',
							'MULTIPLE' => 'N',
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						?><?
					else:
						?><?
						$APPLICATION->IncludeComponent("bitrix:socialnetwork.user_search_input", ".default", array(
								"NAME" => "flt_created_by_id",
								"VALUE" => $_REQUEST["flt_created_by_id"],
								"TEXT" => 'size="20"',
								"EXTRANET" => "I",
								"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
								"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
								"FUNCTON" => "__logFilterCreatedByChange"
							)
						);
						?><script type="text/javascript">
							BX.bind(document.forms["log_filter"]["flt_created_by_id"], "change", __logFilterCreatedByChange);
							BX.bind(document.forms["log_filter"]["flt_created_by_id"], "keypress", __logFilterCreatedByChange);
						</script><?
					endif;
					?>
				</div>
				</td>
				<td valign="top">
					<div style="width: 200px;">
						<div class="sonet-log-filter-group-title"><?=GetMessage("SONET_C30_T_FILTER_GROUP");?>:</div>
						<span id="sonet-log-filter-group" class="<?=(!$arResult["Group"]["ID"]?" webform-field-textbox-empty":"")?>">
							<input type="text" id="filter-field-group" value="<?=$arResult["Group"]["NAME"]?>" />
							<a class="sonet-log-field-textbox-clear" href=""></a>
						</span>							
					</div>
					<input type="hidden" name="flt_group_id" value="<?=$arResult["Group"]["ID"]?>" id="filter_field_group_hidden">
					<? $APPLICATION->IncludeComponent(
							"bitrix:socialnetwork.group.selector", 
							".default", 
							array(
								"BIND_ELEMENT" => "sonet-log-filter-group",
								"JS_OBJECT_NAME" => "filterGroupsPopup",
								"ON_SELECT" => "onFilterGroupSelect",
								"SEARCH_INPUT" => "filter-field-group",
								"SELECTED" => $arResult["Group"]["ID"] ? $arResult["Group"]["ID"] : 0
							), 
							null, 
							array("HIDE_ICONS" => "Y")
						);
					?><script type="text/javascript">
						BX.ready(function(){					
							BX.bind(BX("filter-field-group"), "click", function(e) {
								if(!e) e = window.event;
								filterGroupsPopup.show();
								BX.PreventDefault(e);
							});
						
							BX.bind(BX.findNextSibling(BX("filter-field-group"), {tagName : "a"}), "click", function(e){
								if(!e) e = window.event;

								filterGroupsPopup.deselect(BX("filter_field_group_hidden").value.value);
								BX("filter_field_group_hidden").value = "0";
								BX.addClass(BX("filter-field-group").parentNode, "webform-field-textbox-empty");
								BX.PreventDefault(e);
							});

						});
					</script>
				</td>
				<td valign="top">
				<div class="sonet-log-filter-date-title"><?=GetMessage("SONET_C30_T_FILTER_DATE");?>:</div>
					<select name="flt_date_datesel" onchange="__logOnDateChange(this)">
					<?
					foreach($arResult["DATE_FILTER"] as $k=>$v):
						?>
						<option value="<?=$k?>"<?if($_REQUEST["flt_date_datesel"] == $k) echo ' selected="selected"'?>><?=$v?></option>
						<?
					endforeach;
					?>
					</select>
					<span class="sonet-log-filter-date-days-span" style="display:none">
						<input type="text" name="flt_date_days" value="<?=htmlspecialcharsbx($_REQUEST["flt_date_days"])?>"  class="sonet-log-filter-date-days" size="2" /> <?echo GetMessage("SONET_C30_DATE_FILTER_DAYS")?>
					</span>
					<span class="sonet-log-filter-date-from-span" style="display:none">
						<input type="text" name="flt_date_from" value="<?=(array_key_exists("LOG_DATE_FROM", $arParams) ? $arParams["LOG_DATE_FROM"] : "")?>" class="sonet-log-filter-date-interval" />
						<?
						$APPLICATION->IncludeComponent(
							"bitrix:main.calendar",
							"",
							array(
								"SHOW_INPUT" => "N",
								"INPUT_NAME" => "flt_date_from",
								"INPUT_VALUE" => (array_key_exists("LOG_DATE_FROM", $arParams) ? $arParams["LOG_DATE_FROM"] : ""),
								"FORM_NAME" => "log_filter",
							),
							$component,
							array("HIDE_ICONS"	=> true)
						);?>
					</span>
					<span class="sonet-log-filter-date-hellip-span" style="display:none">&hellip;</span>
					<span class="sonet-log-filter-date-to-span" style="display:none">
						<input type="text" name="flt_date_to" value="<?=(array_key_exists("LOG_DATE_TO", $arParams) ? $arParams["LOG_DATE_TO"] : "")?>" class="sonet-log-filter-date-interval" />
						<?
						$APPLICATION->IncludeComponent(
							"bitrix:main.calendar",
							"",
							array(
								"SHOW_INPUT" => "N",
								"INPUT_NAME" => "flt_date_to",
								"INPUT_VALUE" => (array_key_exists("LOG_DATE_TO", $arParams) ? $arParams["LOG_DATE_TO"] : ""),
								"FORM_NAME" => "log_filter",
							),
							$component,
							array("HIDE_ICONS"	=> true)
						);?>
					</span>
					<script type="text/javascript">
						BX.ready(function(){
								BX.addCustomEvent('onAjaxInsertToNode', __logOnAjaxInsertToNode);
								__logOnDateChange(document.forms['log_filter'].flt_date_datesel);
								if (BX('sonet_log_comment_text'))
									BX('sonet_log_comment_text').onkeydown = BX.eventCancelBubble;
							}
						);
					</script>
				</td>
			</tr>
			</table>
			<?
			if (array_key_exists("flt_comments", $_REQUEST) && $_REQUEST["flt_comments"] == "Y")
				$bChecked = true;
			else
				$bChecked = false;
			?>
			<div id="flt_comments_cont" style="visibility: <?=(strlen($_REQUEST["flt_created_by_id"]) > 0 ? "visible" : "hidden")?>"><nobr><input type="checkbox" id="flt_comments" name="flt_comments" value="Y" <?=($bChecked ? "checked" : "")?>> <label for="flt_comments"><?=GetMessage("SONET_C30_T_FILTER_COMMENTS")?></label></nobr></div><?
			if ($arParams["SUBSCRIBE_ONLY"] == "Y"):
				if (array_key_exists("flt_show_hidden", $_REQUEST) && $_REQUEST["flt_show_hidden"] == "Y")
					$bChecked = true;
				else
					$bChecked = false;
				?>
				<div style="padding-top: 10px;"><nobr><input type="checkbox" id="flt_show_hidden" name="flt_show_hidden" value="Y" <?=($bChecked ? "checked" : "")?> onclick="__logFilterClick('<?=$featureID?>')"> <label for="flt_show_hidden"><?=GetMessage("SONET_C30_T_SHOW_HIDDEN")?></label></nobr></div>
				<div class="sonet-log-filter-line"></div>
				<?
			endif;
			?>
			<div class="sonet-log-filter-submit"><input type="submit" name="log_filter_submit" value="<?=GetMessage("SONET_C30_T_SUBMIT")?>"></div>
			<input type="hidden" id="flt_event_id_all" name="flt_event_id_all" value="<?=($bCheckedAll ? "Y" : "")?>">
			<input type="hidden" name="skip_subscribe" value="<?=($_REQUEST["skip_subscribe"] == "Y" ? "Y" : "N")?>">
			</form>

			</div>
			<div class="sonet-log-filter-lb">
				<div class="sonet-log-filter-rb"></div>
			</div>
		</div><?
	}
?></div>