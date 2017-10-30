<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	function fShowStore(id, showImages, formWidth, siteId)
	{
		var strUrl = '<?=$templateFolder?>' + '/map.php';
		var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

		var storeForm = new BX.CDialog({
					'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
					head: '',
					'content_url': strUrl,
					'content_post': strUrlPost,
					'width': formWidth,
					'height':450,
					'resizable':false,
					'draggable':false
				});

		var button = [
				{
					title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
					id: 'crmOk',
					'action': function ()
					{
						GetBuyerStore();
						BX.WindowManager.Get().Close();
					}
				},
				BX.CDialog.btnCancel
			];
		storeForm.ClearButtons();
		storeForm.SetButtons(button);
		storeForm.Show();
	}

	function GetBuyerStore()
	{
		BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
		//BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
		BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
		BX.show(BX('select_store'));
	}
</script>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
<div class="bx_section">
	<?
	if(!empty($arResult["DELIVERY"]))
	{
		$width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;
		?>
		<h4><?=GetMessage("SOA_TEMPL_DELIVERY")?></h4>
		<?
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
				{
					?>
					<div class="bx_block w100 vertical">
						<div class="bx_element">

							<input
								type="radio"
								id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
								name="<?=htmlspecialcharsbx($arProfile["FIELD_NAME"])?>"
								value="<?=$delivery_id.":".$profile_id;?>"
								<?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
								onclick="submitForm();"
								/>

							<label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">

								<?
								if (count($arDelivery["LOGOTIP"]) > 0):

									$arFileTmp = CFile::ResizeImageGet(
										$arDelivery["LOGOTIP"]["ID"],
										array("width" => "95", "height" =>"55"),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);

									$deliveryImgURL = $arFileTmp["src"];
								else:
									$deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
								endif;
								?>

								<div class="bx_logotype" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;submitForm();">
									<span style='background-image:url(<?=$deliveryImgURL?>);'></span>
								</div>

								<div class="bx_description">

									<strong onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;submitForm();">
										<?=htmlspecialcharsbx($arDelivery["TITLE"])." (".htmlspecialcharsbx($arProfile["TITLE"]).")";?>
									</strong>

									<span class="bx_result_price"><!-- click on this should not cause form submit -->
										<?
										if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0):
										?>
											<strong><?=$arResult["DELIVERY_PRICE_FORMATED"]?></strong>
										<?
										else:
											$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
												"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
												"DELIVERY" => $delivery_id,
												"PROFILE" => $profile_id,
												"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
												"ORDER_PRICE" => $arResult["ORDER_PRICE"],
												"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
												"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
												"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
												"ITEMS" => $arResult["BASKET_ITEMS"]
											), null, array('HIDE_ICONS' => 'Y'));
										endif;
										?>
									</span>

									<p onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;submitForm();">
										<?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
											<?=nl2br($arProfile["DESCRIPTION"])?>
										<?else:?>
											<?=nl2br($arDelivery["DESCRIPTION"])?>
										<?endif;?>
									</p>
								</div>

							</label>

						</div>
					</div>
					<?
				} // endforeach
			}
			else // stores and courier
			{
				if (count($arDelivery["STORE"]) > 0)
					$clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."')\";";
				else
					$clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
				?>
					<div class="bx_block w100 vertical">

						<div class="bx_element">

							<input type="radio"
								id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
								name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
								value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
								onclick="submitForm();"
								/>

							<label for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>" <?=$clickHandler?>>

								<?
								if (count($arDelivery["LOGOTIP"]) > 0):

									$arFileTmp = CFile::ResizeImageGet(
										$arDelivery["LOGOTIP"]["ID"],
										array("width" => "95", "height" =>"55"),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);

									$deliveryImgURL = $arFileTmp["src"];
								else:
									$deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
								endif;
								?>

								<div class="bx_logotype"><span style='background-image:url(<?=$deliveryImgURL?>);'></span></div>

								<div class="bx_description">
									<strong>
										<div class="name"><?= htmlspecialcharsbx($arDelivery["NAME"]) ?></div>
										<span class="bx_result_price">
											<?
											if (strlen($arDelivery["PERIOD_TEXT"])>0)
											{
												echo $arDelivery["PERIOD_TEXT"];
												?><br /><?
											}
											?>
											<?=GetMessage("SALE_DELIV_PRICE");?>: <?=$arDelivery["PRICE_FORMATED"]?><br />
										</span>
									</strong>
									<p>
										<?
										if (strlen($arDelivery["DESCRIPTION"])>0)
											echo $arDelivery["DESCRIPTION"]."<br />";

										if (count($arDelivery["STORE"]) > 0):
										?>
											<span id="select_store"<?if(strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
												<span class="select_store"><?=GetMessage('SOA_ORDER_GIVE_TITLE');?>: </span>
												<span class="ora-store" id="store_desc"><?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?></span>
											</span>
										<?
									endif;
									?>
									</p>
								</div>

							</label>

						<div class="clear"></div>
					</div>
				</div>
				<?
			}
		}
		?>
		</table>
		<?
	}
?>
<div class="clear"></div>
</div>