<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" type="text/css" href='<?=SITE_TEMPLATE_PATH?>/css/jquery.jscrollpane.css' />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jscrollpane.js"></script>
<?
$itemsCnt = count($arResult['ITEMS']);
$delUrlID = "";
?>
<div class="sort">
	<div class="sorttext"><?=GetMessage('CATALOG_CHARACTERISTICS_LABEL')?>:</div>
	<?
	if($arResult["DIFFERENT"]):
	?>
			<a class="sortbutton" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
			<a class="sortbutton current" href="javascript:void(0)"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
	<?
	else:
	?>
			<a class="sortbutton current" href="javascript:void(0)"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
			<a class="sortbutton" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
	<?
	endif;
	?>
</div>

<?
if(!empty($arResult["PROP_ROWS"]) || !empty($arResult["OFFERS_PROP_ROWS"]))
{
	?>
	<!--noindex-->
	<div class="filtren compare">
		<h5><?=GetMessage("CATALOG_COMPARE_PARAMS")?></h5>  
		<ul class="lsnn">
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
						<li><span><input type="checkbox" id="<?=$propCode?>" checked="checked" onclick="location.href='<?=CUtil::JSEscape($url)?>'"></span><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>"><?=$arResult["SHOW_PROPERTIES"][$propCode]["NAME"]?></label></a></li>
						<?
					}
					elseif(!empty($arResult["DELETED_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&pr_code=".$propCode,array("pr_code","action")));
						?>
						<li><span><input type="checkbox" id="<?=$propCode?>" onclick="location.href='<?=CUtil::JSEscape($url)?>'"></span><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>" class="unchecked"><?=$arResult["DELETED_PROPERTIES"][$propCode]["NAME"]?></label></a></li>
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
						<li><span><input type="checkbox" id="<?=$propCode?>" checked="checked" onclick="location.href='<?=CUtil::JSEscape($url)?>'"></span><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>"><?=$arResult["SHOW_OFFER_PROPERTIES"][$propCode]["NAME"]?></label></a></li>
						<?
					}
					elseif(!empty($arResult["DELETED_OFFER_PROPERTIES"][$propCode]))
					{
						$url = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&op_code=".$propCode,array("op_code","action")));
						?>
						<li><span><input type="checkbox" id="<?=$propCode?>" onclick="location.href='<?=CUtil::JSEscape($url)?>'"></span><a href="<?=$url?>" rel="nofollow"><label for="<?=$propCode?>" class="unchecked"><?=$arResult["DELETED_OFFER_PROPERTIES"][$propCode]["NAME"]?></label></a></li>
						<?
					}
				}
			}
		}
		?>
		</ul>
	</div>
	<!--/noindex-->
	<?
}

$i = 0;
?>
<div class="table_compare horizontal-only" id="contein">
	<table>
		<tr>
			<td style="width:150px;"></td>
			<?foreach($arResult["ITEMS"] as $arElement):
				$delUrlID .= "&ID[]=".$arElement["ID"];
			?>
			<td>
				<a class="deleteitem_compare" onclick="return deleteFromCompareTable(this)" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arElement['ID'],array("action", "IBLOCK_ID", "ID")))?>" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"></a>
				<?if(is_array($arElement["FIELDS"]["DETAIL_PICTURE"])):?>
					<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["FIELDS"][$code]["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["FIELDS"][$code]["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"][$code]["ALT"]?>" /></a>
				<?else:?>
					<div class="no-photo-div-small" style="width:115px; height:90px"></div>
				<?endif;?>
			</td>
			<?endforeach;?>
		</tr>
		<tr>
			<td></td>
			<?foreach($arResult["ITEMS"] as $arElement):?>
			<td>
				<a href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement['NAME']?></a>
			</td>
			<?endforeach;?>
		</tr>
<?
$i++;
foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):
	if($code == "NAME" || $code=="DETAIL_PICTURE")
		continue;
