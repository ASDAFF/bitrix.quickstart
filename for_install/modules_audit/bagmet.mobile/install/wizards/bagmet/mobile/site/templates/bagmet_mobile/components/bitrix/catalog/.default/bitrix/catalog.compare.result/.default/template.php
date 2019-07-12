<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jScrollPane/css/jquery.jscrollpane.css');
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jScrollPane/js/jquery.jscrollpane.min.js');
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jScrollPane/js/mwheelIntent.js');
?>
<div class="catalog-section-list">
	<ul>
		<noindex>
			<?if($arResult["DIFFERENT"]):?>
				<li><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a></li>
			<?else:?>
			<li><span><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span></li>
			<?endif?>
			<?if(!$arResult["DIFFERENT"]):?>
				<li><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a></li>
			<?else:?>
				<li><span><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span></li>
			<?endif?>
		</noindex>
	</ul>
</div>
<div class="compare_options">
	<p class="compare_options_title"><?=GetMessage("CATALOG_FILTER_SETTINGS")?></p>
	<ul>
		<?
		if(!empty($arResult["PROP_ROWS"]))
		{
			foreach($arResult["PROP_ROWS"] as $arProp)
			{
				foreach($arProp as $propCode)
				{
					if(!empty($arResult["SHOW_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FEATURE&pr_code=".$propCode,array("pr_code","action")));
						?>
						<li>
							<input type="checkbox" id="<?=$propCode?>" checked="checked" onclick="location.href='<?=CUtil::JSEscape($url)?>'">
							<a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>"><?=$arResult["SHOW_PROPERTIES"][$propCode]["NAME"]?></label></a>
						</li>
					<?
					}
					elseif(!empty($arResult["DELETED_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&pr_code=".$propCode,array("pr_code","action")));
						?>
						<li>
							<input type="checkbox" id="<?=$propCode?>" onclick="location.href='<?=CUtil::JSEscape($url)?>'">
							<a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>" class="unchecked"><?=$arResult["DELETED_PROPERTIES"][$propCode]["NAME"]?></label></a>
						</li>
					<?
					}
				}
			}
		}
		?>
		<?
		if(!empty($arResult["OFFERS_PROP_ROWS"]))
		{
			foreach($arResult["OFFERS_PROP_ROWS"] as $arProp)
			{
				foreach($arProp as $propCode)
				{
					if(!empty($arResult["SHOW_OFFER_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FEATURE&op_code=".$propCode,array("op_code","action")));
						?>
						<li>
							<input type="checkbox" id="<?=$propCode?>" checked="checked" onclick="location.href='<?=CUtil::JSEscape($url)?>'">
							<a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>"><?=$arResult["SHOW_OFFER_PROPERTIES"][$propCode]["NAME"]?></label></a>
						</li>
					<?
					}
					elseif(!empty($arResult["DELETED_OFFER_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&op_code=".$propCode,array("op_code","action")));
						?>
						<li>
							<input type="checkbox" id="<?=$propCode?>" onclick="location.href='<?=CUtil::JSEscape($url)?>'">
							<a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>" class="unchecked"><?=$arResult["DELETED_OFFER_PROPERTIES"][$propCode]["NAME"]?></label></a>
						</li>
					<?
					}
				}
			}
		}
		?>
	</ul>
</div>

<div class="compare">
	<ul class="compare_table">
