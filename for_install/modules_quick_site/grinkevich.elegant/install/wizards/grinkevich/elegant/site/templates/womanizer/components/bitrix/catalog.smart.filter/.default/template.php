<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="leftside" id="lefttextblock">
	<form id="cat-search" name="cat-search" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
		<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>"/>
		<?endforeach;?>	
		<h2><?=GetMessage("CT_BCSF_SEARCH")?></h2>

		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if($arItem["CODE"] == "MANUFACTURER" && sizeof($arItem["VALUES"]) > 1):?>
				<?
				$activeName = "";
				$activeValue = "";
				?>
				<select data-placeholder="<?=$arItem["NAME"]?>" style="width:205px;" class="chzn-select">
					<option value="" data-inpid="<?=$arItem["CODE"]?>" data-name=""></option>
					<?foreach($arItem["VALUES"] as $val => $ar):?>
						<option value="<?echo $ar["VALUE"];?>" data-inpid="<?=$arItem["CODE"]?>" data-name="<?echo $ar["CONTROL_NAME"]?>" <?echo $ar["CHECKED"]? 'selected': ''?>><?echo $ar["VALUE"];?></option>
						<?if($ar["CHECKED"]){
							$activeName = $ar["CONTROL_NAME"];
							$activeValue = $ar["HTML_VALUE"];
						}?>
					<?endforeach;?>
				</select>
				<input type="hidden" name="<?=$activeName?>" id="in<?=$arItem["CODE"]?>" value="<?=$activeValue?>" />
				<a href="/brand/" class="info"><?=GetMessage("CT_BCSF_ALF")?></a>
			<?endif;?>
		<?endforeach;?>
		
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if($arItem["CODE"] == "SIZE" && sizeof($arItem["VALUES"]) > 1 ):?>
				<?
				$activeName = "";
				$activeValue = "";
				?>
				<select data-placeholder="<?=GetMessage("CT_BCSF_SELECT")?> <?=strtolower($arItem["NAME"])?>" style="width:205px;" class="chzn-select-deselect">
					<option value="" data-inpid="<?=$arItem["CODE"]?>" data-name=""></option>
					<?foreach($arItem["VALUES"] as $val => $ar):?>
						<option value="<?echo $ar["VALUE"];?>" data-inpid="<?=$arItem["CODE"]?>" data-name="<?echo $ar["CONTROL_NAME"]?>" <?echo $ar["CHECKED"]? 'selected': ''?>><?echo $ar["VALUE"];?></option>
						<?if($ar["CHECKED"]){
							$activeName = $ar["CONTROL_NAME"];
							$activeValue = $ar["HTML_VALUE"];
						}?>
					<?endforeach;?>
				</select>
				<input type="hidden" name="<?=$activeName?>" id="in<?=$arItem["CODE"]?>" value="<?=$activeValue?>" />
				<a href="/information/table-sizes/" class="info" target="_blank"><?=GetMessage("CT_BCSF_TABLE")?></a>
			<?endif;?>
		<?endforeach;?>

		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if(isset($arItem["PRICE"])):?>
				<?
				if (empty($arItem["VALUES"]["MIN"]["VALUE"])) 
					$arItem["VALUES"]["MIN"]["VALUE"] = 0;
				if (empty($arItem["VALUES"]["MAX"]["VALUE"])) 
					$arItem["VALUES"]["MAX"]["VALUE"] = 100000;
				$arItem["VALUES"]["MAX"]["VALUE"] = number_format($arItem["VALUES"]["MAX"]["VALUE"], 0, ',', '');
				$arItem["VALUES"]["MIN"]["VALUE"] = number_format($arItem["VALUES"]["MIN"]["VALUE"], 0, ',', '');
				if( empty( $arItem["VALUES"]["MIN"]["HTML_VALUE"] ) ) 
					$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $arItem["VALUES"]["MIN"]["VALUE"];
				if( empty( $arItem["VALUES"]["MAX"]["HTML_VALUE"] ) ) 
					$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $arItem["VALUES"]["MAX"]["VALUE"];
				?>
				<div class="slide-header">
					<span class="price"><?=GetMessage("CT_BCSF_PRICE")?></span>
					<?=GetMessage("CT_BCSF_FILTER_FROM")?> <span id="price-from"><?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?></span>
					<?=GetMessage("CT_BCSF_FILTER_TO")?> <span id="price-to"><?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?></span> <?=GetMessage("CT_BCSF_CURRENCY")?>
					<input type="hidden" id="price-from-inp" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"];?>" />
					<input type="hidden" id="price-to-inp"  name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"];?>" />
				</div>
				<div id="seacrh-slider"></div>
				<?unset($arResult["ITEMS"][$key]);?>
		
				<script>
					$(document).ready(function(){
						$( "#seacrh-slider" ).slider({
							range: true,
							values: [ <?=$arItem["VALUES"]["MIN"]["HTML_VALUE"];?>, <?=$arItem["VALUES"]["MAX"]["HTML_VALUE"];?> ],
							min: <?=$arItem["VALUES"]["MIN"]["VALUE"];?>,
							max: <?=$arItem["VALUES"]["MAX"]["VALUE"];?>,			
							slide: function( event, ui ) {             
								$('#price-from').html(ui.values[ 0 ]);
								$('#price-from-inp').val(ui.values[ 0 ]);
								$('#price-to').html(ui.values[ 1 ]);
								$('#price-to-inp').val(ui.values[ 1 ]);
							},
							stop: function( event, ui ) {
								filterSearchResultCustom();
							}
						});
					});
				</script>
			<?endif;?>
		<?endforeach;?>
		
		<br /><br />
		
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if($arItem["CODE"] != "SIZE" && $arItem["CODE"] != "MANUFACTURER" && $arItem["CODE"] != "COLOR" && sizeof($arItem["VALUES"]) > 0 ):?>
				<?
				$activeName = "";
				$activeValue = "";
				?>
				<select data-placeholder="<?=GetMessage("CT_BCSF_SELECT")?> <?=strtolower($arItem["NAME"])?>" style="width:205px;" class="chzn-select-deselect">
					<option value="" data-inpid="<?=$arItem["CODE"]?>" data-name=""></option>
					<?foreach($arItem["VALUES"] as $val => $ar):?>
						<option value="<?echo $ar["HTML_VALUE"]?>" data-inpid="<?=$arItem["CODE"]?>" data-name="<?echo $ar["CONTROL_NAME"]?>" <?echo $ar["CHECKED"]? 'selected': ''?>><?echo $ar["VALUE"];?></option>
						<?if($ar["CHECKED"]){
							$activeName = $ar["CONTROL_NAME"];
							$activeValue = $ar["HTML_VALUE"];
						}?>
					<?endforeach;?>
				</select>
				<input type="hidden" name="<?=$activeName?>" id="in<?=$arItem["CODE"]?>" value="<?=$activeValue?>" />
			<?endif;?>
		<?endforeach;?>
		
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?if($arItem["CODE"] == "COLOR" && sizeof($arItem["VALUES"]) > 1 ):?>
				<div class="slide-header"><span class="price"><?=GetMessage("CT_BCSF_COLORS")?></span></div>
				<?
				$activeName = "";
				$activeValue = "";
				?>
				<div class="colors">
					<?foreach($arItem["VALUES"] as $val => $ar):?>
					<label style="background-color: <?=$ar["COLORCODE"]?>;"><input type="checkbox" name="<?echo $ar["CONTROL_NAME"]?>" value="<?echo $ar["HTML_VALUE"]?>" <?echo $ar["CHECKED"]? 'checked': ''?> /></label>
					<?endforeach;?>
				</div>
			<?endif;?>
		<?endforeach;?>
		
		
				
		<div class="filter_count" id="modef" style="display:none">
			<?=GetMessage("CT_BCSF_FILTER_COUNT")?>: <span id="div_count_elements"></span>
			<a href="#" id="div_show_result"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
			<span class="ecke"></span>
		</div>
				
		<div class="sbmt">
			<input name="set_filter" type="submit" class="grey-but" value="<?=GetMessage("CT_BCSF_SEARCH2")?>" />
		</div>
		<div class="reset-filter">
			<a href="<?=$APPLICATION->GetCurDir()?>">x <span><?=GetMessage("CT_BCSF_CLEAR_FILTER")?></span></a>
		</div>
		
		
		
		
	</form>
</div>




<script>
	var setIntervalClose;
	function filterSearchResultCustom(){
		clearTimeout(setIntervalClose);
		$("#modef").fadeOut();
		var postdatas = $("#cat-search").formSerialize() + "&set_filter=Y&ajax=y";
		$.ajax({
				url: "<?$APPLICATION->GetCurPageParam()?>",
				type: "GET",
				data: postdatas,
				cache	: false,
				success: function(data){
					var obj = $.parseJSON( data );
					$("#div_count_elements").html( obj["ELEMENT_COUNT"] );
					$("#div_show_result").attr( "href", obj["FILTER_URL"].replace(new RegExp("&amp;",'g'),"&"));
					$("#modef").fadeIn();
					setIntervalClose = setTimeout( function(){$("#modef").fadeOut();} , 2000);
				}
			});
		return false;
	}
	$(document).ready(function(){
		$("#modef").hover(function(){
			clearTimeout(setIntervalClose);
		},function(){
			setIntervalClose = setTimeout( function(){$("#modef").fadeOut();} , 2000);
		});
	});
</script>
