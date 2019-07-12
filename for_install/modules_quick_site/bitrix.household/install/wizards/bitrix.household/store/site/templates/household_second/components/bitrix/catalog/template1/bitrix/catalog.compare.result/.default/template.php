<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$itemsCnt = count($arResult['ITEMS']);
$delUrlID = "";
?>
<div class="comp_scroll">
<div class="compare-list-result">
	
	
<?
if(!empty($arResult["PROP_ROWS"]))
{/*
	?>
	<!--noindex-->
	<div class="compare-props">
		<table class="compare-props" cellspacing="0">
		<tbody>
			<?
			foreach($arResult["PROP_ROWS"] as $arProp)
			{
				?>
				<tr>
				<?
				foreach($arProp as $propCode)
				{
					?>
					<td>
					<?
					if(!empty($arResult["SHOW_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FEATURE&pr_code=".$propCode,array("pr_code","action")));
						?>
						<input type="checkbox" id="<?=$propCode?>" checked="checked" onclick="location.href='<?=CUtil::JSEscape($url)?>'"><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>"><?=$arResult["SHOW_PROPERTIES"][$propCode]["NAME"]?></label></a>
						<?
					}
					elseif(!empty($arResult["DELETED_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_FEATURE&pr_code=".$propCode,array("pr_code","action")));
						?>
						<input type="checkbox" id="<?=$propCode?>" onclick="location.href='<?=CUtil::JSEscape($url)?>'"><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>" class="unchecked"><?=$arResult["DELETED_PROPERTIES"][$propCode]["NAME"]?></label></a>
						<?
					}
					?>
					</td>
					<?
				}
				?>
				</tr>
				<?
			}
			?>
		</tbody>
		</table>
	</div>
	<!--/noindex-->
	<?
*/}
$i = 0;
?>
	<div class="compare-grid">
		<?if($itemsCnt > 4):?>
			<table class="compare-grid" cellspacing="0" cellpadding="0" border="0" style="width:<?=($itemsCnt*25 + 25)?>%; table-layout: fixed;">
		<?else:?>
			<table class="compare-grid" cellspacing="0" cellpadding="0" border="0">
				<col />
				<col span="<?=$itemsCnt?>"/>
		<?endif;?>
			<thead>
				<tr>
					<td class="compare_main">
						<div class="back"><a href="<?=substr($APPLICATION->GetCurDir(),0,strpos($APPLICATION->GetCurDir(),"compare"))?>"><?=GetMessage('CATALOG_BACK')?></a></div>
						<div class="kv">
							<?=str_replace("#NUM#", "<strong>".$itemsCnt."</strong>", GetMessage('CATALOG_COUNT'))?>
						</div>
						
						<div class="compare_clear">
								<?
								if($arResult["DIFFERENT"]):
								?>
										<a href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a><br/><b><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></b>
								<?
								else:
								?>
										<b><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></b><br/><a href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
								<?
								endif;
								?>
						</div>
					</td>
					<?
					foreach($arResult["ITEMS"] as $arElement):
					$section_id = $arElement["IBLOCK_SECTION_ID"];
					if(!$section_id)
					{		
						$arElement["DETAIL_PAGE_URL"] = str_replace("/".$arElement['CODE'].".php","/0/".$arElement['CODE'].".php", $arElement["DETAIL_PAGE_URL"]);		
					}	
					$delUrlID .= "&ID[]=".$arElement["ID"];
					?>
					<td class="compare_item">	
