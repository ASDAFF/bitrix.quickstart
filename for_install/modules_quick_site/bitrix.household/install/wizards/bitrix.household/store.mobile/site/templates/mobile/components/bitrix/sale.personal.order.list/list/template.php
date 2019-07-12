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
		<select name="filter" data-theme="c" onChange="changeFilter(this.value)">
			<option value="active"<?if($page == "active") echo " selected";?>><?=GetMessage("STPOL_F_ACTIVE")?></option>
			<option value="all"<?if($page == "all") echo " selected";?>><?=GetMessage("STPOL_F_ALL")?></option>
			<option value="completed"<?if($page == "completed") echo " selected";?>><?=GetMessage("STPOL_F_COMPLETED")?></option>
			<option value="canceled"<?if($page == "canceled") echo " selected";?>><?=GetMessage("STPOL_F_CANCELED")?></option>
		</select>
		<script language="JavaScript">
		function changeFilter(val)
		{
			if(val == "active")
				url = "filter_history=N";
			else if(val == "all")
				url = "filter_history=Y";
			else if(val == "completed")
				url = "filter_status=F&filter_history=Y";
			else if(val == "canceled")
				url = "filter_canceled=Y&filter_history=Y";
			$.mobile.changePage({url:'<?=$arResult["CURRENT_PAGE"]?>', data:url, type:'GET'}, 'none', true, false);
		}
		</script>
		<?/*
		<?if($page != "active"):?><a href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=N"><?else:?><b><?endif;?><?=GetMessage("STPOL_F_ACTIVE")?><?if($page != "active"):?></a><?else:?></b><?endif;?>

		<?if($page != "all"):?><a href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=Y"><?else:?><b><?endif;?><?=GetMessage("STPOL_F_ALL")?><?if($page != "all"):?></a><?else:?></b><?endif;?>
		
		<?if($page != "completed"):?><a href="<?=$arResult["CURRENT_PAGE"]?>?filter_status=F&filter_history=Y"><?else:?><b><?endif;?><?=GetMessage("STPOL_F_COMPLETED")?><?if($page != "completed"):?></a><?else:?></b><?endif;?>
		
		<?if($page != "canceled"):?><a href="<?=$arResult["CURRENT_PAGE"]?>?filter_canceled=Y&filter_history=Y"><?else:?><b><?endif;?><?=GetMessage("STPOL_F_CANCELED")?><?if($page != "canceled"):?></a><?else:?></b><?endif;?>
		*/?>
			<ul data-role="listview" data-inset="true">
			<?
			$bNoOrder = true;
			foreach($arResult["ORDERS"] as $key => $val)
			{
				$bNoOrder = false;
				?>
					<li>
						<h3><a href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>"><?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ID"] ?>&nbsp;<?=GetMessage("STPOL_FROM")?>&nbsp;<?=$val["ORDER"]["DATE_INSERT"]; ?></a></h3>
						<p>
							<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
								<span class="order-status order-status-deny"><?=GetMessage("STPOL_CANCELED");?></span>
								<span class="order-status-date"><?=$val["ORDER"]["DATE_CANCEL"]?></span>
							<?else:?>
								<span class="order-status order-status-<?=toLower($val["ORDER"]["STATUS_ID"])?>"><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?></span>
								<span class="order-status-date"><?=$val["ORDER"]["DATE_STATUS"]?></span>
							<?endif;?>
						</p>

						<p><label><?echo GetMessage("STPOL_SUM")?></label> <span><?=$val["ORDER"]["FORMATED_PRICE"]?></span></p>
						<p><label><?=GetMessage("STPOL_PAYED")?></label> <span><?echo (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?></span></p>
						<?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0)
							echo "<p><label>".GetMessage("P_PAY_SYS")."</label> <span>".$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]."</span></p>"?>

						<?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0)
						{
							echo "<p><label>".GetMessage("P_DELIVERY")."</label> <span>".$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]."</span></p>";
						}
						elseif (strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false)
						{
							echo "<p><label>".GetMessage("P_DELIVERY")."</label> <span>";
							$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
							echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")"."</span></p>";
						}
						?>
						<p><label><?echo GetMessage("STPOL_CONTENT")?></label>
							<?
							foreach($val["BASKET_ITEMS"] as $vvval)
							{
								?>
								<br /> &mdash;
								<?
								echo $vvval["NAME"];
								if($vvval["QUANTITY"] > 1)
									echo " &mdash; ".$vvval["QUANTITY"].GetMessage("STPOL_SHT");
							}
							?></p>
						<p>
							<?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
								<a data-role="button" data-theme="c" data-inline="true" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?= GetMessage("STPOL_CANCEL") ?></a>
							<?endif;?>
						</p>
						<p>
							<a data-role="button" data-inline="true" data-theme="c" href="<?=$val["ORDER"]["URL_TO_COPY"]?>"><?= GetMessage("STPOL_REORDER1") ?></a>
						</p>
						<a href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>" data-theme="c"><?echo GetMessage("STPOL_DETAIL")?></a>
				</li>
				<?
			}
			?></ul><?

			if ($bNoOrder)
			{
				echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"));
			}
			?>
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<?=$arResult["NAV_STRING"]?>
<?endif?>
