<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jslider.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jslider.plastic.css');
?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/jshashtable-2.1_src.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/jquery.numberformatter-1.2.3.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/tmpl.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/jquery.dependClass-0.1.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/draggable-0.1.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/filter/jquery.slider.js');?>

<?$IsIe = (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? true : false;?>

<div class="filter_wrapper">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
		<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input
				type="hidden"
				name="<?echo $arItem["CONTROL_NAME"]?>"
				id="<?echo $arItem["CONTROL_ID"]?>"
				value="<?echo $arItem["HTML_VALUE"]?>"
			/>
		<?endforeach;?>
		<a href="javascript:void(0)" class="hide_show_filter"></a>
		<div class="filter">
			<!--<h5><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></h5>-->
			<ul >
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?if(isset($arItem["PRICE"])):?>
					<li class="level_1 current"><span><a href="javascript:void(0)" class="showchild"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
						<ul>
							<?
							if (empty($arItem["VALUES"]["MIN"]["VALUE"]))  $arItem["VALUES"]["MIN"]["VALUE"] = 0;
							if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 50000;
							?>
							<li class="current level_2">
								<input
										class="max_price_value"
										type="hidden"
										name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
										id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										<?/*value="<?if (!empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"*/?>
										size="5"
										placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>"
										onchange="smartFilter.keyup(this)"
										/>
								<input
										class="min_price_value"
										type="hidden"
										name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										<?/*value="<?if (!empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"*/?>
										size="5"
										placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>"
										onchange="smartFilter.keyup(this)"
										/>

								<?
								$middleValue = $arItem["VALUES"]["MIN"]["VALUE"] + round(($arItem["VALUES"]["MAX"]["VALUE"]-$arItem["VALUES"]["MIN"]["VALUE"])/2);
								?>
								<div class="layout-slider">
									<input id="price_slider_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" type="slider" name="area" value="<?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? $arItem["VALUES"]["MIN"]["VALUE"] : $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>;<?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? $arItem["VALUES"]["MAX"]["VALUE"] : $arItem["VALUES"]["MAX"]["HTML_VALUE"];?>" />
								</div>
								<script type="text/javascript" >
									jQuery("#price_slider_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").slider({
										from: <?=$arItem["VALUES"]["MIN"]["VALUE"]?>,
										to: <?=$arItem["VALUES"]["MAX"]["VALUE"]?>, /*heterogeneity: ['50/5000', '75/15000'],*/
										<?if ($arItem["VALUES"]["MIN"]["VALUE"] != $arItem["VALUES"]["MAX"]["VALUE"]):?>
										scale: [<?=$arItem["VALUES"]["MIN"]["VALUE"]?>, '|', <?=$arItem["VALUES"]["MIN"]["VALUE"]+round(($middleValue-$arItem["VALUES"]["MIN"]["VALUE"])/2)?>, '|', '<?=$middleValue?>', '|', <?=$middleValue + round(($arItem["VALUES"]["MAX"]["VALUE"]-$middleValue)/2)?>, '|', <?=$arItem["VALUES"]["MAX"]["VALUE"]?>],
										<?endif?>
										limits: false, step: 1, dimension: '&nbsp;<?=GetMessage("CATALOG_CURRENCY")?>', skin: "plastic",
										min_control_id:"<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>", max_control_id:"<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>", first_time:"Y" });
								</script>
							</li>
						</ul>
					</li>
				<?endif;?>
			<?endforeach;?>

			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?if($arItem["PROPERTY_TYPE"] == "N"):?>
					<?if ($arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"]) continue;?>
					<li class="level_1"><span class="showchild"><a href="javascript:void(0)"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
						<ul>
							<?
								if (empty($arItem["VALUES"]["MIN"]["VALUE"]))  $arItem["VALUES"]["MIN"]["VALUE"] = 0;
								if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 50000;
							?>
							<li class="level_2">
								<input
									class="max-price"
									type="hidden"
									name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
									id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
									value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
									placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>"
									size="5"
									onkeyup="smartFilter.keyup(this)"
									style="float:right;"
									/>
								<?if ($IsIe):?><span style="margin-top: 11px; margin-left: 80px; position: absolute;"><?echo GetMessage("CT_BCSF_FILTER_TO")?></span><?endif?>
								<input
									class="min-price"
									type="hidden"
									name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
									id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
									value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
									placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>"
									size="5"
									onkeyup="smartFilter.keyup(this)"
								/>

								<?
								$middleValue = $arItem["VALUES"]["MIN"]["VALUE"] + round(($arItem["VALUES"]["MAX"]["VALUE"]-$arItem["VALUES"]["MIN"]["VALUE"])/2);
								?>
								<div class="layout-slider">
									<input id="price_slider_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" type="slider" name="area" value="<?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? $arItem["VALUES"]["MIN"]["VALUE"] : $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>;<?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? $arItem["VALUES"]["MAX"]["VALUE"] : $arItem["VALUES"]["MAX"]["HTML_VALUE"];?>" />
								</div>
								<script type="text/javascript" >
									jQuery("#price_slider_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").slider({
										from: <?=$arItem["VALUES"]["MIN"]["VALUE"]?>,
										to: <?=$arItem["VALUES"]["MAX"]["VALUE"]?>, /*heterogeneity: ['50/5000', '75/15000'],*/
										<?if ($arItem["VALUES"]["MIN"]["VALUE"] != $arItem["VALUES"]["MAX"]["VALUE"]):?>
										scale: [<?=$arItem["VALUES"]["MIN"]["VALUE"]?>, '|', <?=$arItem["VALUES"]["MIN"]["VALUE"]+round(($middleValue-$arItem["VALUES"]["MIN"]["VALUE"])/2)?>, '|', '<?=$middleValue?>', '|', <?=$middleValue + round(($arItem["VALUES"]["MAX"]["VALUE"]-$middleValue)/2)?>, '|', <?=$arItem["VALUES"]["MAX"]["VALUE"]?>],
										<?else:?>
										scale: [<?=$arItem["VALUES"]["MIN"]["VALUE"]?>, '|',<?=$arItem["VALUES"]["MIN"]["VALUE"]?>],
										<?endif?>
										limits: false,
										step: 1,
										skin: "plastic",
										min_control_id:"<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>",
										max_control_id:"<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>",
										first_time:"Y" });
								</script>
							</li>
						</ul>
					</li>
					<?//if (CModule::IncludeModule("sale")) $curCurrency = CSaleLang::GetLangCurrency(LANGUAGE_ID); SaleFormatCurrency($arItem["VALUES"]["MAX"]["VALUE"], $curCurrency);?>
				<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):;?>
				<li class="level_1"><span class="showchild"><a href="javascript:void(0)"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
					<ul class="filter_item_hidden">
						<?foreach($arItem["VALUES"] as $val => $ar):?>
						<li class="level_2<?if ($ar["DISABLED"]) echo ' level_2_disabled'?>"><input
							type="checkbox"
							value="<?echo $ar["HTML_VALUE"]?>"
							name="<?echo $ar["CONTROL_NAME"]?>"
							id="<?echo $ar["CONTROL_ID"]?>"
							<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
							onclick="smartFilter.click(this)"
							/>
							<label for="<?echo $ar["CONTROL_ID"]?>"><?echo $ar["VALUE"];?></label></li>
						<?endforeach;?>
					</ul>
				</li>
				<?endif;?>
			<?endforeach;?>
			</ul>
			<input type="submit" id="set_filter" name="set_filter" class="bt1 lupe" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>" />
			<input type="submit" id="del_filter" name="del_filter" class="bt2" value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>" />

			<div class="modef filter_pop_up" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?>>
				<p><?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?></p>
				<a class="showchild" href="<?echo $arResult["FILTER_URL"]?>"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
				<span class="filter_pop_up_arrow"></span>
			</div>

		</div>
	</form>
</div>

<script type="text/javascript">
	var smartFilter = new JCSmartFilter('<?echo $arResult["FORM_ACTION"]?>');

	$(document).ready(function(){
	/* Filter - hide\show - begin */
		$('.hide_show_filter').toggle(function(){
				$('.filter').css({'display': 'none'});
				$('.left_block').css({'width': '0'});
				$('.right_block').css({'marginLeft': '0'});
				$(this).css({'position':'fixed', 'left':'0', 'top':'376px', 'backgroundPosition':'-20px 0'});
			},function(){
				$('.right_block').css({'marginLeft': '250px'});
				$('.left_block').css({'width': '244px'});
				$('.filter').css({'display': 'block'});
				$(this).css({'position':'absolute', 'left':'238px', 'top':'250px', 'backgroundPosition':'0 0'});
		});

		// Store variables
		var filter_head = $('.level_1 > .showchild');
		// Click function
		filter_head.on('click', function(event) {
			// Disable header links
			event.preventDefault();
			// Show and hide the tabs on click
			if ($(this).next().next().hasClass('filter_item_hidden')){
				$(this).next().next().slideDown('normal');
				$(this).next().next().removeClass('filter_item_hidden');
			}
			else
			{
				$(this).next().next().slideUp('normal');
				$(this).next().next().addClass('filter_item_hidden');
			}
		});

		var filterHeight = $('.filter').height();
		if (filterHeight < '220')
		{
			$('.hide_show_filter').addClass('hide_show_filter_bottom');
			$('.filter_wrapper').css({'position': 'relative'});
			$('.hide_show_filter').css({'top': 'inherit'});
			$('.hide_show_filter').css({'bottom': '-20px'});
		}

		/* Filter - hide\show - begin */
		$('.hide_show_filter').toggle(function(){
				$('.filter_wrapper').css({'position': 'static'});
				$('.filter').css({'display': 'none'});
				$('.left_block').css({'width': '0'});
				$('.right_block').css({'marginLeft': '0'});
				$(this).css({'position':'fixed', 'left':'0', 'top':'390px', 'backgroundPosition':'-20px 0'});
				$(this).removeClass('hide_show_filter_bottom');
			},function(){
				$('.right_block').css({'marginLeft': '250px'});
				$('.left_block').css({'width': '244px'});
				$('.filter').css({'display': 'block'});
				$(this).css({'position':'absolute', 'left':'238px', 'top':'200px'});
				if (filterHeight < '220')
				{
					$('.hide_show_filter').addClass('hide_show_filter_bottom');
					$('.filter_wrapper').css({'position': 'relative'});
					$('.hide_show_filter').css({'top': 'inherit'});
					$('.hide_show_filter').css({'bottom': '-20px'});
					$(this).css({'backgroundPosition':'-40px 0'});
				}
				else
				{
					$(this).css({'backgroundPosition':'0 0'});
				}
		});
		/* Filter - hide\show - end */
	});
</script>


