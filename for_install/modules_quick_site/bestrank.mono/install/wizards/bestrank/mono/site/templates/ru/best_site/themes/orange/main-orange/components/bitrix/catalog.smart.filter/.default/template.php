<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.widget.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.mouse.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.slider.js');?>

<link rel="stylesheet" type="text/css" href='<?=SITE_TEMPLATE_PATH?>/css/jquery.jscrollpane.css' />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jscrollpane.js"></script>


<?$IsIe = (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? true : false;?>




<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
	<?foreach($arResult["HIDDEN"] as $arItem):?>
		<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>"/>
	<?endforeach;?>
	<div class="filtren compare">
		<h5><?echo GetMessage("CT_BCSF_FILTER_TITLE")?> </h5>
		<table style="  margin-bottom: 15px;">
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if(isset($arItem["PRICE"])):?>
			<?
				if (empty($arItem["VALUES"]["MIN"]["VALUE"])) $arItem["VALUES"]["MIN"]["VALUE"] = 0;
				if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 100000;
			?>
			<tr class="cnt" id="<?=$arItem["CODE"]?>">
				<td  class="<?=$arItem["CODE"]?> caption" ><?=$arItem["NAME"]?></td>
				<td ><?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_FROM")?></span><?endif?><input class="min-price" type="text"  name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>" id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)"/></td>
				<td ><?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_TO")?></span><?endif?><input class="max-price" type="text"  name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>" id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" /></td>
			</tr>
			<tr>
				<td colspan="3" style="vertical-align:top;">
					<div class="slider-range" id="slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" style="margin:0"></div>
					<div class="max-price" id="max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"></div>
					<div class="min-price" id="min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
				</td>
			</tr>
			<script>
				$(function() {
					var minprice = <?=CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"])?>;
					var maxprice = <?=CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"])?>;
					$("#slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").slider({
						range: true,
						min: minprice,
						max: maxprice,
						values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MIN"]["HTML_VALUE"])?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MAX"]["HTML_VALUE"])?> ],
						slide: function( event, ui ) {
							$("#<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").val(ui.values[0]);
							$("#<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").val(ui.values[1]);
							smartFilter.keyup(BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"));
						}
					});
					$("#max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").text(maxprice);
					$("#min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").text(minprice);


				});
			</script>
				<?unset($arResult["ITEMS"][$key]);?>
			<?endif;?>
		<?endforeach;?>
		</table>




		<div class="modef" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])  ) echo 'style="display:none"';?>>
			<?AddMessage2Log(var_export($arResult["FILTER_URL"], true));?>
			<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
			<a href="<?echo $arResult["FILTER_URL"]?>" ><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
			<span class="ecke"></span>
		</div>


	<div class="more-options-hfilter"><?if (count($arResult["ITEMS"]) > 0):?>
		
			<div class="catf" id="horizontalfilter">
				<ul class="horizontalfilter"> <!-- Titles -->
				<?$flag = 0;?>
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?if(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
					<li  class="<?=$arItem["CODE"]?> <?if ($flag == 0) echo " active"?>">
						






						<?if (!empty($arItem["VALUES"])):?>
						<div class="cnt<?if ($flag == 0) echo " active"?>" id="<?=$arItem["CODE"]?>">
							<?if($arItem["PROPERTY_TYPE"] == "N" && !isset($arItem["PRICE"])):?>


 								<?if ($arItem["VALUES"]["MIN"]["VALUE"] > 0 && $arItem["VALUES"]["MAX"]["VALUE"] > 0 && $arItem["VALUES"]["MIN"]["VALUE"] < $arItem["VALUES"]["MAX"]["VALUE"]):?>

								<table style="margin:0"><tbody><tr class="cnt" >
												<td class="BASE caption"><?=$arItem["NAME"]?></td>
												<td><?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_FROM")?></span><?endif?><input  type="text" class="<?=$arItem["CODE"]?>" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>" value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)"/></td>
												<td><?if ($IsIe):?><span style="position: absolute; margin-top: 11px;margin-left: -21px;"><?echo GetMessage("CT_BCSF_FILTER_TO")?></span><?endif?><input type="text" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>" value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" /></td>
											</tr>
											<tr>
												<td colspan="3" style="vertical-align:top;">
																	<div class="slider-range" id="slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" style="margin:5px auto 3px;"></div>
																	<div class="max-price" id="max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"></div>
																	<div class="min-price" id="min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"></div>
												</td>
											</tr>
								</tbody></table>




								<script>
									var minprice2 = <?=CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"])?>;
									var maxprice2 = <?=CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"])?>;
									$("#slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").slider({
										range: true,
										min: minprice2,
										max: maxprice2,
										values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MIN"]["HTML_VALUE"])?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MAX"]["HTML_VALUE"])?> ],
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


							<?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
							
								
								<div class="caption">
									<?if($arItem["JUST_CHECKBOX"]){
										foreach($arItem["VALUES"] as $val => $ar){?>
											<input type="checkbox" value="<?echo $ar["HTML_VALUE"]?>" name="<?echo $ar["CONTROL_NAME"]?>" id="<?echo $ar["CONTROL_ID"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?> onclick="smartFilter.click(this)"/>
										<?}?> <label for="<?echo $ar["CONTROL_ID"]?>" ><?=$arItem["NAME"]?></label><?
									} else {?>
									<div class="arrow <?=(count($arItem["SELECTED"])>0 ? 'opened' : 'closed')?>"></div><a href="#self" ><?=$arItem["NAME"]?></a><ul <?=(count($arItem["SELECTED"])>0 ? 'style="display:block;"' : '')?>><?
									foreach($arItem["VALUES"] as $val => $ar){?>
										<li class="lvl2<?echo $ar["DISABLED"]? ' lvl2_disabled': ''?> "><input style="/*display:none;*/" type="checkbox" value="<?echo $ar["HTML_VALUE"]?>" name="<?echo $ar["CONTROL_NAME"]?>" id="<?echo $ar["CONTROL_ID"]?>" <?echo $ar["CHECKED"]? 'checked="checked"': ''?> onclick="smartFilter.click(this)"/>
										<label for="<?echo $ar["CONTROL_ID"]?>" class=""><?=($ar["LIST_NAME"] ? $ar["LIST_NAME"] : $ar["VALUE"])?></label>
										</li>
										<?
									}?></ul><?
								}?></div>					
							






	
							<div style="clear:both;"></div>
							<?endif;?>	
						</div>
						<?endif?>	








					<?$flag = 1;?>
					<?endif?>
				<?endforeach?>
				</ul>
			</div>
			
	<?endif?></div>	


		<div class="buttons">
			<input type="submit" id="set_filter" name="set_filter" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>" class="bt1 lupe"/>
			<input type="submit" id="del_filter" name="del_filter" value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>" class="bt2"/>
		</div>
		<div style="clear:both;"></div>

	</div>
