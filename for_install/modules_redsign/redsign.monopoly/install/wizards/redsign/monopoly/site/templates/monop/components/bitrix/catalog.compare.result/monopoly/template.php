<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

CJSCore::Init('ajax');

$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");

?><div class="bx_compare" id="bx_catalog_compare_block"><?
if ($isAjax)
{
	$APPLICATION->RestartBuffer();
}
?><div class="bx_sort_container">
	<span class="sorttext"><?=GetMessage("CATALOG_SHOWN_CHARACTERISTICS")?>:</span>
	<a class="btn sortbutton<? echo (!$arResult["DIFFERENT"] ? ' current btn-primary' : ' btn-default'); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N'; ?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
	<a class="btn sortbutton<? echo ($arResult["DIFFERENT"] ? ' current btn-primary' : ' btn-default'); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y'; ?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
</div>
<div class="table_compare table-responsive">
<table class="data-table table table-hover table-striped">
	<thead class="products">
		<tr>
			<td class="empty"><div></div></td>
			<?
			foreach($arResult["ITEMS"] as &$arItem)
			{
				?>
				<td class="product">
					<div class="item">
						<div class="del">
							<a onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arItem['~DELETE_URL'])?>');" href="javascript:void(0)"><i class="fa"></i></a>
						</div>
						<div class="pic">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
								<?
								if( isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src'])!='' ) {
									?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
								} else {
									?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
								}
								?>
							</a>
						</div>
						<div class="data">
							<div class="name">
								<a class="aprimary" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
							</div>
							<div class="row buy">
								<div class="col col-xs-6 prices">
									<?
									if( IntVal($arItem['RS_PRICE']['DISCOUNT_DIFF'])>0 ) {
										?><div class="price old"><?=$arItem['RS_PRICE']['PRINT_VALUE']?></div><?
										?><div class="price cool new"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
									} else {
										?><div class="price cool"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
									}
									?>
								</div>
								<div class="col col-xs-6 text-right buybtn">
									<a class="btn btn-primary" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage('RS.MONOPOLY.MORE')?></a>
								</div>
							</div>
						</div>
					</div>
				</td>
				<?
			}
			?>
		</tr>
	</thead>
<tbody>
<?
if (!empty($arResult["SHOW_FIELDS"]))
{
	foreach ($arResult["SHOW_FIELDS"] as $code => $arProp)
	{
		$showRow = true;
		if (!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as &$arItem)
			{
				$arPropertyValue = $arItem["FIELDS"][$code];
				if (is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arItem);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
			if($code=='NAME' || $code=='PREVIEW_PICTURE' || $code=='DETAIL_PICTURE')
				continue;

			?><tr><td class="propname"><?=GetMessage("IBLOCK_FIELD_".$code)?></td><?
			foreach($arResult["ITEMS"] as &$arItem)
			{
		?>
				<td valign="top">
		<?
				switch($code)
				{
					case "NAME":
						?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem[$code]?></a>
						<?if($arItem["CAN_BUY"]):?>
						<noindex><br /><a class="bx_bt_button bx_small" href="<?=$arItem["BUY_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a></noindex>
						<?elseif(!empty($arResult["PRICES"]) || is_array($arItem["PRICE_MATRIX"])):?>
						<br /><?=GetMessage("CATALOG_NOT_AVAILABLE")?>
						<?endif;
						break;
					case "PREVIEW_PICTURE":
					case "DETAIL_PICTURE":
						if(is_array($arItem["FIELDS"][$code])):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
							border="0"
							src="<?=$arItem["FIELDS"][$code]["SRC"]?>"
							width="auto"
							height="150"
							alt="<?=$arItem["FIELDS"][$code]["ALT"]?>"
							title="<?=$arItem["FIELDS"][$code]["TITLE"]?>"
							/></a>
						<?endif;
						break;
					default:
						echo $arItem["FIELDS"][$code];
						break;
				}
			?>
				</td>
			<?
			}
			unset($arItem);
		}
	?>
	</tr>
	<?
	}
}

if (!empty($arResult["SHOW_OFFER_FIELDS"]))
{
	foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as &$arItem)
			{
				$Value = $arItem["OFFER_FIELDS"][$code];
				if(is_array($Value))
				{
					sort($Value);
					$Value = implode(" / ", $Value);
				}
				$arCompare[] = $Value;
			}
			unset($arItem);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
		?>
		<tr>
			<td><?=GetMessage("IBLOCK_OFFER_FIELD_".$code)?></td>
			<?foreach($arResult["ITEMS"] as &$arItem)
			{
			?>
			<td>
				<?=(is_array($arItem["OFFER_FIELDS"][$code])? implode("/ ", $arItem["OFFER_FIELDS"][$code]): $arItem["OFFER_FIELDS"][$code])?>
			</td>
			<?
			}
			unset($arItem);
			?>
		</tr>
		<?
		}
	}
}
?>
<tr>
	<td><?=GetMessage('CATALOG_COMPARE_PRICE');?></td>
	<?
	foreach ($arResult["ITEMS"] as &$arItem)
	{
		if (isset($arItem['MIN_PRICE']) && is_array($arItem['MIN_PRICE']))
		{
			?><td><? echo $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></td><?
		}
		else
		{
			?><td>&nbsp;</td><?
		}
	}
	unset($arItem);
	?>
</tr>
<?
if (!empty($arResult["SHOW_PROPERTIES"]))
{
	foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as &$arItem)
			{
				$arPropertyValue = $arItem["DISPLAY_PROPERTIES"][$code]["VALUE"];
				if (is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arItem);
			$showRow = (count(array_unique($arCompare)) > 1);
		}

		if ($showRow)
		{
			?>
			<tr>
				<td><?=$arProperty["NAME"]?></td>
				<?foreach($arResult["ITEMS"] as &$arItem)
				{
					?>
					<td>
						<?=(is_array($arItem["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arItem["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arItem["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
					</td>
				<?
				}
				unset($arItem);
				?>
			</tr>
		<?
		}
	}
}

if (!empty($arResult["SHOW_OFFER_PROPERTIES"]))
{
	foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as &$arItem)
			{
				$arPropertyValue = $arItem["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arItem);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
		?>
		<tr>
			<td><?=$arProperty["NAME"]?></td>
			<?foreach($arResult["ITEMS"] as &$arItem)
			{
			?>
			<td>
				<?=(is_array($arItem["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arItem["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arItem["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
			</td>
			<?
			}
			unset($arItem);
			?>
		</tr>
		<?
		}
	}
}
	?>
	<tr>
		<td></td>
		<?foreach($arResult["ITEMS"] as &$arItem)
		{
		?>
		<td>
			<a class="aprimary" onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arItem['~DELETE_URL'])?>');" href="javascript:void(0)"><?=GetMessage("CATALOG_REMOVE_PRODUCT")?></a>
		</td>
		<?
		}
		unset($arItem);
		?>
	</tr>
</tbody>
</table>
</div>
<?
if ($isAjax)
{
	die();
}
?>
</div>
<script type="text/javascript">
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>