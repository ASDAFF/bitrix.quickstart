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

<div class="catalog-section-list">
	<ul class="cart_sections">
		<li class="cart_sections_title"><?=GetMessage("STPOL_F_NAME")?></li>
		<li><a class="sortbutton <?if($page == "active") echo " active"?>" href="<?if($page != "active") echo $arResult["CURRENT_PAGE"]."?filter_history=N"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ACTIVE")?></a></li>

		<li><a class="sortbutton <?if($page == "all") echo " active"?>" href="<?if($page != "all") echo $arResult["CURRENT_PAGE"]."?filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ALL")?></a></li>

		<li><a class="sortbutton <?if($page == "completed") echo " active"?>" href="<?if($page != "completed") echo $arResult["CURRENT_PAGE"]."?filter_status=F&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_COMPLETED")?></a></li>

		<li><a class="sortbutton <?if($page == "canceled") echo " active"?>" href="<?if($page != "canceled") echo $arResult["CURRENT_PAGE"]."?filter_canceled=Y&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_CANCELED")?></a></li>
	</ul>
</div>
<div class="order_wrapper">
	<?
	$bNoOrder = true;
	foreach($arResult["ORDERS"] as $key => $val)
	{
		$bNoOrder = false;
		?>
		<div class="my_orders_item">
		<!--<table class="equipment orders<?if ($val["ORDER"]["CANCELED"] == "Y"):?> canceled<?else: echo " ".toLower($val["ORDER"]["STATUS_ID"]); endif?>" style="width:726px">-->

				<h4>
					<?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ID"] ?>&nbsp;<?=GetMessage("STPOL_FROM")?>&nbsp;<?=$val["ORDER"]["DATE_INSERT"]; ?>
				</h4>
				<a class="my_orders_item_to_details" title="<?echo GetMessage("STPOL_DETAIL")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>"><?echo GetMessage("STPOL_DETAIL")?></a>

				<ul class="my_orders_item_ul">
					<li class="my_orders_item_li">
						<ul>
							<li>
								<strong><?echo GetMessage("STPOL_SUM")?></strong> <?=$val["ORDER"]["FORMATED_PRICE"]?>
							</li>

							<li>
								<strong><?=GetMessage("STPOL_PAYED")?></strong> <?echo (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?>
							</li>
							<li>
							<?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0)
								echo "<strong>".GetMessage("P_PAY_SYS")."</strong> ".$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?>
							</li>
							<li>
							<?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0)
							{
								echo "<strong>".GetMessage("P_DELIVERY")."</strong> ".$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"];
							}
							elseif (strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false)
							{
								echo "<strong>".GetMessage("P_DELIVERY")."</strong> ";
								$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
								echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")";
							}
							?>
							</li>
							<li><strong><?echo GetMessage("STPOL_CONTENT")?></strong></li>
							<li>
								<ul class="my_orders_item_sostav">
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
							</li>
						</ul>
					</li>
					<li class="my_orders_item_li">
						<div class="my_orders_item_status order_status_<?if ($val["ORDER"]["CANCELED"] == "Y"):?>canceled<?else: echo toLower($val["ORDER"]["STATUS_ID"]); endif?>">
							<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
								<?=GetMessage("STPOL_CANCELED");?>
							<?else:?>
								<?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?>
							<?endif;?>
						</div>

						<div class="my_orders_item_status_date">
							<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
								<?=$val["ORDER"]["DATE_CANCEL"]?>
							<?else:?>
								<?=$val["ORDER"]["DATE_STATUS"]?>
							<?endif;?>
						</div>
					</li>
				</ul>

				<?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
				<a class="my_orders_item_cancel_btn" title="<?= GetMessage("STPOL_CANCEL") ?>" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?= GetMessage("STPOL_CANCEL") ?></a>
				<?endif;?>
				<a class="my_orders_item_redo_btn" title="<?= GetMessage("STPOL_REORDER") ?>" href="<?=$val["ORDER"]["URL_TO_COPY"]?>"><?= GetMessage("STPOL_REORDER1") ?></a>

		</div>
		<?
	}

	if ($bNoOrder)
	{
		echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"));
	}
	?>

	<?if(strlen($arResult["NAV_STRING"]) > 0):?>
		<div class="navigation"><?=$arResult["NAV_STRING"]?></p></div>
	<?endif?>
	<div class="splitter"></div>
</div>