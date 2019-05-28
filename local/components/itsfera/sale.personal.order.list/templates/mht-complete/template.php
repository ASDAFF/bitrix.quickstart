<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	WP::loadScript('/js_/my_orders.js');

	foreach(array(
		'FATAL',
		'NONFATAL'
	) as $type){
		if(empty($arResult['ERRORS'][$type])){
			continue;
		}
		foreach($arResult['ERRORS'][$type] as $error){
			echo ShowError($error);
		}

		if($type == 'FATAL'){
			return;
		}
	}

?>
   <div class="my_orders_page">
      <div class="my_orders">
        <h1>мои заказы</h1>
        <div class="order_list">
		
        	<? if(!empty($arResult['ORDERS'])){ ?>
        			<? foreach($arResult['ORDER_BY_STATUS'] as $key => $group){ ?>


					<?if ($key == "F") { ?>
						
				

        				<? foreach($group as $k => $order){ ?>
        					
					        	<div class="order">
					            	<div class="order_header">
					                	<div class="order_number">Заказ №<?=$order["ORDER"]["ACCOUNT_NUMBER"]?> от <?=$order["ORDER"]["DATE_INSERT_FORMATED"];?></div>
					                    <div class="order_detail"><a href="<?=$order["ORDER"]["URL_TO_DETAIL"]?>">Подробнее о заказе</a></div>
					                </div>
					                <div class="description_block">
					                	<div class="float-right">
											<?
											if (
												!empty($order["ORDER"]["ID"]) &&
												!empty($order["ORDER"]["PAY_SYSTEM_ID"]) &&
												!empty($order["ORDER"]["PERSON_TYPE_ID"]) &&
												$order["ORDER"]["PAY_SYSTEM_ID"] == 17 &&
												$arPaySys = CSalePaySystem::GetByID($order["ORDER"]["PAY_SYSTEM_ID"],$order["ORDER"]["PERSON_TYPE_ID"])
											){
												CSalePaySystemAction::InitParamArrays($order["ORDER"], $order["ORDER"]["ID"], $arPaySys["PSA_PARAMS"]);
												?>
												
												<?
											}
											?>
											<div class="status_block">
												<div class="status">
													
													<div class="name">Заказ закрыт</div>
												</div>
												<div class="buttons">
													<!-- <a href="<?=$order["ORDER"]["URL_TO_CANCEL"]?>">отменить заказ</a> -->
													<a href="<?=$order["ORDER"]["URL_TO_COPY"]?>">повторить заказ</a>
												</div>
											</div>
										</div>
										<div class="description">
					                    	<div class="order_information">
					                        <table>
					                            <tbody><tr><td><span>сумма к оплате:</span></td><td><?=$order["ORDER"]["FORMATED_PRICE"]?></td></tr>
					                                <tr>
                                                        <td>
                                                            <span>оплачен:</span>
                                                        </td>
                                                        <td>
                                                            <?=$order["ORDER"]["PAYED"] == 'Y' ? 'да' : 'нет'?>
                                                        </td>
                                                    </tr>
													<? if(intval($order["ORDER"]["PAY_SYSTEM_ID"])){ ?>
						                                <tr><td><span>способ оплаты:</span></td><td><?=$arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?></td></tr>
													<? } ?>
			
													<? if($order['HAS_DELIVERY']){ ?>


						                                <tr><td><span>доставка:</span></td><td><?
						                                	if(intval($order["ORDER"]["DELIVERY_ID"])){
						                                		echo $arResult["INFO"]["DELIVERY"][$order["ORDER"]["DELIVERY_ID"]]["NAME"];
						                                		if (intval($order["ORDER"]["STORE_ID"])) {
							                                		echo ' ('.$arResult["INFO"]["STORES"][$order["ORDER"]["STORE_ID"]]["TITLE"].')';
							                                	}
						                                	}
						                                	elseif(strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false){
						                                		$arId = explode(":", $order["ORDER"]["DELIVERY_ID"]);
						                                		echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"].' ('.$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].')';
						                                	}
						                                ?></td></tr>
													<? } ?>
					                            </tbody>
					                        </table>
					                        </div>
					                        <div class="order_composition">
					                        	<div class="h4">Состав заказа:</div>
					                            <ul>
													<? foreach($order["BASKET_ITEMS"] as $item){ ?>
														<li>
															<? if(strlen($item["DETAIL_PAGE_URL"])){ ?>
																<a href="<?=$item["DETAIL_PAGE_URL"]?>" target="_blank">
															<? } ?>
															<?=$item['NAME']?>
															<? if(strlen($item["DETAIL_PAGE_URL"])){ ?>
																</a> 
															<? } ?>
															&nbsp;&mdash; <?=$item['QUANTITY']?> шт
														</li>
													<? } ?>
					                            </ul>	
					                        </div>
					                    </div>
					                </div>
					            </div>
        					<? } ?>
        					<? } ?>
        				<? } ?>
        		<? } ?>
        </div>
        <?MHT::showRecentlyViewed('mht2')?>
      </div>


    </div>

<? return ?>


<?if(!empty($arResult['ERRORS']['FATAL'])):?>

	<?foreach($arResult['ERRORS']['FATAL'] as $error):?>
		<?=ShowError($error)?>
	<?endforeach?>

