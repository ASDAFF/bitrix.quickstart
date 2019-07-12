<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//echo'<pre>';print_r($arResult);echo'</pre>';
global $APPLICATION;
if(count($arResult["PROP_VALUES"])>0){
	?>
	<script>
		$(document).ready(function(){
			$(".optionsFilter").styler({
			'selectVisibleOptions':7,
			'selectSearchLimit':5,
			});
			setTimeout(function(){
				$('.catalogFilter').css({'height':$('.catalogFilter .filter').height()+'px'});
			},500);
		});
		
	</script>
	<div class="filter">
	<div class="headfilter"><h4><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_1")?></h4></div>
		<form method="get">
			<?foreach($arResult["PROP_VALUES"] as $key=>$value){
				$vParams[$key] = array();
				if($param = $arParams["D_PROP_PARAM_".$key]){
					$visualParams = explode(",",$param);
					foreach($visualParams as $val){
						if(strpos($val,"=") !== false){
							$temp = explode("=",$val);
							$vParams[$key][$temp[0]] = $temp[1];
						}
					}
				}
				$vParams[$key]["MODE"] = ($arParams["D_PROP_".$key]) ? $arParams["D_PROP_".$key] : "MODE1";
				//echo'<pre>';print_r($vParams[$key]);echo'</pre>';
				?>
				
				<div class="propwrap">
					<div class="propLabel">
						<?if($vParams[$key]['name']){?><?=$vParams[$key]['name']?><?}else{?><?=$arResult["PROP_DATA"][$key]["NAME"]?><?}?>
					</div>
					<div class="propValues">
						<?if($arResult["PROP_DATA"][$key]['PROPERTY_TYPE']=='N' && is_array($value) && count($value)==2){?>
							<?
							$min = $arResult["PROP_VALUES"][$key][0]["PROPERTY_".$key."_VALUE"];
							$max = $arResult["PROP_VALUES"][$key][1]["PROPERTY_".$key."_VALUE"];
							if(!$min) $min = 0;
							if(!$max) $max = 0;
							if($kef = $vParams[$key]['round']){
								$kef = round((100000/$kef),0);
								$min = round(($min*$kef),0);
								$min = $min/$kef;
								$max = round(($max*$kef),0);
								$max = $max/$kef;
							}
							?>
							<?
							$url = $arResult["PROPN_CUR_URL"][$key];
							$keyCode = mb_strtolower($arResult["PROP_DATA"][$key]["CODE"]);
							if(strpos($url,$keyCode."-") !== false) {
								$reg = "/(.*?)(\/?(filter_)".$keyCode."-)(.*?)\/(.*?)/is";
								$url = preg_replace($reg,"$1$2#JS".$key."#/$5",$url);
								$reg = "/^(.*?)(".$keyCode."-)(.*?)\/(.*?)$/is";
								$url = preg_replace($reg,"$1$2#JS".$key."#/$4",$url);
							}else{
								$add = "filter_";
								if(substr($url,0,7) == "filter_") $add = "";
								$url .= $add.$keyCode."-#JS".$key."#/";
							}
							?>
							<?
							if(!$arResult["PROP_VALUES_MIN"][$key] || $arResult["PROP_VALUES_MIN"][$key]<$min) $arResult["PROP_VALUES_MIN"][$key] = $min;
							if(!$arResult["PROP_VALUES_MAX"][$key] || $arResult["PROP_VALUES_MAX"][$key]>$max) $arResult["PROP_VALUES_MAX"][$key] = $max;
							$curAr = array($arResult["PROP_VALUES_MIN"][$key],$arResult["PROP_VALUES_MAX"][$key]);
							
							?>
							<input type="hidden" id="ft_<?=$key?>" class="num_res" name="ft_<?=$key?>" value="<?=implode(',',$curAr)?>"/>
							<span class="otdo"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_6")?></span><input type="text" class="otdo" id="ft_<?=$key?>_min" value="<?=$curAr[0]?>" readonly="readonly"/>
							<span class="otdo"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_7")?></span><input type="text" class="otdo" id="ft_<?=$key?>_max" value="<?=$curAr[1]?>" readonly="readonly"/>
							<div class="clear"></div>
							<div id="slider<?=$key?>"></div>
							<script>
							$(document).ready(function(){
							$("#slider<?=$key?>").slider({
								min: <?=$min?>,
								max: <?=$max?>,
								values: [<?=implode(',',$curAr)?>],
								step: <?echo ($vParams[$key]['step']) ? intval($vParams[$key]['step']) : 1;?>,
								range: true,
								animate: true,
								stop: function(event, ui) {
									$("input#ft_<?=$key?>").val(ui.values[0]+','+ui.values[1]);
									$("input#ft_<?=$key?>_min").val(ui.values[0]);
									$("input#ft_<?=$key?>_max").val(ui.values[1]);
									var url = "<?=$arResult["CURPAGE"]?><?=$url?>";
									var urlval = $('#ft_<?=$key?>').val();
									$(".filter .submit").attr("href",url.replace(/#JS<?=$key?>#/i, ui.values[0]+','+ui.values[1]));
									$('.filter .submit').click();
								},
								slide: function(event, ui){
									$("input#ft_<?=$key?>").val(ui.values[0]+','+ui.values[1]);
									$("input#ft_<?=$key?>_min").val(ui.values[0]);
									$("input#ft_<?=$key?>_max").val(ui.values[1]);
								}
							});
							});
							</script>
						<?}elseif($arResult["PROP_DATA"][$key]['PROPERTY_TYPE']=='L'){?>
							<?if($vParams[$key]['MODE']=="MODE4"){?>
							<?foreach($value as $key2=>$propVal){?>
								<?if($propVal["PROPERTY_".$key."_VALUE"]){?>
								<div class="propVal">
									<?if(isset($arResult["PROP_CUR_VALUES"][$key][$key2]['CNT'])){?>
									<a class="<?if($arResult["PROP_CUR_VALUES"][$key][$key2]["ACTIVE"]=="N"){?>normal<?}else{?>active<?}?>" href="<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["LINKSEF"]?>">
									<?=$propVal["PROPERTY_".$key."_VALUE"]?> (<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["CNT"]?>)
									</a>
									<?}else{?>
									<span><?=$propVal["PROPERTY_".$key."_VALUE"]?> (0)</span>
									<?}?>
								</div>
								<?}?>
							<?}?>
							<?}elseif($vParams[$key]['MODE']=="MODE5"){?>
							<select class="optionsFilter" id="selfilter<?=$key?>" onchange="window.setFilter('selfilter<?=$key?>');">
							<?$activeLink = $arResult['URL_SET']?>
							<?foreach($value as $key2=>$propVal){?>
								<?if($propVal["PROPERTY_".$key."_VALUE"]){?>
									<?if(isset($arResult["PROP_CUR_VALUES"][$key][$key2]['CNT'])){?>
									<?
									$sel = "";
									if($arResult["PROP_CUR_VALUES"][$key][$key2]["ACTIVE"]=="Y") {
										$sel = ' selected="selected"';
										$activeLink = $arResult["PROP_CUR_VALUES"][$key][$key2]["LINKSEF"];
									}
									?>
									<option value="<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["LINKSEF"]?>"<?=$sel?>>
									<?=$propVal["PROPERTY_".$key."_VALUE"]?> (<?=$arResult["PROP_CUR_VALUES"][$key][$key2]["CNT"]?>)
									</option>
									<?}else{?>
									<option value="" disabled="disabled"><?=$propVal["PROPERTY_".$key."_VALUE"]?> (0)</option>
									<?}?>
								<?}?>
							<?}?>
							<option value="<?=$activeLink?>"<?if($activeLink==$arResult['URL_SET']){?> selected="selected"<?}?>>Показать все</option>
							</select>
							<?}?>
						<?}?>
					</div>
				</div>
				
				<?
			}?>
		<a class="submit" style="display:none;" href="<?=$arResult['URL_SET']?>"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_2")?></a>
		<?if(isset($arResult['COUNT'])){?><div class="result">Отобрано товаров: <?=$arResult['COUNT']?></div><?}?>
		<div class="buttons">
			<a href="<?=$arResult['URL_RESET']?>" class="reset"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_4")?></a>
			<a href="<?=$arResult['URL_SET']?>" class="reset rightf"><?=GetMessage("MLIFE_ASZ_CATALOG_FILTER_T_5")?></a>
		</div>
		</form>
	</div>
	
	<?
}
?>