<!-- props -->
		<li class="compare_table_column compare_title_column">
			<ul>
			<?$i=1;?>
			<li class="compare_line_<?=$i++?>"></li>

			<?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):?>
				<?if ($code != "NAME" && $code!= "DETAIL_PICTURE"):?>
				<li class="compare_line_<?=$i++?>"><span><?=GetMessage("IBLOCK_FIELD_".$code)?></span></li>
				<?endif?>
			<?endforeach?>

			<?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):?>
				<?
				$arCompare = Array();
				foreach($arResult["ITEMS"] as $arItem)
				{
					$arPropertyValue = $arItem["DISPLAY_PROPERTIES"][$code]["VALUE"];
					if(is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				$diff = (count(array_unique($arCompare)) > 1 ? true : false);
				?>
				<?if($diff || !$arResult["DIFFERENT"]):?>
					<li class="compare_line_<?=$i++?>"><span><?=$arProperty["NAME"]?></span></li>
				<?endif?>
			<?endforeach?>

			<?foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty):?>
				<li class="compare_line_<?=$i++?>"><span><?=$arProperty["NAME"]?></span></li>
			<?endforeach?>
			</ul>
		</li>

<!-- item values-->
		<?$numItems = count($arResult["ITEMS"]);
		if ($numItems > 6) $numItems = 6; ?>
		<?foreach($arResult["ITEMS"] as $arElement):?>
			<?$i=1;?>
			<li class="compare_table_column compare_item_column compare_items_qty_<?=$numItems?>">
				<ul>
					<li class="compare_line_<?=$i++?>">
						<a href='<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arElement['ID'],array("action", "IBLOCK_ID", "ID")))?>' class="delete_from_compare_btn" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT_DESCR")?>"></a>
						<div class="compare_item_content <?if (!is_array($arElement["DETAIL_PICTURE"])) echo "compare_item_without_img"?>">
							<div class="compare_item_top_block">

								<a class="compare_item_content_a" href="<?=$arElement["DETAIL_PAGE_URL"]?>">
									<?if (is_array($arElement["DETAIL_PICTURE"])):?><img src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>"><?endif?>
								</a>

								<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class='compare_prices'>
									<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
										<?if (count($arElement["PRICES"]) > 1):?><span><?=$arResult["PRICES"][$code]["TITLE"]?></span><?endif?>
										<?if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
											<span class='compare_price'><?=$arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"]?></span>
										<?endif;?>
									<?endforeach?>
								</a>
							</div>
							<div class="compare_item_descr">
								<h4><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></h4>
							</div>
						</div>
					</li>

					<?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):?>
						<?if ($code != "NAME" && $code != "DETAIL_PICTURE" ):?>
							<?if($code == "PREVIEW_PICTURE" && is_array($arElement["FIELDS"][$code])):?>
								<li class="compare_line_<?=$i++?>"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["FIELDS"][$code]["SRC"]?>" width="<?=$arElement["FIELDS"][$code]["WIDTH"]?>" height="<?=$arElement["FIELDS"][$code]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"][$code]["ALT"]?>" /></a></li>
							<?else:?>
								<li class="compare_line_<?=$i++?>"><span><?echo $arElement["FIELDS"][$code];?></span></li>
							<?endif;?>
						<?endif?>
					<?endforeach;?>

					<?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):?>
						<?
						$arCompare = Array();
						foreach($arResult["ITEMS"] as $arItem)
						{
							$arPropertyValue = $arItem["DISPLAY_PROPERTIES"][$code]["VALUE"];
							if(is_array($arPropertyValue))
							{
								sort($arPropertyValue);
								$arPropertyValue = implode(" / ", $arPropertyValue);
							}
							$arCompare[] = $arPropertyValue;
						}
						$diff = (count(array_unique($arCompare)) > 1 ? true : false);
						?>
						<?if($diff || !$arResult["DIFFERENT"]):?>
						<li  class="compare_line_<?=$i++?>">
							<span>
							<?if($diff):?>
								<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
							<?else:?>
								<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
							<?endif?>
							</span>
						</li>
						<?endif?>
					<?endforeach?>

					<?foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty):
						$arCompare = Array();
						foreach($arResult["ITEMS"] as $arItem)
						{
							$arPropertyValue = $arItem["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
							if(is_array($arPropertyValue))
							{
								sort($arPropertyValue);
								$arPropertyValue = implode(" / ", $arPropertyValue);
							}
							$arCompare[] = $arPropertyValue;
						}
						$diff = (count(array_unique($arCompare)) > 1 ? true : false);
						if($diff || !$arResult["DIFFERENT"]):?>
							<li  class="compare_line_<?=$i++?>">
								<span>
								<?if($diff):?>
									<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
								<?else:?>
									<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
								<?endif?>
								</span>
							</li>
						<?endif?>
					<?endforeach;?>
				</ul>
			</li>
		<?endforeach?>
	</ul>
</div>

	<!--<table class="data-table" cellspacing="0" cellpadding="0" border="0">
		<thead>
		<tr>
			<td valign="top">&nbsp;</td>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<td valign="top" width="<?=round(100/count($arResult["ITEMS"]))?>%">
					<input type="checkbox" name="ID[]" value="<?=$arElement["ID"]?>" />
				</td>
			<?endforeach?>
		</tr>
		<?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):?>
		<tr>
			<th valign="top" nowrap><?=GetMessage("IBLOCK_FIELD_".$code)?></th>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<td valign="top">
					<?switch($code):
						case "NAME":
							?><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a><?
							if($arElement["CAN_BUY"]):
								?><noindex><br /><a href="<?=$arElement["BUY_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a></noindex><?
							elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):
								?><br /><?=GetMessage("CATALOG_NOT_AVAILABLE")?><?
							endif;
							break;
						case "PREVIEW_PICTURE":
						case "DETAIL_PICTURE":
							if(is_array($arElement["FIELDS"][$code])):?>
								<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["FIELDS"][$code]["SRC"]?>" width="<?=$arElement["FIELDS"][$code]["WIDTH"]?>" height="<?=$arElement["FIELDS"][$code]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"][$code]["ALT"]?>" /></a>
							<?endif;
							break;
						default:
							echo $arElement["FIELDS"][$code];
							break;
					endswitch;
					?>
				</td>
			<?endforeach?>
		</tr>
		<?endforeach;?>
		</thead>
		<?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):?>
			<?if($arPrice["CAN_ACCESS"]):?>
			<tr>
				<th valign="top" nowrap><?=$arResult["PRICES"][$code]["TITLE"]?></th>
				<?foreach($arResult["ITEMS"] as $arElement):?>
					<td valign="top">
						<?if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
							<b><?=$arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"]?></b>
						<?endif;?>
					</td>
				<?endforeach?>
			</tr>
			<?endif;?>
		<?endforeach;?>
		<?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
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
			if($diff || !$arResult["DIFFERENT"]):?>
				<tr>
					<th valign="top" nowrap>&nbsp;<?=$arProperty["NAME"]?>&nbsp;</th>
					<?foreach($arResult["ITEMS"] as $arElement):?>
						<?if($diff):?>
						<td valign="top">&nbsp;
							<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
						</td>
						<?else:?>
						<th valign="top">&nbsp;
							<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
						</th>
						<?endif?>
					<?endforeach?>
				</tr>
			<?endif?>
		<?endforeach;?>
		<?foreach($arResult["SHOW_OFFER_FIELDS"] as $code):
			$arCompare = Array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$Value = $arElement["OFFER_FIELDS"][$code];
				if(is_array($Value))
				{
					sort($Value);
					$Value = implode(" / ", $Value);
				}
				$arCompare[] = $Value;
			}
			$diff = (count(array_unique($arCompare)) > 1 ? true : false);
			if($diff || !$arResult["DIFFERENT"]):?>
				<tr>
					<th valign="top" nowrap>&nbsp;<?=GetMessage("IBLOCK_FIELD_".$code)?>&nbsp;</th>
					<?foreach($arResult["ITEMS"] as $arElement):?>
						<?if($diff):?>
						<td valign="top">&nbsp;
							<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
						</td>
						<?else:?>
						<th valign="top">&nbsp;
							<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
						</th>
						<?endif?>
					<?endforeach?>
				</tr>
			<?endif?>
		<?endforeach;?>
		<?foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty):
			$arCompare = Array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			$diff = (count(array_unique($arCompare)) > 1 ? true : false);
			if($diff || !$arResult["DIFFERENT"]):?>
				<tr>
					<th valign="top" nowrap>&nbsp;<?=$arProperty["NAME"]?>&nbsp;</th>
					<?foreach($arResult["ITEMS"] as $arElement):?>
						<?if($diff):?>
						<td valign="top">&nbsp;
							<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
						</td>
						<?else:?>
						<th valign="top">&nbsp;
							<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
						</th>
						<?endif?>
					<?endforeach?>
				</tr>
			<?endif?>
		<?endforeach;?>
	</table>-->

<?/*if(count($arResult["ITEMS_TO_ADD"])>0):?>
<p>
<form action="<?=$APPLICATION->GetCurPage()?>" method="get">
	<input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
	<input type="hidden" name="action" value="ADD_TO_COMPARE_RESULT" />
	<select name="id">
	<?foreach($arResult["ITEMS_TO_ADD"] as $ID=>$NAME):?>
		<option value="<?=$ID?>"><?=$NAME?></option>
	<?endforeach?>
	</select>
	<input type="submit" value="<?=GetMessage("CATALOG_ADD_TO_COMPARE_LIST")?>" />
</form>
</p>
<?endif*/?>


<script type="text/javascript">
	$(document).ready(function() {
		/* Compare table - lines, scroll - begin*/
		var getMaxHeight = function ($elms) {
			var maxHeight = 0;
			$elms.each(function () {
				var height = $(this).height();
				if (height > maxHeight) {
					maxHeight = height;
				}
			});
			return maxHeight;
		};

		for (var i = 1; i <= 100; i++) {
			$('.compare_line_'+i).height( getMaxHeight($('.compare_line_'+i)) );
		}

		$(function()
		{
			$('.compare').jScrollPane();
		});
	});

</script>