<div class="catalog-item">					
						<div class="delete"><a class="compare-delete-item" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arElement['ID'],array("action", "IBLOCK_ID", "ID")))?>" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><?=GetMessage("CATALOG_REMOVE_PRODUCT")?></a></div>
												
						<div class="image"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img  id="catalog_list_image_<?=$arElement['ID']?>" border="0" src="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["ALT"]?>" /></a></div>
						
						<h2><?=$arElement['NAME']?></h2>
						
                        <div class="rating">
								<div style="float:left;"><?=GetMessage('CATALOG_ELEMENT_RATING')?> : </div>
								<?$APPLICATION->IncludeComponent(
								"bitrix:iblock.vote",
								"star",
								Array(
									"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
									"IBLOCK_ID" => $arParams["IBLOCK_ID"],
									"ELEMENT_ID" => $arElement['ID'],
									"CACHE_TYPE" => "N",
									"CACHE_TIME" => "3600",
									"MAX_VOTE" => "5",
									"VOTE_NAMES" => array(
										0 => "1",
										1 => "2",
										2 => "3",
										3 => "4",
										4 => "5",
										5 => "",
									),
									"SET_STATUS_404" => "N"
								),
								$component
							);?>
							<span><?if ($arElement["PROPERTIES"]["rating"]["~VALUE"]!="") echo $arElement["PROPERTIES"]["rating"]["~VALUE"]; else echo "0";?></span><br/>
								<?=GetMessage('CATALOG_AVAILABLE')?> : 
									<?if ($arElement["CATALOG_QUANTITY"]>0) {?><em class="yes"><?=GetMessage('CATALOG_ELEMENT_YES')?></em><?}
									else {?><em class="no"><?=GetMessage('CATALOG_ELEMENT_NO')?></em><?}?><br/>
								<?=GetMessage('CATALOG_ELEMENT_FEEDBACK')?> : 
									<strong><?if ($arElement["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]!="") echo $arElement["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]; else echo "0";?></strong><br/>
						</div>
</div>
						
					</td>
<?
endforeach;
?>
				</tr>
			</thead>
			<tbody>
			
		
			<?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):

				if($arPrice["CAN_ACCESS"]):
			?>
							<tr class="compare_price">
								<td class="compare-property"><?=$arResult["PRICES"][$code]["TITLE"]?></td>
			<?
					foreach($arResult["ITEMS"] as $arElement):
						$arElement["PRICES"][$code]["VALUE"]=number_format($arElement["PRICES"][$code]["VALUE"], 0, '.', ',');
						$thousand=substr($arElement["PRICES"][$code]["VALUE"],0,strpos($arElement["PRICES"][$code]["VALUE"],","));
						if ($thousand!="")
							{
							$hundred=substr($arElement["PRICES"][$code]["VALUE"],strpos($arElement["PRICES"][$code]["VALUE"],",")+1,strlen($arElement["PRICES"][$code]["VALUE"]));
							}
						else
							{
							$thousand=substr($arElement["PRICES"][$code]["VALUE"],0,1);
							$hundred=substr($arElement["PRICES"][$code]["VALUE"],1);
							}
					?>
								<td>
			<?
						if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
									<p class="price"  style="margin-top: 5px;"><strong><?=$thousand?></strong><?=$hundred?>-</p>
						<?endif;?>
						
						<?if ($arElement['CAN_BUY']):?>
							<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>images/button_buy.gif" width="79px" height="19px" alt="Купить" /></a>
							<?elseif (count($arResult["PRICES"]) > 0):?>
							<span class="catalog-item-not-available"><?=GetMessage('CATALOG_NOT_AVAILABLE')?></span>
						<?endif;?>

								</td>
			<?
					endforeach;
			?>
							</tr>
			<?
				$i++;
				endif;
			endforeach;
			?>

		

<?
$i++;
foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):
	if($code == "NAME")
		continue;
?>
				<tr<?if($i%2 == 0) echo ' class="alt"';?>>
				<?if ($code!="DETAIL_PICTURE"):?>
					<td class="compare-property"><?=GetMessage("IBLOCK_FIELD_".$code)?></td>
					<?
						foreach($arResult["ITEMS"] as $arElement):
					?>
										<td>
					<?
							switch($code):
								case "NAME":
					?>
											<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a>
					<?
								break;
								case "PREVIEW_PICTURE":
								break;
								default:
									echo $arElement["FIELDS"][$code];
								break;
							endswitch;
					?>
					</td>
					<?
						endforeach;
					?>
				<?endif;?>
				</tr>
<?
$i++;
endforeach;

foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
	$arCompare = Array();
	foreach($arResult["ITEMS"] as $arElement)
	{
		$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
		if(is_array($arPropertyValue))
		{
			sort($arPropertyValue);
			$arPropertyValue = implode(" / ", $arPropertyValue);
		}
		$arCompare[] = $arPropertyValue;
	}
	$diff = (count(array_unique($arCompare)) > 1 ? true : false);
	if($diff || !$arResult["DIFFERENT"]):
?>
				<tr>
					<th><?=$arProperty["NAME"]?></th>
					<?foreach($arResult["ITEMS"] as $arElement):?>
						<th></th>
					<?endforeach;?>
				</tr>
				<tr>
					<td><?=$arProperty["NAME"]?></td>
<?
		foreach($arResult["ITEMS"] as $arElement):
			if($diff):
?>
					<td>
<?
				echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);
?>
					</td>
<?
			else:
?>
					<td>
<?
				echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);
?>
					</td>
<?
			endif;
		endforeach;
?>
				</tr>
<?
	$i++;
	endif;
endforeach;
?>
			</tbody>
		</table>
	</div>
	

</div>
<?
if(strlen($delUrlID) > 0)
{
	$delUrl = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'].$delUrlID,array("action", "IBLOCK_ID", "ID")));
	?><p><a href="<?=$delUrl?>"><?=GetMessage("CATALOG_DELETE_ALL")?></a></p><?
}
?>
</div>