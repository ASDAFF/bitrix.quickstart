<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(!empty($arResult["ERROR"]))
{
	echo "<div class=\"message error\">";
	foreach($arResult["ERROR"] as $v)
		echo $v." ";
	echo "</div>";
}
elseif(!empty($arResult["MESSAGE"]))
{
	echo "<div class=\"message ok\">";
	foreach($arResult["MESSAGE"] as $v)
		echo $v;
	echo "</div>";
}
$arProduct = $arResult["AUCTION"]["PRODUCT"];
?>

<?if (!empty($arResult["AUCTION"]) && $arResult["AUCTION"]["ACCESS"] >= "R"):?>
<table class="auction-detail">
<?if ($arParams["AUCTION_SHOW_NAME"] === "Y"):?>
	<tr>
		<td class="auction-lot-name" colspan="2"><a href="<?=$arResult["AUCTION"]["PRODUCT"]["DETAIL_PAGE_URL"]?>"><?=$arResult["AUCTION"]["PRODUCT"]["NAME"]?></a></td>
	</tr>
<?endif;?>
<tr>
	<td class="auction-info">
    	<?if (strlen($arProduct["RESIZE_PICTURE"]["SRC"]) > 0):?>
        	<img src="<?=$arProduct["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arProduct["NAME"]?>">
        <?endif;?>
        
        <div class="auction-prev"><?=$arProduct["PREVIEW_TEXT"]?></div>
        
        <?if (is_array($arResult["AUCTION"]["PRICE"]) && $arParams["AUCTION_BUY_LOT"] === "Y"):?>
            <div class="auction-price-title"><?=GetMessage('A_AUCTION_BUY_NOW');?></div>
            <div class="auction-price"><?=$arResult["AUCTION"]["PRICE"]["PRINT_VALUE"]?></div>
            
            <form name="form_buy" action="<?=POST_FORM_ACTION_URI?>" method="POST">
                <?=bitrix_sessid_post()?>
                <input type="hidden" name="PRODUCT_ID" value="<?=$arResult["AUCTION"]["PRODUCT_ID"]?>">
                <input type="submit" name="BTN_OLD_BUY" value="<?=GetMessage('CT_BTN_BUY')?>" class="auction-btn">
            </form>
        <?endif;?>
    </td>
    <td class="auction-data">
    	<table class="auction-information">
        <tr>
        	<th><?=GetMessage('A_PROPS');?></th>
            <th class="js-auction-date-title">
			<?if (strlen($arResult["AUCTION"]["DATE_BEGIN"]) > 0):?>
				<?=GetMessage('A_BEGIN');?>
			<?else:?>
				<?=GetMessage('A_TIME');?>
			<?endif;?>
			</th>
            <th><?=GetMessage('A_BETS');?></th>
        </tr>
        <tr>
        	<td>
                <div class="auction-params-title"><?=GetMessage('CT_BETS_DATE_BEGIN')?>:</div>
                <div class="auction-params-item"><?=$arResult["AUCTION"]["DATE_ACTIVE_FROM_FORMAT"]?></div>
                <div class="auction-clear"></div>
				
				<div class="auction-params-title"><?=GetMessage('A_DATE_END')?>:</div>
                <div class="auction-params-item"><?=$arResult["AUCTION"]["DATE_ACTIVE_TO_FORMAT"]?></div>
                <div class="auction-clear"></div>
                
                <div class="auction-params-title"><?=GetMessage('CT_BETS_PRICE_BEGIN')?>:</div>
                <div class="auction-params-item"><?=$arResult["AUCTION"]["PRICE_BEGIN_FORMAT"]?></div>
                <div class="auction-clear"></div>

                <div class="auction-params-title"><?=GetMessage('CT_BETS_FOOT')?>:</div>
                <div class="auction-params-item"><?=$arResult["AUCTION"]["BETS_FORMAT"]?></div>
                <div class="auction-clear"></div>

				<div class="auction-params-title"><?=GetMessage('CT_BETS_LAST')?>:</div>
                <?if (floatval($arResult["AUCTION"]["BETS_LAST"]) > 0):?>
                    <div class="auction-params-item"><?=$arResult["AUCTION"]["BETS_LAST_FORMAT"]?></div>
				<?else:?>
					<div class="auction-params-item"><?=GetMessage('A_BETS_NULL');?></div>
                <?endif;?>
				<div class="auction-clear"></div>
				
				<?if (floatval($arResult["AUCTION"]["BETS_PROFIT"]) > 0):?>
                    <div class="auction-params-title"><?=GetMessage('CT_BETS_PROFIT')?>:</div>
                    <div class="auction-params-item"><?=$arResult["AUCTION"]["BETS_PROFIT_FORMAT"]?></div>
                    <div class="auction-clear"></div>
                <?endif;?>
            </td>
            <td>
				<div class="js-auction-time">
					<div class="js-auction-time-end auction-hide"><?=$arResult["AUCTION"]["COUNT_DOWN"]?></div>
					<div class="js-auction-time-begin auction-hide"><?=$arResult["AUCTION"]["COUNT_DOWN_BEGIN"]?></div>
					<div class="js-auction-timestamp-begin auction-hide"><?=$arResult["AUCTION"]["DATE_ACTIVE_FROM_TIMESTAMP"]?></div>
					<div class="js-auction-timestamp-end auction-hide"><?=$arResult["AUCTION"]["DATE_ACTIVE_TO_TIMESTAMP"]?></div>
					<div class="js-auction-count-bets auction-hide"><?=count($arResult["USERS_BETS"])?></div>
					
					<div class="js-auction-calc auction-calc"></div>
				</div>                
                
                <?if ($arResult["AUCTION"]["ACCESS"] > "R"):?>
                    <div class="js-auction-bets auction-bets" <?=($arResult["AUCTION"]["ACTIVE"] == "N")?"style='display:none;'":"";?> >
                        <form name="form_bet" action="<?=POST_FORM_ACTION_URI?>" method="POST">
                            <?=bitrix_sessid_post()?>
                            
                            <?if ($arParams["AUCTION_EDIT_PRICE"] === "Y"):?>
                                <input type="text" name="USER_BETS" value="<?=$arResult["AUCTION"]["BETS_NEXT"]?>" class="auction-input">
                            <?else:?>
                                <div class="auction-bets-price"><?=$arResult["AUCTION"]["BETS_NEXT_FORMAT"]?></div>
                            <?endif;?>
							
                            <input type="submit" name="BTN_USER" value="<?=GetMessage('CT_BETS_BTN')?>" class="auction-btn">
                        </form>
                    </div>
				<?endif;?>

				<?if ($arResult["USERS_VICTORY"]["CAN_BUY"] == "Y" && $arResult["AUCTION"]["ACTIVE"] == "N"):?>
					<form name="form_buy" action="<?=POST_FORM_ACTION_URI?>" method="POST">
						<?=bitrix_sessid_post()?>
						
						<br><input type="submit" name="BTN_BUY" value="<?=GetMessage('CT_BTN_BUY')?>" class="auction-btn">
						<div class="auction-price victory"><?=GetMessage('CT_BTN_FROM')." ".$arResult["USERS_VICTORY"]["BETS_FORMAT"]?></div>
					</form>
				<?endif;?>

            </td>
            <td>
            	<?if (!empty($arResult["USERS_BETS"])):?>
                	<?foreach($arResult["USERS_BETS"] as $bets):?>
                     <div class="auction-bets-item"><?=$bets["BETS_FORMAT"]." ".$bets["USER_NAME"]." <span>[".$bets["BETS_DATE"]?>]</span></div>
                    <?endforeach;?>
				<?else:?>
					<div class="auction-bets-item"><?=GetMessage('A_BETS_NULL')?></div>
                <?endif;?>
            </td>
        </tr>
        <?if ($arParams["AUCTION_CHAT"] === "Y"):?>
        <tr>
        	<td colspan="3">
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
            </td>
        </tr>
        <?endif;?>
        </table>
    </td>
</tr>
</table>
<?else:?>
	<?=GetMessage('CT_AUCTION_NO');?>
<?endif;?>