<?else:?>

	<?if(!empty($arResult['ERRORS']['NONFATAL'])):?>

		<?foreach($arResult['ERRORS']['NONFATAL'] as $error):?>
			<?=ShowError($error)?>
		<?endforeach?>

	<?endif?>

	<div class="bx_my_order_switch">

		<?$nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]);?>

		<?if($nothing || isset($_REQUEST["filter_history"])):?>
			<a class="bx_mo_link" href="<?=$arResult["CURRENT_PAGE"]?>?show_all=Y"><?=GetMessage('SPOL_ORDERS_ALL')?></a>
		<?endif?>

		<?if($_REQUEST["filter_history"] == 'Y' || $_REQUEST["show_all"] == 'Y'):?>
			<a class="bx_mo_link" href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=N"><?=GetMessage('SPOL_CUR_ORDERS')?></a>
		<?endif?>

		<?if($nothing || $_REQUEST["filter_history"] == 'N' || $_REQUEST["show_all"] == 'Y'):?>
			<a class="bx_mo_link" href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=Y"><?=GetMessage('SPOL_ORDERS_HISTORY')?></a>
		<?endif?>

	</div>

	<?if(!empty($arResult['ORDERS'])):?>

		<?foreach($arResult["ORDER_BY_STATUS"] as $key => $group):?>

			<?foreach($group as $k => $order):?>

				<?if(!$k):?>

					<div class="bx_my_order_status_desc">

						<h2><?=GetMessage("SPOL_STATUS")?> "<?=$arResult["INFO"]["STATUS"][$key]["NAME"] ?>"</h2>
						<div class="bx_mos_desc"><?=$arResult["INFO"]["STATUS"][$key]["DESCRIPTION"] ?></div>

					</div>

				<?endif?>

				<div class="bx_my_order">
					
					<table class="bx_my_order_table">
						<thead>
							<tr>
								<td><?=GetMessage('SPOL_ORDER')?> <?=GetMessage('SPOL_NUM_SIGN')?><?=$order["ORDER"]["ACCOUNT_NUMBER"]?> <?=GetMessage('SPOL_FROM')?> <?=$order["ORDER"]["DATE_INSERT_FORMATED"];?></td>
								<td style="text-align: right;">
									<a href="<?=$order["ORDER"]["URL_TO_DETAIL"]?>"><?=GetMessage('SPOL_ORDER_DETAIL')?></a>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<strong><?=GetMessage('SPOL_PAY_SUM')?>:</strong> <?=$order["ORDER"]["FORMATED_PRICE"]?> <br />

									<strong><?=GetMessage('SPOL_PAYED')?>:</strong> <?=GetMessage('SPOL_'.($order["ORDER"]["PAYED"] == "Y" ? 'YES' : 'NO'))?> <br />

									<? // PAY SYSTEM ?>
									<?if(intval($order["ORDER"]["PAY_SYSTEM_ID"])):?>
										<strong><?=GetMessage('SPOL_PAYSYSTEM')?>:</strong> <?=$arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?> <br />
									<?endif?>

									<? // DELIVERY SYSTEM ?>
									<?if($order['HAS_DELIVERY']):?>

										<strong><?=GetMessage('SPOL_DELIVERY')?>:</strong>

										<?if(intval($order["ORDER"]["DELIVERY_ID"])):?>
										
											<?=$arResult["INFO"]["DELIVERY"][$order["ORDER"]["DELIVERY_ID"]]["NAME"]?> <br />
										
										<?elseif(strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false):?>
										
											<?$arId = explode(":", $order["ORDER"]["DELIVERY_ID"])?>
											<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]?> (<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"]?>) <br />

										<?endif?>

									<?endif?>

									<strong><?=GetMessage('SPOL_BASKET')?>:</strong>
									<ul class="bx_item_list">

										<?foreach ($order["BASKET_ITEMS"] as $item):?>

											<li>
												<?if(strlen($item["DETAIL_PAGE_URL"])):?>
													<a href="<?=$item["DETAIL_PAGE_URL"]?>" target="_blank">
												<?endif?>
													<?=$item['NAME']?>
												<?if(strlen($item["DETAIL_PAGE_URL"])):?>
													</a> 
												<?endif?>
												<nobr>&nbsp;&mdash; <?=$item['QUANTITY']?> <?=(isset($item["MEASURE_NAME"]) ? $item["MEASURE_NAME"] : GetMessage('SPOL_SHT'))?></nobr>
											</li>

										<?endforeach?>

									</ul>

								</td>
								<td>
									<?=$order["ORDER"]["DATE_STATUS_FORMATED"];?>
									<div class="bx_my_order_status <?=$arResult["INFO"]["STATUS"][$key]['COLOR']?><?/*yellow*/ /*red*/ /*green*/ /*gray*/?>"><?=$arResult["INFO"]["STATUS"][$key]["NAME"]?></div>

									<?if($order["ORDER"]["CANCELED"] != "Y"):?>
										<a href="<?=$order["ORDER"]["URL_TO_CANCEL"]?>" style="min-width:140px"class="bx_big bx_bt_button_type_2 bx_cart bx_order_action"><?=GetMessage('SPOL_CANCEL_ORDER')?></a>
									<?endif?>

									<a href="<?=$order["ORDER"]["URL_TO_COPY"]?>" style="min-width:140px"class="bx_big bx_bt_button_type_2 bx_cart bx_order_action"><?=GetMessage('SPOL_REPEAT_ORDER')?></a>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

			<?endforeach?>

		<?endforeach?>



	<?else:?>
		<?=GetMessage('SPOL_NO_ORDERS')?>
	<?endif?>

<?endif?>