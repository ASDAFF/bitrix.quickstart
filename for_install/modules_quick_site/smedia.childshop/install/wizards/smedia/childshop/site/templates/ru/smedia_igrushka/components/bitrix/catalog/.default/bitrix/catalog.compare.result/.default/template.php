<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<pre><?php //print_r($arResult)?></pre>
<?
$itemsCnt = count($arResult['ITEMS']);
$delUrlID = "";?>
<div class="sravnenie">
<?$i = 0;?>
	<table>
        <tr>
<?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):
	//if($code == "NAME")
	//	continue;?>
    <th class="compare-property"><?=GetMessage("IBLOCK_FIELD_".$code)?></th>
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
		<th style="width: 90px;"><?=$arProperty["NAME"]?></th><?
	endif;
endforeach;?>
<?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):
	if($arPrice["CAN_ACCESS"]):?>
		<th style="width: 90px;"><?=$arResult["PRICES"][$code]["TITLE"]?></th><?
	endif;
endforeach;?>
        </tr>

<?foreach($arResult["ITEMS"] as $arElement):
    $delUrlID .= "&ID[]=".$arElement["ID"];?>
		<tr>
	<?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):?>
	        <td>
	    <?switch($code):
			case "NAME":?>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a><?
			break;
			case "PREVIEW_PICTURE":
			case "DETAIL_PICTURE":
				if(is_array($arElement["FIELDS"][$code])):?>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["FIELDS"][$code]["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["FIELDS"][$code]["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["FIELDS"][$code]["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"][$code]["ALT"]?>" /></a><?
				endif;
			break;
			default:
				echo $arElement["FIELDS"][$code];
			break;
		endswitch;?>
		     </td>
    <?endforeach;?>
	<?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
	    if($diff || !$arResult["DIFFERENT"]):?>
		    <td style="width: 90px;">
			<?if($diff):
			    echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);
			else:
				echo (
					is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					: $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
				);
			endif;?>
			</td>
		<?endif;
	endforeach;?>
	<?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):
	    if($arPrice["CAN_ACCESS"]):?>
		    <td class="red" style="width: 90px;">
			<?if($arElement["PRICES"][$code]["CAN_ACCESS"]):
				echo $arElement["PRICES"][$code]["PRINT_VALUE"];
			endif;?>
			</td>
        <?endif;
	endforeach;?>
		</tr>
<?endforeach;?>
				
	</table>

<?
if(strlen($delUrlID) > 0)
{
	$delUrl = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'].$delUrlID,array("action", "IBLOCK_ID", "ID")));
	?><noindex><p><br/><a href="<?=$delUrl?>" rel="nofollow"><?=GetMessage("CATALOG_DELETE_ALL")?></a></p></noindex><?
}
?>
</div>