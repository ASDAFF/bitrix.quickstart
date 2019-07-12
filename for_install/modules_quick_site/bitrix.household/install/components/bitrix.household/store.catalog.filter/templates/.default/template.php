<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.trackbar.js"></script>



<div class="settings_filter">

		<div id="hide" class="hide" onClick="document.getElementById('catalog_item_toogle_filter').innerHTML = document.getElementById('settings_filter_main').style.display == 'none' ? '<?=GetMessage('IBLOCK_FILTER_HIDE')?>' : '<?=GetMessage('IBLOCK_FILTER_SHOW')?>'; return(false);">
	
		<h2><a href="#" id="catalog_item_toogle_filter" class="catalog_item_toogle_filter"><?=GetMessage('IBLOCK_FILTER_SHOW')?></a></h2>
		
		</div>
		
		<div class="settings_filter_main" id="settings_filter_main" style="display: <?if ($_REQUEST['set_filter']=='Y'):?>block<?else:?>none<?endif;?>;">
	
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">

	<?
	foreach($arResult["ITEMS"] as $arItem)
		if(array_key_exists("HIDDEN", $arItem))
			echo $arItem["INPUT"];
	?>

	
	

	
		<table class="settings_filter_main_table" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="producers">
                  	<h3><?=GetMessage("IBLOCK_PRODUSER_FILTER")?></h3>
							<?foreach($arResult["ITEMS"] as $arItem): ?>
								<?if($arItem["NAME"]=="Производитель"):?>
									<?=$arItem["INPUT"]?>
								<?endif?>
							<?endforeach;?>	
                   <div class="clear"></div>
				</td>
				
				<td>
					<h3><?=GetMessage("IBLOCK_FILTER")?></h3>
						<?foreach($arResult["ITEMS"] as $arItem): ?>
							<?if(!array_key_exists("HIDDEN", $arItem) && $arItem["NAME"]!="Производитель" && $arItem["TYPE"]!="price"):?>
								<div class="item_settings">
									<label for="producers[]"><?=$arItem["NAME"]?></label>
										<div class="select">
														<?=$arItem["INPUT"]?>
										</div>
									<div class="clear"></div>
								</div>
										
							<?endif?>
						<?endforeach;?>
						
					<div class="price">
					<h3><?=GetMessage("IBLOCK_PRICE_FILTER")?></h3>
						<div class="price_main">										
										<?foreach($arResult["ITEMS"] as $arItem): //echo "<pre>"; print_r($arItem); echo "</pre>";?>
											<?if(!array_key_exists("HIDDEN", $arItem) && $arItem["NAME"]!="Производитель" && $arItem["TYPE"]=="price"):
													if($arItem['MAX']==0)
														continue;?>
													<?if(count($arParams["PRICE_CODE"])>1):?>
														<div style="clear:both;margin:10px 0;"><?=$arItem['PRICE_NAME']?></div>
													<?endif?>
													<?
													  $l_b= $_GET[arrFilter_cf][$arItem['PRICE_ID']][LEFT];
													  $r_b= $_GET[arrFilter_cf][$arItem['PRICE_ID']][RIGHT];
													  echo $arItem["INPUT"];
													  ?>

													<script type="text/javascript">

														function setVal<?=$arItem['PRICE_ID']?>(l, r) {
															document.getElementById("arrFilter_cf[<?=$arItem['PRICE_ID']?>][LEFT]").value = l;
															document.getElementById("arrFilter_cf[<?=$arItem['PRICE_ID']?>][RIGHT]").value = r;
															}

														$(document).ready(function(){
															$("#track_<?=$arItem['PRICE_ID']?>").trackbar({onMove : function() {setVal<?=$arItem['PRICE_ID']?>(this.leftValue,this.rightValue)}, width: 320, leftLimit: 0, leftValue: <?if ($l_b!="") echo $l_b; else echo "0";?>, rightLimit: <?=$arItem['MAX']?>, rightValue: <?if ($r_b!="" && $r_b<$arItem['MAX']) echo $r_b; else echo $arItem['MAX'];?>, roundUp: 1});
														 });

													</script>
											  
												  <div class="trackbar">
												  	<div id="track_<?=$arItem['PRICE_ID']?>"></div>
												  </div>

											<?endif?>
										<?endforeach;?>

						</div>
					</div>
											
											
				</td>
			</tr>
		</table>
	
		<div class="settings_filter_finish">
			<div class="left_button"><input type="button" onClick="location.href='<?=$APPLICATION->GetCurDir()?>'" name="del_filter" value="" /></div>
			
			<div class="right_button"><input type="submit" name="set_filter" value=""/></div>
			
			<input type="hidden" name="set_filter" value="Y" />
		</div>
			
	
		
		
</form>
</div> <!--hide-->

		</div>  <!--settings_filter-->
		
		<script type="text/javascript">
			$(function () {
				$(".hide").click(function() {
					$(".settings_filter_main").slideToggle();
				});
			});
		</script>

