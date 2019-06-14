<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (!empty($arResult["AUCTION"]) && $arResult["AUCTION"]["ACCESS"] >= "R"):
	if(!empty($arResult["ERROR"]))
	{
		echo "<div class=\"message error\">";
		foreach($arResult["ERROR"] as $v)
			echo $v."<br>";
		echo "</div>";
	}
	elseif(!empty($arResult["MESSAGE"]))
	{
		echo "<div class=\"message ok\">";
		foreach($arResult["MESSAGE"] as $v)
			echo $v;
		echo "</div>";
	}
	?>
	
	<div class="informer">
		<?if ($arResult["AUCTION"]["ACTIVE"] == "Y"):?>
			<?=GetMessage('CT_AUCTION_INFO');?>
		<?elseif (strlen($arResult["AUCTION"]["DATE_BEGIN"]) > 0):?>
			<?=GetMessage('A_BEGIN_TITLE')." ".$arResult["AUCTION"]["DATE_BEGIN"]?>
		<?else:?>
			<?=GetMessage('CT_AUCTION_END')." ".$arResult["USERS_VICTORY"]["USER_NAME"];?>
		<?endif;?>
	</div>
	
	<div class="auction-tabs">
    	<div class="auction-tab">
           <input type="radio" id="tab-1" name="auction-group" checked>
           <label for="tab-1"><?=GetMessage('CT_AUCTION');?></label>
           
           <div class="auction-content">
			   <table class="auction-data">
			    <?if ($arParams["AUCTION_SHOW_NAME"] === "Y"):?>
					<tr>
						<td class="auction-lot-name" colspan="2"><a href="<?=$arResult["AUCTION"]["PRODUCT"]["DETAIL_PAGE_URL"]?>"><?=$arResult["AUCTION"]["PRODUCT"]["NAME"]?></a></td>
					</tr>
				<?endif;?>
			   <tr>
					<?if (!empty($arResult["AUCTION"])):?>				
						<td class="auction-data-item">
							<?if (is_array($arResult["AUCTION"]["PRODUCT"]["RESIZE_PICTURE"])):?>
								<img src="<?=$arResult["AUCTION"]["PRODUCT"]["RESIZE_PICTURE"]["SRC"]?>" alt="">
							<?endif;?>
							
							<p><?=$arResult["AUCTION"]["PRODUCT"]["PREVIEW_TEXT"];?></p>
							
							<?if (is_array($arResult["AUCTION"]["PRICE"]) && $arParams["AUCTION_BUY_LOT"] === "Y"):?>
								<div class="auction-price-title"><?=GetMessage('SIM_AUCTION_BUY_NOW');?></div>
								<div class="auction-price"><?=$arResult["AUCTION"]["PRICE"]["PRINT_VALUE"]?></div>
								
								<form name="form_buy" action="<?=POST_FORM_ACTION_URI?>" method="POST">
									<?=bitrix_sessid_post()?>
									<input type="hidden" name="PRODUCT_ID" value="<?=$arResult["AUCTION"]["PRODUCT_ID"]?>">
									<input type="submit" name="BTN_OLD_BUY" value="<?=GetMessage('CT_BTN_BUY')?>" class="auction-btn">
								</form>
							<?endif;?>
						</td>
						
						<td class="auction-data-item">
							<table class="auction-params">
							<tr>
								<td class="auction-params-title"><?=GetMessage('CT_BETS_PRICE_BEGIN')?>:</td>
								<td><span class="auction-params-item"><?=$arResult["AUCTION"]["PRICE_BEGIN_FORMAT"]?></span></td>
							</tr>
							<tr>
								<td class="auction-params-title"><?=GetMessage('CT_BETS_FOOT')?>:</td>
								<td><span class="auction-params-item"><?=$arResult["AUCTION"]["BETS_FORMAT"]?></span></td>
							</tr>
							<tr>
								<td class="auction-params-title"><?=GetMessage('A_DATE_END')?>:</td>
								<td><span class="auction-params-item"><?=$arResult["AUCTION"]["DATE_ACTIVE_TO_FORMAT"]?></span></td>
							</tr>
							
							<tr>
								<td class="auction-params-title"><?=GetMessage('CT_BETS_LAST')?>:</td>
								<td>
								<?if (floatval($arResult["AUCTION"]["BETS_LAST"]) > 0):?>
									<span class="auction-params-item"><?=$arResult["AUCTION"]["BETS_LAST_FORMAT"]?></span>
								<?else:?>
									<div class="auction-params-item"><?=GetMessage('A_BETS_NULL');?></div>
								<?endif;?>
								</td>
							</tr>
							
							<tr>
								<td class="auction-params-title">
									<div class="js-auction-params-title">
										<?if ($arResult["AUCTION"]["ACTIVE"] != "Y" && strlen($arResult["AUCTION"]["DATE_BEGIN"]) > 0):?>
											<?=GetMessage("A_BEGIN");?>
										<?else:?>
											<?=GetMessage('CT_BETS_ALONE')?>
										<?endif;?>
									</div>
									
									<div class="js-auction-time-end auction-hide"><?=$arResult["AUCTION"]["COUNT_DOWN"]?></div>
									<div class="js-auction-time-begin auction-hide"><?=$arResult["AUCTION"]["COUNT_DOWN_BEGIN"]?></div>
									<div class="js-auction-timestamp-begin auction-hide"><?=$arResult["AUCTION"]["DATE_ACTIVE_FROM_TIMESTAMP"]?></div>
									<div class="js-auction-timestamp-end auction-hide"><?=$arResult["AUCTION"]["DATE_ACTIVE_TO_TIMESTAMP"]?></div>
									<div class="js-auction-count-bets auction-hide"><?=count($arResult["USERS_BETS"])?></div>
								</td>
								<td class="auction-time">
									<div class="js-auction-calc auction-calc"></div>
								</td>
							</tr>
							
							<?if ($arResult["AUCTION"]["ACCESS"] > "R"):?>
								<tr class="js-auction-bets" <?=($arResult["AUCTION"]["ACTIVE"] == "N")?"style='display:none;'":"";?> >
									<td><br><?=GetMessage('CT_BETS_NEXT')?>:</td>
									<td><br>
										<form name="form_bet" action="<?=POST_FORM_ACTION_URI?>" method="POST">
											<?=bitrix_sessid_post()?>
											
											<?if ($arParams["AUCTION_EDIT_PRICE"] === "Y"):?>
												<input type="text" name="USER_BETS" value="<?=$arResult["AUCTION"]["BETS_NEXT"]?>" class="auction-input">
											<?else:?>
												<div class="auction-bets-price"><?=$arResult["AUCTION"]["BETS_NEXT_FORMAT"]?></div>										
											<?endif;?>
											
											<input type="submit" name="BTN_USER" value="<?=GetMessage('CT_BETS_BTN')?>" class="auction-btn">
										</form>
									</td>
								</tr>
							<?else:?>
								<?if ($arResult["USERS_VICTORY"]["CAN_BUY"] == "Y"):?>
									<tr>
									<td colspan="2">
										<form name="form_buy" action="<?=POST_FORM_ACTION_URI?>" method="POST">
											<?=bitrix_sessid_post()?>
											
											<br><input type="submit" name="BTN_BUY" value="<?=GetMessage('CT_BTN_BUY')?>" class="auction-btn">
											<div class="auction-price victory"><?=GetMessage('CT_BTN_FROM')." ".$arResult["USERS_VICTORY"]["BETS_FORMAT"]?></div>
										</form>
									</td>
									</tr>
								<?endif;?>
							<?endif;?>
							</table>
							
							<?if ($arParams["AUCTION_CHAT"] === "Y"):?>
								<div class="auction-chat js-auction-chat">
									<div class="auction-chat-area"></div>
									<input type="hidden" name="state" value="0" class="auction-chat-state">
									
									<?if ($arResult["AUCTION"]["ACTIVE"] === "Y" && $arResult["AUCTION"]["ACCESS"] > "R"):?>
										<table class="auction-chat-write">
											<tr>
											<td>
												<input type="text" name="" value="" maxlength="250" class="auction-chat-message js-auction-chat-message">
											</td>
											<td class="auction-chat-tdsend">
												<input type="submit" name="auction-chat-send" class="auction-btn js-auction-chat-send" value="<?=GetMessage('CT_BTN_SEND_MESSAGE');?>">
											</td>
											</tr>
										</table>
									<?endif;?>
								</div>
							<?endif;?>
						</td>
					
					<?endif?>
				</tr>
				</table>
           </div> 
       </div>
        
       <div class="auction-tab">
           <input type="radio" id="tab-2" name="auction-group">
           <label for="tab-2"><?=GetMessage('CT_BETS');?></label>
           
           <div class="auction-content">
               <?if (count($arResult["USERS_BETS"]) > 0):?>
					<?foreach ($arResult["USERS_BETS"] as $item):?>
					<div class="puts">
						<img src="<?=$item["AVATAR_SRC"]?>" alt="">
						<div class="puts-name"><?=$item["USER_NAME"]?></div>
						<div class="puts-puts"><?=$item["BETS_FORMAT"]?> <?=$item["BETS_DATE"]?></div>
					</div>
					<?endforeach;?>
				<?else:?>
					<?=GetMessage("USERS_BETS_NULL");?>
				<?endif;?>
           </div> 
       </div>
	   
	   <div class="auction-clear"></div>  
    </div>
<?else:?>
	<?=GetMessage('CT_AUCTION_NO');?>
<?endif;?>