<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="col-sm-24">
	<div class="row">
		<div class="col-sm-17 col-sm-offset-1 col-md-14 col-md-offset-4 col-lg-13 col-lg-offset-5">
			<div class="wrap_order_count">
				<div class="row">
					<div class="col-sm-7">
						<div class="order_count_title"><?=GetMessage("MS_ORDER_SUMMARY_OUR_ORDER");?></div>
					</div>
					<div class="col-sm-17 sm-padding-left-no">
						<div class="block_order_count">
							<div class="row">
								<div class="col-sm-6 sm-padding-right-no">
									<p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_WEIGHT");?>:</span> <b><?=$arResult["ORDER_WEIGHT_FORMATED"]?></b></p>
								</div>
								<div class="col-sm-6 sm-padding-right-no">
									<p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_PRODUCTS_ON");?>:</span> <b><?=$arResult["ORDER_PRICE_FORMATED"]?></b></p>
								</div>
								<div class="col-sm-6 sm-padding-right-no">
									<p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_DELIVERY");?>:</span> <b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b></p>
								</div>
								<div class="col-sm-6">
									<p class="order_prop order_prop_price"><span><?=GetMessage("MS_ORDER_SUMMARY_ITOGO");?>:</span> <b><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></b></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 sm-padding-right-no">
		<div class="wrap_order_send_btn">
			<input id="basketOrderButton2" type="submit" name="BasketOrder" value="<?=GetMessage("MS_ORDER_SUMMARY_DO_ORDER");?>" onclick="submitForm('Y'); return false;">
		</div>
		</div>
	</div>
</div>