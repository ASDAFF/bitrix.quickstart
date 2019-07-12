<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y")
{
	$page = "canceled";
	$pageTitle = GetMessage("STPOL_F_NAME_CANC");
}
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y")
{
	$page = "completed";
	$pageTitle = GetMessage("STPOL_F_NAME_EX");
}
elseif($_REQUEST["filter_history"] == "Y")
{
	$page = "all";
	$pageTitle = GetMessage("STPOL_F_NAME_ALL");
}
else
{
	$page = "active";
	$pageTitle = GetMessage("STPOL_F_NAME_ACT");

}




?>


	<p>&nbsp;<br />
		<?=GetMessage("STPOL_F_NAME")?>

		<a class="sortbutton active<?if($page == "active") echo " current"?>" href="<?if($page != "active") echo $arResult["CURRENT_PAGE"]."?filter_history=N"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ACTIVE")?></a>
		|
		<a class="sortbutton all<?if($page == "all") echo " current"?>" href="<?if($page != "all") echo $arResult["CURRENT_PAGE"]."?filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ALL")?></a>
        |
		<a class="sortbutton completed<?if($page == "completed") echo " current"?>" href="<?if($page != "completed") echo $arResult["CURRENT_PAGE"]."?filter_status=F&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_COMPLETED")?></a>
		|
		<a class="sortbutton canceled<?if($page == "canceled") echo " current"?>" href="<?if($page != "canceled") echo $arResult["CURRENT_PAGE"]."?filter_canceled=Y&filter_history=Y"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_CANCELED")?></a>
        <br />&nbsp;
	</p>


	<h2><?= $pageTitle; ?></h2>
<? if (!empty($arResult["ORDERS"])) :?>


						<div class="c-table">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<th><?=GetMessage("STPOL_F_NAME_TNUM")?></th>
									<th><?=GetMessage("STPOL_F_NAME_TSTA")?></th>
									<th style="text-align:right"><?=GetMessage("STPOL_F_NAME_TDAT")?></th>
								</tr>

	<? foreach($arResult["ORDERS"] as $key => $val)
	{
		?>


		<tr>
			<td><a class="lnk-item"><?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ID"] ?></a></td>
			<td>
				<span class="type t-<?if ($val["ORDER"]["CANCELED"] == "Y" || ($val["ORDER"]["STATUS_ID"] == 'F')):?>old<? elseif ($val["ORDER"]["PAYED"]!="Y"): ?>wait<? else: ?>new<? endif; ?>">
					<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
						<?=GetMessage("STPOL_CANCELED");?>
					<?else:?>
						<?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?>
					<?endif;?>
				</span>
			</td>
			<td style="text-align:right"><?=$val["ORDER"]["DATE_INSERT_FORMAT"]; ?></td>
		</tr>
		<tr>
			<td colspan="3" class="subtd">
				<div class="z-info">



												<div class="z-form">
													<div class="zf-col">
														<div>
															<span class="lb"><?=GetMessage("STPOL_F_NAME_POLU");?></span>
															<?= $val['ORDER']['USER_NAME']; ?> <?= $val['ORDER']['USER_LAST_NAME']; ?>
														</div>
														<div>
															<span class="lb"><?= GetMessage("STPOL_SUM")?></span>
															<?=$val["ORDER"]["FORMATED_PRICE"]?>
														</div>
                                                        <div>
															<span class="lb"><?=GetMessage("STPOL_PAYED")?></span>
															<?= (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?>
														</div>


														<?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0):?>
															<div>
																<span class="lb"><?= GetMessage("P_PAY_SYS"); ?></span>
																<?= $arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]; ?>
															</div>
														<? endif; ?>


														<?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0)
														{
															echo '<div><span class="lb">'.GetMessage("P_DELIVERY")."</span> ".$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]."</div>";
															if ($val["ORDER"]["PRICE_DELIVERY"] != 0)
																echo '<div><span class="lb">'.GetMessage("STPOL_F_NAME_STOI").'</span> '._emisc::pf($val["ORDER"]["PRICE_DELIVERY"])."</div>";
														}
														elseif (strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false)
														{
															echo '<div><span class="lb">'.GetMessage("P_DELIVERY")."</span> ";
															$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
															echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")"."</div>";
															if ($val["ORDER"]["PRICE_DELIVERY"] != 0)
																echo '<div><span class="lb">'.GetMessage("STPOL_F_NAME_STOI").'</span> '._emisc::pf($val["ORDER"]["PRICE_DELIVERY"])."</div>";
														}
														?>
														<? if($val['ORDER']['USER_DESCRIPTION']): ?>
														<div>
															<span class="lb"><?=GetMessage("STPOL_F_NAME_COMM");?></span>
															<?= $val['ORDER']['USER_DESCRIPTION']; ?>
														</div>
														<? endif; ?>
													</div>


													<div class="zf-tbl">
														<table cellpadding="0" cellspacing="0" class="">
															<col width="67%" />
															<col width="15%" />
															<col width="11%" />

															<?
															foreach($val["BASKET_ITEMS"] as $vvval)
															{
																?>
																<tr>
																	<td>
																		<?
																		if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
																			echo "<a href=\"".$vvval["DETAIL_PAGE_URL"]."\">";
																		echo $vvval["NAME"];
																		if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
																			echo "</a>";
																		?>
																	</td>

                                                                    <td><strong><?= _emisc::pf($vvval["PRICE"]); ?></strong> <span class="rubl">a</span></td>
                                                                    <td><?= $vvval["QUANTITY"].GetMessage("STPOL_SHT"); ?></td>

																	<td class="red"><strong><?= _emisc::pf($vvval["PRICE"] * $vvval["QUANTITY"]); ?></strong> <span class="rubl">a</span></td>
																</tr>
																<?
															}
															?>
														</table>
														<div class="border"></div>
													</div>

													<div class="zf-total"><?= GetMessage("STPOL_F_NAME_ITOG") ?>  <strong><?=_emisc::pf($val["ORDER"]["PRICE"]);?></strong><span class="rubl">a</span></div>

													<div class="zf-butts">
														<a class="bt2 db" href="<?=$val["ORDER"]["URL_TO_DETAIL"]?>"><?= GetMessage("STPOL_F_NAME_DETA") ?></a>
														&nbsp;
														<a class="bt2 db" title="<?= GetMessage("STPOL_REORDER") ?>" href="<?=$val["ORDER"]["URL_TO_COPY"]?>"><?= GetMessage("STPOL_REORDER1") ?></a>
														<?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
															&nbsp;
															<a class="bt2 db" title="<?= GetMessage("STPOL_CANCEL") ?>" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?= GetMessage("STPOL_CANCEL") ?></a>
														<?endif;?>
													</div>

												</div>







					<div style="clear:both"></div>
				</div>
			</td>
		</tr>
		<?
	}
	?>


							</table>
							<div class="border"></div>
						</div>

<? else: ?>
	<br />
	<?= ShowNote(GetMessage("STPOL_NO_ORDERS_NEW")); ?>
<? endif; ?>



<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<div class="navigation"><?=$arResult["NAV_STRING"]?></p>
<?endif?>