?>
		<tr>
			<td><?=GetMessage("IBLOCK_FIELD_".$code)?></td>
	<?foreach($arResult["ITEMS"] as $arElement):?>
			<td>
		<?switch($code):
			case "NAME":?>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a>
			<?break;
			default:
				echo $arElement["FIELDS"][$code];
			break;
		endswitch;?>
			</td>
	<?endforeach;?>
		</tr>
<?
$i++;
endforeach;

//Price
foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):
	if($arPrice["CAN_ACCESS"]):
?>
		<tr>
			<td><?=$arResult["PRICES"][$code]["TITLE"]?></td>
		<?foreach($arResult["ITEMS"] as $arElement):?>
			<td>
				<?if($arElement["PRICES"][$code]["CAN_ACCESS"]):
					echo $arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"];
				endif;?>
			</td>
		<?endforeach;?>
		</tr>
<?
	$i++;
	endif;
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
	if($diff || !$arResult["DIFFERENT"]):?>

		<tr>
			<td><?=$arProperty["NAME"]?></td>
		<?foreach($arResult["ITEMS"] as $arElement):
			if($diff):?>
				<td>
				<?echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);?>
				</td>

			<?else:?>
				<td>
				<?echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);?>
				</td>
			<?endif;
		endforeach;?>
			</tr>
<?
	$i++;
	endif;
endforeach;

$i++;
foreach($arResult["SHOW_OFFER_FIELDS"] as $code=>$field):
?>
			<tr<?if($i%2 == 0) echo ' class="alt"';?>>
				<td><?=GetMessage("IBLOCK_FIELD_".$code)?></td>
	<?foreach($arResult["ITEMS"] as $arElement):?>
				<td>
				<?=$arElement['OFFER_FIELDS'][$code]?>
				</td>
	<?endforeach;?>
			</tr>
<?
$i++;
endforeach;
foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty):
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
			<tr<?if($i%2 == 0) echo ' class="alt"';?>>
				<td class="compare-property"><?=$arProperty["NAME"]?></td>
		<?foreach($arResult["ITEMS"] as $arElement):
			if($diff):?>
				<td>
				<?echo (
					is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);?>
				</td>
			<?else:?>
				<td>
				<?echo (
					is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);?>
				</td><?
			endif;
		endforeach;?>
			</tr>
<?
	$i++;
	endif;
endforeach;
?>
	</table>
</div>
<?
if(strlen($delUrlID) > 0)
{
	$delUrl = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'].$delUrlID,array("action", "IBLOCK_ID", "ID")));
	?><p class="deleteAllFromCompareLink"><a href="<?=$delUrl?>"><?=GetMessage("CATALOG_DELETE_ALL")?></a></p><?
}
?>
<p><font class="notetext emptyListCompare" style="display:none"><?=GetMessage("CATALOG_COMPARE_EMPTY")?></font></p>

<script type="text/javascript" id="sourcecode">
	$(function(){
		$('#contein').each(function(){
			$(this).jScrollPane({
				showArrows: false
			});
			var api = $(this).data('jsp');
			var throttleTimeout;
			$(window).bind('resize',function(){
				if ($.browser.msie) {
					// IE fires multiple resize events while you are dragging the browser window which
					// causes it to crash if you try to update the scrollpane on every one. So we need
					// to throttle it to fire a maximum of once every 50 milliseconds...
					if (!throttleTimeout) {
						throttleTimeout = setTimeout(function(){
							api.reinitialise();
							throttleTimeout = null;
						},50);
					}
				} else {
					api.reinitialise();
				}
			});
		})
	});
	$(document).ready(function() {
		$('#contein').each(function(){
			$(this).jScrollPane({
				showArrows: true
			});
			api = $(this).data('jsp');
			var throttleTimeout;

			setTimeout(function(){
				api.reinitialise();
			},800);
			$(".deleteitem_compare").live('click', function() {
				$('.jspPane').css({"left":0});
				$('.jspDrag').css({"left":0});
				api.reinitialise();
			});
		})

	});
</script>