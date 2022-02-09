<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.widget.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.mouse.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.slider.js');?>
<?$IsIe = (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? true : false;?>
<?if(!isset($_REQUEST["ajax"])) $this->SetViewTarget("right_sidebar");?>
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
	<?foreach($arResult["HIDDEN"] as $arItem):?>
		<input
			type="hidden"
			name="<?echo $arItem["CONTROL_NAME"]?>"
			id="<?echo $arItem["CONTROL_ID"]?>"
			value="<?echo $arItem["HTML_VALUE"]?>"
		/>
	<?endforeach;?>
	<div class="filtren">
		<h5><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></h5>
		<ul class="lsnn">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?if(isset($arItem["PRICE"])):?>
				<li class="lvl1 current"><span><a href="javascript:void(0)" class="showchild"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
					<ul class="lsnn">
						<?
						if (empty($arItem["VALUES"]["MIN"]["VALUE"]))  $arItem["VALUES"]["MIN"]["VALUE"] = 0;
						if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 50000;
						?>
						<li class="current lvl2">
							<?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_FROM")?></span><?endif?>
							<input
									class="max-price"
									type="text"
									name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
									id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
									value="<?if (!empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
									size="5"
									placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>"
									onkeyup="smartFilter.keyup(this)"
									style="float:right;"
									/>
							<?if ($IsIe):?><span style="margin-top: 11px; margin-left: 80px; position: absolute;"><?echo GetMessage("CT_BCSF_FILTER_TO")?></span><?endif?>
							<input
									class="min-price"
									type="text"
									name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
									id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
									value="<?if (!empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
									size="5"
									placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>"
									onkeyup="smartFilter.keyup(this)"
									/>
						</li>
						<li class="current lvl2">
							<div class="slider-range" id="slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
							<div class="max-price" id="max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"></div>
							<div class="min-price" id="min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
						</li>
					</ul>
				</li>
				<?//if (CModule::IncludeModule("sale")) $curCurrency = CSaleLang::GetLangCurrency(LANGUAGE_ID); SaleFormatCurrency($arItem["VALUES"]["MAX"]["VALUE"], $curCurrency);?>
				<script>
					var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($APPLICATION->GetCurPageParam())?>');
					//$(function() {
						var minprice = <?=$arItem["VALUES"]["MIN"]["VALUE"]?>;
						var maxprice = <?=$arItem["VALUES"]["MAX"]["VALUE"];?>;
						$( "#slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" ).slider({
							range: true,
							min: minprice,
							max: maxprice,
							values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? $arItem["VALUES"]["MIN"]["VALUE"] : $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? $arItem["VALUES"]["MAX"]["VALUE"] : $arItem["VALUES"]["MAX"]["HTML_VALUE"];?> ],
							slide: function( event, ui ) {
								$("#<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").val(ui.values[0]);
								$("#<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").val(ui.values[1]);
								smartFilter.keyup(BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"));
							}
						});
						$("#min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").text(minprice);
						$("#max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").text(maxprice);
						//$(".min-price").val($(".slider-range").slider("values", 0));
						//$(".max-price").val($(".slider-range").slider("values", 1));
					//});
				</script>
			<?endif;?>
		<?endforeach;?>


		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?if($arItem["PROPERTY_TYPE"] == "N"/* || isset($arItem["PRICE"])*/):?>
				<li class="lvl1"><span><a href="javascript:void(0)" class="ShowChildren" onclick="return ShowChildren(this)"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
					<ul class="lsnn">
						<?
							//if (empty($arItem["VALUES"]["MIN"]["VALUE"]))  $arItem["VALUES"]["MIN"]["VALUE"] = 0;
							//if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 50000;
						?>
						<li class="lvl2">
							<?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_FROM")?></span><?endif?>
							<input
								class="max-price"
								type="text"
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
								type="text"
								name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
								id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
								value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
								placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>"
								size="5"
								onkeyup="smartFilter.keyup(this)"
							/>
						</li>
						<?if ($arItem["VALUES"]["MIN"]["VALUE"] > 0 && $arItem["VALUES"]["MAX"]["VALUE"] > 0 && $arItem["VALUES"]["MIN"]["VALUE"] < $arItem["VALUES"]["MAX"]["VALUE"]):?>
						<li class="lvl2">
							<div class="slider-range" id="slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
							<div class="max-price" id="max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"></div>
							<div class="min-price" id="min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
						</li>
						<?endif?>
					</ul>
				</li>
				<?//if (CModule::IncludeModule("sale")) $curCurrency = CSaleLang::GetLangCurrency(LANGUAGE_ID); SaleFormatCurrency($arItem["VALUES"]["MAX"]["VALUE"], $curCurrency);?>
				<?if ($arItem["VALUES"]["MIN"]["VALUE"] > 0 && $arItem["VALUES"]["MAX"]["VALUE"] > 0 && $arItem["VALUES"]["MIN"]["VALUE"] < $arItem["VALUES"]["MAX"]["VALUE"]):?>
				<script>
					var minprice2 = <?=$arItem["VALUES"]["MIN"]["VALUE"]?>;
					var maxprice2 = <?=$arItem["VALUES"]["MAX"]["VALUE"]?>;
					$( "#slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" ).slider({
						range: true,
						min: minprice2,
						max: maxprice2,
						values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? $arItem["VALUES"]["MIN"]["VALUE"] : $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? $arItem["VALUES"]["MAX"]["VALUE"] : $arItem["VALUES"]["MAX"]["HTML_VALUE"]?> ],
						slide: function( event, ui ) {
							$("#<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").val(ui.values[0]);
							$("#<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").val(ui.values[1]);
							smartFilter.keyup(BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"));
						}
					});
					$("#max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").text(maxprice2);
					$("#min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").text(minprice2);
				</script>
				<?endif?>
			<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):;?>
			<li class="lvl1"><span><a href="javascript:void(0)" class="showchild" onclick="return ShowChildren(this)"><?=$arItem["NAME"]?></a></span><span class="for_modef"></span>
				<ul class="lsnn">
					<?foreach($arItem["VALUES"] as $val => $ar):?>
					<li class="lvl2<?echo $ar["DISABLED"]? ' lvl2_disabled': ''?>"><input
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

		<div class="modef" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?>>
			<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
			<a href="<?echo $arResult["FILTER_URL"]?>"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
			<span class="ecke"></span>
		</div>

	</div>
