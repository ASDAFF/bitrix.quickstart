<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pr_lc" width="1%">
	</td>
	<td width="98%">
			<div id="hide" class="hide" onClick="document.getElementById('hide').style.background = document.getElementById('hide_filter').style.display == 'none' ? 'url(\'<?=SITE_TEMPLATE_PATH?>/images/hide2.png\') 100% 0 no-repeat' : 'url(\'<?=SITE_TEMPLATE_PATH?>/images/hide.png\') 100% 0 no-repeat'; document.getElementById('a_hide').innerHTML = document.getElementById('hide_filter').style.display == 'none' ? '<?=GetMessage('IBLOCK_FILTER_HIDE')?>' : '<?=GetMessage('IBLOCK_FILTER_SHOW')?>'; return(false);">
			
			<a href="#" id="a_hide"><?=GetMessage('IBLOCK_FILTER_SHOW')?></a></div>
	</td>
	<td class="pr_rc" width="1%">
	</td>
</tr>
</table>







<div id="hide_filter" class="hide_filter" style="display: <?if ($_REQUEST['set_filter']=='Y'):?>block<?else:?>none<?endif;?>;">
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">	

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="pr_lc" width="1%"></td>
			<td width="98%">
	
				<div class="makers">
					<h3><?=GetMessage('IBLOCK_PRODUSERS_FILTER')?></h3>
					
				</div>
				<div class="block" <?if ($arResult['IS_FILTERED']):?> style="display: none;" <?endif;?>>
			
					<?foreach($arResult["ITEMS"] as $arItem): ?>
						<?if($arItem["NAME"]==GetMessage('IBLOCK_PRODUSER_FILTER')):?>
							<?=$arItem["INPUT"]?>
						<?endif?>
					<?endforeach;?>
						<div class="clear"></div>
				</div>
			</td>
			<td class="pr_rc" width="1%"></td>
		</tr>
		<tr>
			<td class="pr_lc" width="1%"></td>
			<td style="padding-left:10px; padding-bottom:10px;">
				<div class="item"></div>
			</td>
			<td class="pr_rc" width="1%"></td>
		</tr>
		<tr>
			<td class="pr_lc" width="1%"></td>
			<td  style="padding-left:10px;">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="50%" class="td_filter">
							<h3><?=GetMessage('IBLOCK_FILTER')?></h3>
							<?foreach($arResult["ITEMS"] as $arItem): ?>
									<?if(!array_key_exists("HIDDEN", $arItem) && $arItem["NAME"]!=GetMessage('IBLOCK_PRODUSER_FILTER') && $arItem["TYPE"]!="price"):?>
										<div class="item_settings">
											<label for="producers[]"><?=$arItem["NAME"]?></label>
												<div class="select">
													<?=$arItem["INPUT"]?>
												</div>
											<div class="clear"></div>
										</div>
												
									<?endif?>
							<?endforeach;?>
						</td>
						<td width="50%">
							<div class="prise">
								<h3><?=GetMessage('IBLOCK_PRICE_FILTER')?></h3>
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
							
							
							<div class="settings_filter_finish">
								<div class="left_button"><input class="in1" type="button" onClick="location.href='<?=$APPLICATION->GetCurDir()?>'" name="del_filter" value=""></div>
														
								<div class="right_button"><input class="in2" type="submit" value=""></div>
							</div>
						</td>
					
					</tr>
				</table>			
					
				
			</td>
			<td class="pr_rc" width="1%"></td>
		</tr>		
<tr>
	<td class="pr_lc" width="1%"></td>
	<td style="padding-left:10px;">
		<div class="item"></div>
	</td>
	<td class="pr_rc" width="1%"></td>
</tr>		
</table>
<input type="hidden" name="set_filter" value="Y" />
</form>
</div>

<script type="text/javascript">
			$(function () {
				$(".hide").click(function() {
					$(".hide_filter").slideToggle();
				});
			});
		</script>