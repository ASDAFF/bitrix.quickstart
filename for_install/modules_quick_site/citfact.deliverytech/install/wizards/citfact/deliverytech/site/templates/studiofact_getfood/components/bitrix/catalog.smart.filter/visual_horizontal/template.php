<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$i = 0;
foreach ($arResult["ITEMS"] as $key => $arItem) {
	if (!empty($arItem["VALUES"]) && ($arItem["VALUES"]["MIN"]["VALUE"] != $arItem["VALUES"]["MAX"]["VALUE"] || !isset($arItem["VALUES"]["MIN"]["VALUE"]))) {
		$i++;
	}
}
if ($i == 0) { return; }

CJSCore::Init(array("fx")); ?>
<div class="control_button_show active box">
	<a href="javascript: void(0);" title="<?=GetMessage("SF_SHOW_FILTER_PARAMS");?>"></a>
</div>
<div class="bx_filter_horizontal box padding smart_filter" style="overflow: unset;">
	<div class="bx_filter_section m4">
		<div class="bx_filter_title"><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></div>
		<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
			<? foreach($arResult["HIDDEN"] as $arItem): ?>
				<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
			<? endforeach; ?>
			<? foreach($arResult["ITEMS"] as $key => $arItem):
				$key = md5($key);
				if (isset($arItem["PRICE"])):
					if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
						continue;
					?>
					<div class="bx_filter_container row border_line price_filter">
						<span class="bx_filter_container_title col-xs-12 col-sm-4 col-md-2 col-lg-2"><?=$arItem["NAME"]?></span>
						<div class="bx_filter_param_area col-xs-12 col-sm-8 col-md-5 col-lg-4">
							<div class="bx_filter_param_area_block"><div class="bx_input_container">
								<?=GetMessage("CT_BCSF_FILTER_FROM");?><input class="min-price" type="text" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<? if (intVal($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0) { echo intVal($arItem["VALUES"]["MIN"]["HTML_VALUE"]); } else { echo intVal($arItem["VALUES"]["MIN"]["VALUE"]); } ?>" size="5" onkeyup="smartFilter.keyup(this)" />
							</div></div>
							<div class="bx_filter_param_area_block"><div class="bx_input_container">
								<?=GetMessage("CT_BCSF_FILTER_TO");?><input class="max-price" type="text" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<? if (intVal($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0) { echo intVal($arItem["VALUES"]["MAX"]["HTML_VALUE"]); } else { echo intVal($arItem["VALUES"]["MAX"]["VALUE"]); } ?>" size="5" onkeyup="smartFilter.keyup(this)" />
							</div></div>
							<div style="clear: both;"></div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
							<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
								<div class="bx_ui_slider_range" style="left: 0; right: 0%;"  id="drag_tracker_<?=$key?>"></div>
								<a class="bx_ui_slider_handle left"  href="javascript:void(0)" style="left:0;" id="left_slider_<?=$key?>"></a>
								<a class="bx_ui_slider_handle right" href="javascript:void(0)" style="right:0%;" id="right_slider_<?=$key?>"></a>
							</div>
							<div class="bx_filter_param_area">
								<div class="bx_filter_param_area_block left_value" id="curMinPrice_<?=$key?>"><?=round($arItem["VALUES"]["MIN"]["VALUE"]);?></div>
								<div class="bx_filter_param_area_block right_value" id="curMaxPrice_<?=$key?>"><?=round($arItem["VALUES"]["MAX"]["VALUE"]);?></div>
								<div style="clear: both;"></div>
							</div>
						</div>
					</div>
					<hr />

					<script type="text/javascript" defer="defer">
						var DoubleTrackBar<?=$key?> = new cDoubleTrackBar('drag_track_<?=$key?>', 'drag_tracker_<?=$key?>', 'left_slider_<?=$key?>', 'right_slider_<?=$key?>', {
							OnUpdate: function(){
								BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").value = this.MinPos;
								BX("<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").value = this.MaxPos;
							},
							Min: parseFloat(<?=$arItem["VALUES"]["MIN"]["VALUE"]?>),
							Max: parseFloat(<?=$arItem["VALUES"]["MAX"]["VALUE"]?>),
							MinInputId : BX('<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>'),
							MaxInputId : BX('<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>'),
							FingerOffset: 8,
							MinSpace: 1,
							RoundTo: 1,
							Precision: 2
						});
					</script>
				<?endif?>
			<?endforeach?>

			<div class="smart_filter_props">
				<div class="smart_filter_props0">
				<? $i = 0; foreach($arResult["ITEMS"] as $key => $arItem):
					if ($arItem["PROPERTY_TYPE"] == "N"):
						if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
							continue;
						?>
						<div class="bx_filter_container border_line price_filter<? if ($i == 0) { echo ' active'; } ?>">
							<span class="bx_filter_container_title"><?=$arItem["NAME"]?></span>
							<div class="bx_filter_block row" style="<? if ($i != 0) { echo 'display: none;'; } ?>"><br />
								<div class="bx_filter_param_area col-xs-12 col-sm-12 col-md-5 col-lg-5">
									<div class="bx_filter_param_area_block"><div class="bx_input_container">
										<?=GetMessage("CT_BCSF_FILTER_FROM");?><input class="min-price" type="text" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" />
									</div></div>
									<div class="bx_filter_param_area_block"><div class="bx_input_container">
										<?=GetMessage("CT_BCSF_FILTER_TO");?><input class="max-price" type="text" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" />
									</div></div>
									<div style="clear: both;"></div>
								</div>
								<div class="col-xs-12 col-xs-12 col-sm-12 col-md-7 col-lg-7">
									<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
										<div class="bx_ui_slider_range" style="left: 0; right: 0%;"  id="drag_tracker_<?=$key?>"></div>
										<a class="bx_ui_slider_handle left"  href="javascript:void(0)" style="left:0;" id="left_slider_<?=$key?>"></a>
										<a class="bx_ui_slider_handle right" href="javascript:void(0)" style="right:0%;" id="right_slider_<?=$key?>"></a>
									</div>
									<div class="bx_filter_param_area">
										<div class="bx_filter_param_area_block left_value" id="curMinPrice_<?=$key?>"><?=$arItem["VALUES"]["MIN"]["VALUE"]?></div>
										<div class="bx_filter_param_area_block right_value" id="curMaxPrice_<?=$key?>"><?=$arItem["VALUES"]["MAX"]["VALUE"]?></div>
										<div style="clear: both;"></div>
									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript" defer="defer">
							var DoubleTrackBar<?=$key?> = new cDoubleTrackBar('drag_track_<?=$key?>', 'drag_tracker_<?=$key?>', 'left_slider_<?=$key?>', 'right_slider_<?=$key?>', {
								OnUpdate: function(){
									BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").value = this.MinPos;
									BX("<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").value = this.MaxPos;
								},
								Min: parseFloat(<?=$arItem["VALUES"]["MIN"]["VALUE"]?>),
								Max: parseFloat(<?=$arItem["VALUES"]["MAX"]["VALUE"]?>),
								MinInputId : BX('<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>'),
								MaxInputId : BX('<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>'),
								FingerOffset: 8,
								MinSpace: 1,
								RoundTo: 0.01,
								Precision: 2
							});
						</script>
					<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
						<div class="bx_filter_container">
							<span class="bx_filter_container_title"><?=$arItem["NAME"]?></span>
							<div class="bx_filter_block" style="display: none;">
								<?foreach($arItem["VALUES"] as $val => $ar):?>
								<span class="<?echo $ar["DISABLED"] ? 'disabled': ''?> NiceCheck">
									<input type="checkbox" value="<?echo $ar["HTML_VALUE"]?>" name="<?echo $ar["CONTROL_NAME"]?>" id="<?echo $ar["CONTROL_ID"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?> onclick="smartFilter.click(this)" />
									<label for="<?echo $ar["CONTROL_ID"]?>"><?echo $ar["VALUE"];?></label>
								</span>
								<?endforeach;?>
							</div>
						</div>
					<? $i++; endif;
				endforeach;?>
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="bx_filter_control_section">
				<div class="control_button_hide">
					<a href="javascript: void(0);" class="javascript"><?=GetMessage("SF_HIDE_FILTER_PARAMS");?></a>
				</div>
				<div class="control_button">
					<input class="bx_filter_search_button" type="submit" id="set_filter" name="set_filter" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>" />
					<input class="bx_filter_search_button" type="submit" id="del_filter" name="del_filter" value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>" />
					<div class="bx_filter_popup_result" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?>>
						<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
						<span class="ecke"></span>
					</div>
				</div>
			</div>
		</form>
		<div style="clear: both;"></div>
	</div>
</div>
<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>');
</script>