</form>
<?if(!isset($_REQUEST["ajax"])) $this->EndViewTarget("right_sidebar");?>
<script type="text/javascript">
	function setHeightlvlp(clickitem){
		if(clickitem.parent("span").parent("li").find("ul:first").attr('rel')){
			heightlvl2Ul = clickitem.parent("span").parent("li").find("ul:first").attr('rel');
		} else {
			clickitem.parent("span").parent("li").find("ul:first").css({display: 'block',height:"auto"});
			heightlvl2Ul = clickitem.parent("span").parent("li").find("ul:first").height();
		}
	}

	$(document).ready(function() {
		var lis = $('.filtren ul').find('li');
		for(var i = 0; i < lis.length; i++) {
			if($(lis[i]).hasClass('current')) {
				if($(lis[i]).parents("li").hasClass('lvl1')){

					var ul = $(lis[i]).find('ul:first');
					$(ul).css({display: 'block',height:"auto"});
					var h = $(ul).height();
					$(ul).css({height: 0, display: 'block'});

					var ulp= $(lis[i]).parents("li.lvl1").find('ul:first');
					$(ulp).css({display: 'block'});
					var hp = $(ulp).height();
					$(ulp).css({height: 0, display: 'block'});

					$(ul).attr("rel", h);
					// $(ulp).attr("rel", hp);
					$(ul).css({height: h+'px'});
					$(ulp).css({height: h+hp+15+'px'});
				} else {
					var ul = $(lis[i]).find('ul:first');
					$(ul).css({display: 'block',height:"auto"});
					var h = $(ul).height();
					$(ul).css({height: 0, display: 'block'});
					$(ul).attr("rel", h);
					$(ul).css({height: h+'px'})
				}
			}
		}
	});

	function ShowChildren (element)
	{
		var clickitem = $(element);
		if( clickitem.parent("span").parent("li").hasClass('lvl1')){
			if( clickitem.parent("span").parent("li").hasClass('current')){
				clickitem.parent("span").parent("li").find("ul").animate({height: 0,'padding-top':0,'padding-bottom':0}, 300);
				clickitem.parent("span").parent("li").removeClass("current");
				clickitem.parent("span").parent("li").find(".current").removeClass("current");

			} else {

				setHeightlvlp(clickitem);
				clickitem.parent("span").parent("li").find("ul:first").attr('rel',heightlvl2Ul);
				clickitem.parent("span").parent("li").find("ul:first").css({height: 0, display: 'block'});
				clickitem.parent("span").parent("li").find("ul:first").animate({height: heightlvl2Ul+'px','padding-top':10+'px','padding-bottom':25+'px'}, 300);
				clickitem.parent("span").parent("li").addClass("current");
			}
		} else {
			if( clickitem.parent("span").parent("li").hasClass('current')){
				clickitem.parent("span").parent("li").find("ul").animate({height: 0,'padding-top':0,'padding-bottom':0}, 300);
				heightLVL1 = clickitem.parents(".lvl1").find("ul:first").attr('rel');
				clickitem.parents(".lvl1").find("ul:first").animate({height: heightLVL1+"px"}, 300);
				clickitem.parent("span").parent("li").removeClass("current");
			} else {
				setHeightlvlp(clickitem);

				heightLVL1 = clickitem.parents(".lvl1").find("ul:first").attr('rel');


				clickitem.parent("span").parent("li").find("ul:first").attr('rel',heightlvl2Ul);
				clickitem.parent("span").parent("li").find("ul:first").css({height: 0, display: 'block'});
				clickitem.parent("span").parent("li").find("ul:first").animate({height: heightlvl2Ul+'px','padding-top':10+'px','padding-bottom':25+'px'}, 300);
				clickitem.parents(".lvl1").find("ul:first").animate({height:  parseInt(heightlvl2Ul)+ parseInt(heightLVL1)+'px','padding-top':10+'px','padding-bottom':20+'px'}, 300);
				clickitem.parent("span").parent("li").addClass("current");
			}
		}
		return false;
	}
</script>

