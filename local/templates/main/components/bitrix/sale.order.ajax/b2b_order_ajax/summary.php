<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="col-sm-24 sm-padding-no">
	<div class="summary_block">
        <div class="wrap_order_count">
            <div class="block_order_count_wrap">
                <div class="block_order_count_title">
                    <div class="order_count_title"><?=GetMessage("MS_ORDER_SUMMARY_OUR_ORDER");?></div>
                </div>
                <div class="block_order_count">
                    <div class="block_order_count__one_block">
                        <p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_COUNT");?></span></p>
                        <p class="order_prop"><span class="order_prop_value"><?=$arResult['TOTAL_QUANTITY']?></span></p>
                    </div>
                    <div class="block_order_count__one_block">
                        <p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_PRODUCTS_ON");?></span></p>
                        <p class="order_prop"><span class="order_prop_value"><?=$arResult["ORDER_PRICE_FORMATED"]?></span></p>
                    </div>
                    <!--<div class="block_order_count__one_block">
                        <p class="order_prop"><span><?/*=GetMessage("SALE_CONTENT_DISCOUNT");*/?></span></p>
                        <p class="order_prop"><span class="order_prop_value">10%</span></p>
                    </div>-->
                    <div class="block_order_count__one_block">
                        <p class="order_prop"><span><?=GetMessage("SALE_VAT");?></span></p>
                        <p class="order_prop"><span class="order_prop_value"><?=$arResult["TOTAL_VAT"]?></span></p>
                    </div>
                    <div class="block_order_count__one_block">
                        <p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_WEIGHT");?></span></p>
                        <p class="order_prop"><span class="order_prop_value"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></span></p>
                    </div>
                    <div class="block_order_count__one_block">
                        <p class="order_prop"><span><?=GetMessage("MS_ORDER_SUMMARY_DELIVERY");?></span></p>
                        <p class="order_prop"><span class="order_prop_value"><?=$arResult["DELIVERY_PRICE_FORMATED"]?></span></p>
                    </div>

                </div>
            </div>
        </div>

		<div class="summary_block__btn_block">
            <div class="">
                <p class="order_prop order_prop_price"><span><?=GetMessage("MS_ORDER_SUMMARY_ITOGO");?>:</span><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></p>
            </div>
			<div class="wrap_order_send_btn">
				<input id="basketOrderButton2" type="submit" name="BasketOrder" value="<?=GetMessage("MS_ORDER_SUMMARY_DO_ORDER");?>" onclick="submitForm('Y'); return false;">
			</div>
		</div>
	</div>
</div>