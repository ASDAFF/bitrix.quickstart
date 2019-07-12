<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-top">

<?

$all=0;
foreach($arResult["ROWS"] as $arItems):
	foreach($arItems as $key => $arElement):
		if ($arElement!="") $all++;
	endforeach;
endforeach;

$count=0;
foreach($arResult["ROWS"] as $arItems):
?>
	<div class="group_item">
	<table class="grad" cellpadding="0" cellspacing="0" border="0">
		<tr>
<?
	$i=0;
	foreach($arItems as $key => $arElement):
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));
	
	$section_id = $arElement["IBLOCK_SECTION_ID"];
	if(!$section_id)
	{		
		$arElement["DETAIL_PAGE_URL"] = str_replace("/".$arElement['CODE'].".php","/0/".$arElement['CODE'].".php", $arElement["DETAIL_PAGE_URL"]);		
	}	
		if(is_array($arElement)):
			$i++; $count++;
			$bPicture = is_array($arElement["PREVIEW_IMG"]);
?>
			<td width="<?=100/$arParams['~LINE_ELEMENT_COUNT']?>%" <?if ($i==1) echo " class='first'"; else if ($i==$arParams['~LINE_ELEMENT_COUNT']) echo " class='end'";?>>
				<div class="catalog-item"  id="<?=$this->GetEditAreaId($arElement['ID']);?>">
				<div class="cart">
					<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
						<h2>
							<?if($arParams['ADD_PRODUSER_TO_TITLE']!="N"):?>
								<?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." "?>
							<?endif?>
							<?=$arElement["NAME"]?>
						</h2>
					</a>
					<div class="image">
						<div>
						<?if ($bPicture):?>
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
						<?endif;?>
							<div style="position:relative; top:-<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>px;">
								<?if ($arElement["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?}?>
								<?if ($arElement["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="drop_hit"></span><?}?>
								<?if ($arElement["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="prc"></span><?}?>
							</div>
						</div>
							<?
								if(count($arElement["PRICES"])>0): ;
									foreach($arElement["PRICES"] as $code=>$arPrice):
										if($arParams['SHOW_FRACTION_PRICE']=="Y")
											$decimal=2;
										else
											$decimal=0;
										$arPrice["VALUE"]=number_format($arPrice["VALUE"], $decimal, '.', ',');
										$thousand=substr($arPrice["VALUE"],0,strpos($arPrice["VALUE"],","));
										if ($thousand!="")
										{
											$hundred=substr($arPrice["VALUE"],strpos($arPrice["VALUE"],",")+1,strlen($arPrice["VALUE"]));
										}
										else
										{
											$thousand=substr($arPrice["VALUE"],0,1);
											$hundred=substr($arPrice["VALUE"],1);
										}
										
										$arPrice["DISCOUNT_VALUE"]=number_format($arPrice["DISCOUNT_VALUE"], $decimal, '.', ',');
										$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,strpos($arPrice["DISCOUNT_VALUE"],","));
										if ($thousand2!="")
										{
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],strpos($arPrice["DISCOUNT_VALUE"],",")+1,strlen($arPrice["DISCOUNT_VALUE"]));
										}
										else
										{
											$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,1);
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],1);
										}
										if($arPrice["CAN_ACCESS"]):
											if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):
					?>
												<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
												<?
											else:
					?>
												<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
					<?
											endif;
										endif;
									endforeach;
								endif;
					?>
					</div>
					<div class="info">
						<p><?=substr($arElement["PREVIEW_TEXT"], 0, 50)?>...</p>
						<?if($arElement["CAN_BUY"]):?>
									<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/button_buy.gif" width="79px" height="19px" alt="Купить" /></a>
						<?endif;?>
					</div>
					<div class="clear"></div>

					
					
					<div class="item-info">
						<p class="item-desc">
					<?
								/*foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):
					?>
												<small><?=$arProperty["NAME"]?>:&nbsp;<?
									if(is_array($arProperty["DISPLAY_VALUE"]))
										echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
									else
										echo $arProperty["DISPLAY_VALUE"];
					?></small><br />
					<?
								endforeach;*/
					?>
							
							<span class="item-desc-overlay"></span>
						</p>
					</div>
					
					
					
				</div>
				</div>
			</td>
<?
		endif;
?>
<?
	endforeach;
	$k=$i;
	for($j=0; $j<($arParams['~LINE_ELEMENT_COUNT']-$k); $j++)
	{ $i++;
	 ?>
		<td width="<?=100/$arParams['~LINE_ELEMENT_COUNT']?>%" <?if ($i==1) echo " class='first'"; else if ($i==$arParams['~LINE_ELEMENT_COUNT']) echo " class='end'";?>></td>
	<?}
?>
		</tr>
	</table>
	</div>
<?
endforeach;
if (count($arResult['IDS']) > 0 && CModule::IncludeModule('sale'))
{
	$arItemsInCompare = array();
	foreach ($arResult['IDS'] as $ID)
	{
		if (isset(
			$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$ID]
		))
			$arItemsInCompare[] = $ID;
	}

	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			),
		false,
		false,
		array()
	);

	$arPageItems = array();
	$arPageItemsDelay = array();
	while ($arItem = $dbBasketItems->Fetch())
	{
		if (in_array($arItem['PRODUCT_ID'], $arResult['IDS']))
		{
			if($arItem["DELAY"] == "Y")
				$arPageItemsDelay[] = $arItem['PRODUCT_ID'];
			else
				$arPageItems[] = $arItem['PRODUCT_ID'];
		}
	}
	
	if (count($arPageItems) > 0 || count($arPageItemsDelay) > 0)
	{
		echo '<script type="text/javascript">$(function(){'."\r\n";
		foreach ($arPageItems as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART")."');\r\n";
		}
		foreach ($arPageItemsDelay as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART_DELAY")."');\r\n";
		}
		echo '})</script>';
	}
	
}
?>
</div>


