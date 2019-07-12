<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["DELIVERY"]))
{
	?>
	<div class="label_style_bl">
		<div class="clearfix"></div>
		<h2><?=GetMessage("SOA_TEMPL_DELIVERY")?></h2>
		<div class="clearfix"></div>
		<ul class="ulgray">
		<?
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
				{
					?>
					<li><label>
						<div class="gray_site_block">
						<div class="gray_site_block_top"><div></div></div>
						<div class="gray_site_block_text"><div class="gray_site_block_text_02">
						<div class="radio"><input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> onclick="submitForm();" /></div>
						<div class="text"><b><?=$arDelivery["TITLE"]?></b> - <?=$arProfile["TITLE"]?>
							<p style="font-weight:normal;margin:0px;">
							<?
							if (strlen($arPaySystem["DESCRIPTION"])>0)
							{
								?>
								<br /><?=$arPaySystem["DESCRIPTION"]?>

								<?
							}
							?>
							<?
								$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
									"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
									"DELIVERY" => $delivery_id,
									"PROFILE" => $profile_id,
									"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
									"ORDER_PRICE" => $arResult["ORDER_PRICE"],
									"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
									"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
									"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
								));
							?>
							</p>
						</div>
						<div class="clearfix"></div>
						</div></div>
						<div class="gray_site_block_bottom"><div></div></div>
						</div></label>
					</li>
					<?
				} // endforeach
			}
			else
			{
				?>
					<li><label>
						<div class="gray_site_block">
						<div class="gray_site_block_top"><div></div></div>
						<div class="gray_site_block_text"><div class="gray_site_block_text_02">
						<div class="radio"><input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?> onclick="submitForm();"></div>
						<div class="text"><b><?=$arDelivery["NAME"] ?></b>
							<p style="font-weight:normal;margin:0px;">
							<?
						if (strlen($arDelivery["PERIOD_TEXT"])>0)
						{
							echo $arDelivery["PERIOD_TEXT"];
							?><br /><?
						}

						if(DoubleVal($arDelivery["PRICE"]) > 0)
						{
							echo GetMessage("SALE_DELIV_PRICE")." ".$arDelivery["PRICE_FORMATED"]."<br />";
						}
						if (strlen($arDelivery["DESCRIPTION"])>0)
						{
							?>
							<?=$arDelivery["DESCRIPTION"]?><br />
							<?
						}
						?>
							</p>
						</div>
						<div class="clearfix"></div>
						</div></div>
						<div class="gray_site_block_bottom"><div></div></div>
						</div></label>
					</li>

				<?
			}
		}
		?>
		</ul>
	</div>
	<?
}
?>