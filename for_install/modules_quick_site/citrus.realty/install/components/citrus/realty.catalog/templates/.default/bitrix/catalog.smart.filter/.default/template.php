<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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

if (empty($arResult["ITEMS"]))
	return;

$this->SetViewTarget('sidebar', 100);

CJSCore::Init(array("fx"));

if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->GetFolder().'/themes/'.$arParams["TEMPLATE_THEME"].'/colors.css'))
	$APPLICATION->SetAdditionalCSS($this->GetFolder().'/themes/'.$arParams["TEMPLATE_THEME"].'/colors.css');
?>
<div class="contact-corner selection bx_filter_vertical bx_<?=$arParams["TEMPLATE_THEME"]?>">
	<h3><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></h3>
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter"><dl>
			<?foreach($arResult["HIDDEN"] as $arItem):?>
				<input
					type="hidden"
					name="<?echo $arItem["CONTROL_NAME"]?>"
					id="<?echo $arItem["CONTROL_ID"]?>"
					value="<?echo $arItem["HTML_VALUE"]?>"
					/>
			<?endforeach;?>
			<?foreach($arResult["ITEMS"] as $key=>$arItem):
				$key = md5($key);
				?>
				<?if(isset($arItem["PRICE"])):?>
				<?
				if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
					continue;
				?>
				<dt><?=$arItem["NAME"]?></dt>
				<dd class="dd-price">
					<input
						class="min-price"
						type="text"
						name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
						id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
						value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
						size="5"
						onkeyup="smartFilter.keyup(this)"
						/>
					<input
						class="max-price"
						type="text"
						name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
						id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
						value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
						size="5"
						onkeyup="smartFilter.keyup(this)"
						/>
					<div style="clear: both;"></div>
					<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
						<div class="bx_ui_slider_range" style="left: 0; right: 0%;"  id="drag_tracker_<?=$key?>"></div>
						<a class="bx_ui_slider_handle left"  href="javascript:void(0)" style="left:0;" id="left_slider_<?=$key?>"></a>
						<a class="bx_ui_slider_handle right" href="javascript:void(0)" style="right:0%;" id="right_slider_<?=$key?>"></a>
					</div>
					<div class="bx_filter_param_area">
						<div class="bx_filter_param_area_block" id="curMinPrice_<?=$key?>"><?=$arItem["VALUES"]["MIN"]["VALUE"]?></div>
						<div class="bx_filter_param_area_block" id="curMaxPrice_<?=$key?>"><?=$arItem["VALUES"]["MAX"]["VALUE"]?></div>
						<div style="clear: both;"></div>
					</div>
				</dd>
				<script type="text/javascript">
					var DoubleTrackBar<?=$key?> = new cDoubleTrackBar('drag_track_<?=$key?>', 'drag_tracker_<?=$key?>', 'left_slider_<?=$key?>', 'right_slider_<?=$key?>', {
						OnUpdate: function(){
							BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").value = this.MinPos;
							BX("<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").value = this.MaxPos;
						},
						Min: parseFloat(<?=$arItem["VALUES"]["MIN"]["VALUE"]?>),
						Max: parseFloat(<?=$arItem["VALUES"]["MAX"]["VALUE"]?>),
						MinInputId : BX('<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>'),
						MaxInputId : BX('<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>'),
						FingerOffset: 10,
						MinSpace: 1,
						RoundTo: 0.01,
						Precision: 2
					});
				</script>
			<?endif?>
			<?endforeach?>

			<?
			$showItems = function ($arItems, $hidden = false) use ($arParams)
			{
				?>
				<?foreach($arItems as $key=>$arItem):
				//echo '<pre>:$arItem: ' . htmlspecialcharsbx(var_export($arItem, true)) . '</pre>';
				if (is_array($arParams["SHOWN_PROPERTIES"]))
				{
					if ($hidden)
					{
						if (in_array($arItem["CODE"], $arParams["SHOWN_PROPERTIES"]))
							continue;
					} else
					{
						if (!in_array($arItem["CODE"], $arParams["SHOWN_PROPERTIES"]))
							continue;
					}
				}
				?>
				<?if($arItem["PROPERTY_TYPE"] == "N" ):?>
				<?
				if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
					continue;
				?>
				<dt><?=$arItem["NAME"]?></dt>
				<dd class="dd-price">
					<input
						class="min-price"
						type="text"
						name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
						id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
						value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
						size="5"
						onkeyup="smartFilter.keyup(this)"
						/>
					<input
						class="max-price"
						type="text"
						name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
						id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
						value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
						size="5"
						onkeyup="smartFilter.keyup(this)"
						/>
					<div style="clear: both;"></div>
					<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
						<div class="bx_ui_slider_range" style="left: 0; right: 0%;"  id="drag_tracker_<?=$key?>"></div>
						<a class="bx_ui_slider_handle left"  href="javascript:void(0)" style="left:0;" id="left_slider_<?=$key?>"></a>
						<a class="bx_ui_slider_handle right" href="javascript:void(0)" style="right:0%;" id="right_slider_<?=$key?>"></a>
					</div>
					<div class="bx_filter_param_area">
						<div class="bx_filter_param_area_block" id="curMinPrice_<?=$key?>"><?=$arItem["VALUES"]["MIN"]["VALUE"]?></div>
						<div class="bx_filter_param_area_block" id="curMaxPrice_<?=$key?>"><?=$arItem["VALUES"]["MAX"]["VALUE"]?></div>
						<div style="clear: both;"></div>
					</div>
				</dd>
				<script type="text/javascript">
					var DoubleTrackBar<?=$key?> = new cDoubleTrackBar('drag_track_<?=$key?>', 'drag_tracker_<?=$key?>', 'left_slider_<?=$key?>', 'right_slider_<?=$key?>', {
						OnUpdate: function(){
							BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").value = this.MinPos;
							BX("<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").value = this.MaxPos;
						},
						Min: parseFloat(<?=$arItem["VALUES"]["MIN"]["VALUE"]?>),
						Max: parseFloat(<?=$arItem["VALUES"]["MAX"]["VALUE"]?>),
						MinInputId : BX('<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>'),
						MaxInputId : BX('<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>'),
						FingerOffset: 10,
						MinSpace: 1,
						RoundTo: 1
					});
				</script>
			<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):

				$checked = false;
				foreach ($arItem["VALUES"] as $val => $ar)
				{
					if ($checked = $ar["CHECKED"])
						break;
				}
				?>
				<div class="selection-more<?=($checked ? ' active' : '')?>">
					<a href="javascript:void(0);" onclick="hideFilterProps(this)" class="dotted"><?=$arItem["NAME"]?></a>
					<div class="filter-checkboxes bx_filter_block<?=(count($arItem["VALUES"]) > 14 ? " filter-checkboxes-double" : "")?>">
						<?foreach($arItem["VALUES"] as $val => $ar):?>
							<p class="<?echo $ar["DISABLED"] ? 'disabled': ''?>" title="<?=$ar["VALUE"]?>">
								<input
									type="checkbox"
									value="<?echo $ar["HTML_VALUE"]?>"
									name="<?echo $ar["CONTROL_NAME"]?>"
									id="<?echo $ar["CONTROL_ID"]?>"
									<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
									onclick="smartFilter.click(this)"
									/>
								<label for="<?echo $ar["CONTROL_ID"]?>">
									<?echo $ar["VALUE"];?>
								</label>
							</p>
						<?endforeach;?>
					</div>
				</div>
			<?endif;?>
			<?endforeach;
			};

			$showItems($arResult["ITEMS"]);
			// nook переносить заполненные поля фильтра в «видимые»
			if (is_array($arParams["SHOWN_PROPERTIES"]))
			{
			?>
			<div class="selection-more section-additional">
				<a href="javascript:void(0);" onclick="hideFilterProps(this)" class="dotted"><?=GetMessage("CITRUS_REALTY_MORE_FILTER_PARAMS")?></a>
				<div class="bx_filter_block"><?$showItems($arResult["ITEMS"], true);?></div>
				<?
				}
				?>

				<div style="clear: both;"></div>
				<div class="bx_filter_control_section">
					<div class="bx_filter_popup_result left" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;">
						<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
						<span class="arrow"></span>
						<a href="<?echo $arResult["FILTER_URL"]?>"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
					</div>
				</div>

		</dl>
		<input type="submit" id="set_filter" name="set_filter" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>">
		<input type="submit" class="secondary" id="del_filter" name="del_filter" value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>">
	</form>
</div>
<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>');
</script>