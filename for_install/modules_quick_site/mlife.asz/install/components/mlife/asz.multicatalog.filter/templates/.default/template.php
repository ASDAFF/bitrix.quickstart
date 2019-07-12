<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
$showFilter = false;
if(count($arResult["PROP_VALUES"])>0){
foreach($arResult["PROP_VALUES"] as $key=>$value) {
	if(count($arResult["PROP_VALUES"][$key])>1){
		$showFilter = true;
		break;
	}
}
	?>
	<?if($showFilter){?>
	<div class="filter">
	<div class="headfilter"><h4><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_1")?></h4></div>
		<form method="get">
			<?foreach($arResult["PROP_VALUES"] as $key=>$value){
				
				if(count($value)>1){?>
				<div class="propwrap">
					<div class="propLabel">
						<?=$arResult["PROP_DATA"][$key]["NAME"]?>
					</div>
					<div class="propValues">
						<?if($arResult["PROP_DATA"][$key]['PROPERTY_TYPE']=='N' && is_array($value) && count($value)==2){?>
							<?
							$setnum = false;
							if(isset($_REQUEST["ft_".$key]) && strlen($_REQUEST["ft_".$key])>0){
								$curAr = explode(',',urldecode($_REQUEST["ft_".$key]));
								if(is_array($curAr) && count($curAr)==2) {
									$setnum = true;
								}
							}
							if(!$setnum) {
								$curAr = array();
								$curAr[] = $value[0]["PROPERTY_".$key."_VALUE"];
								$curAr[] = $value[1]["PROPERTY_".$key."_VALUE"];
							}
							?>
							<input type="hidden" id="ft_<?=$key?>" class="num_res" name="ft_<?=$key?>" value="<?=implode(',',$curAr)?>"/>
							<span class="otdo"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_6")?></span><input type="text" id="ft_<?=$key?>_min" value="<?=$curAr[0]?>" readonly="readonly"/>
							<span class="otdo"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_7")?></span><input type="text" id="ft_<?=$key?>_max" value="<?=$curAr[1]?>" readonly="readonly"/>
							<div class="clear"></div>
							<div id="slider<?=$key?>"></div>
							<script>
							<?
							if($value[1]["PROPERTY_".$key."_VALUE"]>1000){
								$step = 100;
							}elseif($value[1]["PROPERTY_".$key."_VALUE"]>500){
								$step = 100;
							}
							elseif($value[1]["PROPERTY_".$key."_VALUE"]>50){
								$step = 10;
							}
							elseif($value[1]["PROPERTY_".$key."_VALUE"]>5){
								$step = 1;
							}
							elseif($value[1]["PROPERTY_".$key."_VALUE"]>1 && $value[0]["PROPERTY_".$key."_VALUE"]<1){
								$step = 0.1;
							}else{
								$step = 1;
							}
							?>
							$("#slider<?=$key?>").slider({
								min: <?=$value[0]["PROPERTY_".$key."_VALUE"]?>,
								max: <?=$value[1]["PROPERTY_".$key."_VALUE"]?>,
								values: [<?=implode(',',$curAr)?>],
								step: <?=$step?>,
								range: true,
								animate: true,
								stop: function(event, ui) {
									$("input#ft_<?=$key?>").val($("#slider<?=$key?>").slider("values",0)+','+$("#slider<?=$key?>").slider("values",1));
									$("input#ft_<?=$key?>_min").val($("#slider<?=$key?>").slider("values",0));
									$("input#ft_<?=$key?>_max").val($("#slider<?=$key?>").slider("values",1));
									$('.submit').attr('href','<?=$APPLICATION->GetCurPageParam("set_filter=1",array("ft_".$key,'PAGEN_1','ajaxcatalog','ajaxfilter','set_filter'))?>&ft_<?=$key?>='+$('#ft_<?=$key?>').val());
									$('.submit').click();
								},
								slide: function(event, ui){
									$("input#ft_<?=$key?>").val($("#slider<?=$key?>").slider("values",0)+','+$("#slider<?=$key?>").slider("values",1));
									$("input#ft_<?=$key?>_min").val($("#slider<?=$key?>").slider("values",0));
									$("input#ft_<?=$key?>_max").val($("#slider<?=$key?>").slider("values",1));
								}
							});
							</script>
						<?}elseif($arResult["PROP_DATA"][$key]['PROPERTY_TYPE']=='L'){?>
							<?foreach($value as $key2=>$propVal){
							//if($propVal["PROPERTY_".$key."_VALUE"]=='да') $propVal["PROPERTY_".$key."_VALUE"] = 'наличие важно';
							?>
								<?if($propVal["PROPERTY_".$key."_VALUE"]){?>
								<div class="propVal">
									<?if(isset($arResult["PROP_CUR_VALUES"][$key][$key2]['CNT'])){?>
									<a class="<?if($arResult["PROP_CUR_VALUES"][$key][$key2]["ACTIVE"]=="N"){?>normal<?}else{?>active<?}?>" href="<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["LINK"]?>">
									<?=$propVal["PROPERTY_".$key."_VALUE"]?> (<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["CNT"]?>)
									</a>
									<?}else{?>
									<span><?=$propVal["PROPERTY_".$key."_VALUE"]?> (0)</span>
									<?}?>
								</div>
								<?}?>
							<?}?>
						<?}?>
					</div>
				</div>
				<?
				}
			}?>
		<a class="submit" style="display:none;" href="<?=$arResult['URL_SET']?>"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_2")?></a>
		<?if(isset($arResult['COUNT'])){?><div class="result"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_3")?>: <?=$arResult['COUNT']?></div><?}?>
		<div class="buttons">
			<a href="<?=$arResult['URL_RESET']?>" class="reset"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_4")?></a>
			<a href="<?=$arResult['URL_SET']?>" class="reset rightf"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_5")?></a>
		</div>
		</form>
	</div>
	<?}?>
	<?
}
?>