</form>
 


<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($APPLICATION->GetCurPageParam())?>');
	var height = 0; 

	<?
		if($_REQUEST['set_filter']){
			?>
				$.cookie("acstatus", "open",{expires:14});
			<?
		}
	?>




	
	$(document).ready(function() {

		var api;

		$('#catalog-main').prepend($('#headers').html());
		$('#headers').html('');
		$('.workarea').css('min-height', $('#filter').height());

		$(".select_drop").click(function(){

				$(this).next().css('z-index', '10000');
				$(this).next().css('border-width', '1px');
				$(this).next().children('ul').children('li').css('display', 'list-item');
				if($(this).next().height()>=250){
					$(this).next().css('overflow-y', 'scroll');
		
		
		
					$(this).next().jScrollPane({
						showArrows: true
					});
					api = $(this).next().data('jsp');
					var throttleTimeout;
		
					setTimeout(function(){
						api.reinitialise();
					},800);


				}

		});
		$(".select>li label").click(function(){
			
		});
		$(".select").mouseleave(function(){
			if (api) api.destroy();
			$(this).parent().css('z-index', '1000');
			$(this).parent().css('border-width', '0px');
			$(this).children('li').css('display', 'none');
			$(this).parent().css('overflow-y', 'hidden');
		});

		$(".filtren.compare ul li .caption .arrow").click(function(){
			$(this).next().trigger( "click" );
		});

		$(".filtren.compare ul li .caption a").click(function(){
			$(this).next().toggle();
			$(this).prev().toggleClass('opened');
		});


	});


		

</script>