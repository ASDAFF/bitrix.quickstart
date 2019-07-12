<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y")
	$page = "canceled";
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y")
	$page = "completed";
elseif($_REQUEST["filter_history"] == "Y")
	$page = "all";
else
	$page = "active";
?>

<div class="sort tabfilter">
	<div class="sorttext"><?=GetMessage("STPOL_F_NAME")?></div>
	<a class="sortbutton active<?if($page == "active") echo " current"?>" href="<?if($page != "active") echo $arResult["CURRENT_PAGE"]."?filter_history=N"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ACTIVE")?></a>

	<a class="sortbutton all<?if($page == "all") echo " current"?>" href="<?if($page != "all") echo $arResult["CURRENT_PAGE"]."?filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ALL")?></a>

	<a class="sortbutton completed<?if($page == "completed") echo " current"?>" href="<?if($page != "completed") echo $arResult["CURRENT_PAGE"]."?filter_status=F&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_COMPLETED")?></a>

	<a class="sortbutton canceled<?if($page == "canceled") echo " current"?>" href="<?if($page != "canceled") echo $arResult["CURRENT_PAGE"]."?filter_canceled=Y&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_CANCELED")?></a>

</div>

<?
$bNoOrder = true;
foreach($arResult["ORDERS"] as $key => $val)
{
	$bNoOrder = false;
	?>
	<table class="equipment orders<?if ($val["ORDER"]["CANCELED"] == "Y"):?> canceled<?else: echo " ".toLower($val["ORDER"]["STATUS_ID"]); endif?>"  >
		<thead>
			<tr>
			<td>
				<span><?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ID"] ?>&nbsp;<?=GetMessage("STPOL_FROM")?>&nbsp;<?=$val["ORDER"]["DATE_INSERT"]; ?></span>
			</td>
			<td class="tar fwn">
				<a title="<?echo GetMessage("STPOL_DETAIL")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>"><?echo GetMessage("STPOL_DETAIL")?></a>
			</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong><?echo GetMessage("STPOL_SUM")?></strong> <?=$val["ORDER"]["FORMATED_PRICE"]?></td>
				<td>
					<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
						<?=$val["ORDER"]["DATE_CANCEL"]?>
					<?else:?>
						<?=$val["ORDER"]["DATE_STATUS"]?>
					<?endif;?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?=GetMessage("STPOL_PAYED")?></strong> <?echo (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?>
				</td>
				<td class="order_status">
					<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
						<strong><?=GetMessage("STPOL_CANCELED");?></strong>
					<?else:?>
						<strong><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?></strong>
					<?endif;?>
				</td>
			</tr>
			<tr>
				<td  class="compositionorder">
				<?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0)
					echo "<strong>".GetMessage("P_PAY_SYS")."</strong> ".$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]."<br><br>"?>

				<?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0)
				{
					echo "<strong>".GetMessage("P_DELIVERY")."</strong> ".$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]."<br><br>";
				}
				elseif (strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false)
				{
					echo "<strong>".GetMessage("P_DELIVERY")."</strong> ";
					$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
					echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")"."<br><br>";
				}
				?>
					<strong><?echo GetMessage("STPOL_CONTENT")?></strong>
					<ul>
						<?
						foreach($val["BASKET_ITEMS"] as $vvval)
						{
							?>
							<li>
								<?
								if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
									echo "<a href=\"".$vvval["DETAIL_PAGE_URL"]."\">";
								echo $vvval["NAME"];
								if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
									echo "</a>";
								if($vvval["QUANTITY"] > 1)
									echo " &mdash; ".$vvval["QUANTITY"].GetMessage("STPOL_SHT");
								?>
							</li>
							<?
						}
						?>
					</ul>
				</td>
				<td style="padding:20px 0 20px 5px;">
					<?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
					<a class="bt2 db" style="width: 130px;" title="<?= GetMessage("STPOL_CANCEL") ?>" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?= GetMessage("STPOL_CANCEL") ?></a><br>
					<?endif;?>
					<a class="bt2 db" style="width: 130px;" title="<?= GetMessage("STPOL_REORDER") ?>" href="<?=$val["ORDER"]["URL_TO_COPY"]?>"><?= GetMessage("STPOL_REORDER1") ?></a>
				</td>
			</tr>
		</tbody>
	</table>
	<?
}

if ($bNoOrder)
{
	echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"));
}
?>


<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<div class="navigation"><?=$arResult["NAV_STRING"]?></p>
<?endif?>
