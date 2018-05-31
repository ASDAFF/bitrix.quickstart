<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["AUCTION"]) && $arResult["ACCESS"] >= "R"):?>
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?><br>
	<?endif;?>

	<table class="auction-list">
		<?foreach($arResult["AUCTION"] as $cell=>$arAuction):?>
		<?$arElement = $arAuction["PRODUCT"];?>
		
		<tr>
			<?if (is_array($arElement["RESIZE_PICTURE"])):?>
			<td>
				<a class="auction-lot-img" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>"></a>
			</td>
			<?endif;?>
			
			<td>
				<?if ($arParams["AUCTION_NAME"] === "Y"):?>
					<a class="auction-name" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><?=$arAuction["NAME"]?></a>
				<?endif;?>
				
				<?if ($arParams["AUCTION_LOT"] === "Y"):?>
					<a class="auction-lot" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
				<?endif;?>
				
				<div class="auction-preview"><?=$arElement["PREVIEW_TEXT"]?></div>
				
				<?if ($arParams["AUCTION_LAST_BETS"] === "Y" && strlen($arAuction["LAST_PRICE_FORMAT"]) > 0):?>
					<div class="auction-last-bets">
						<span><?=GetMessage('A_LAST_BETS');?></span>
						<?=$arAuction["LAST_PRICE_FORMAT"];?>
					</div>
					<br>
				<?endif;?>
				
				<a href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><?=GetMessage('A_MORE');?></a>
			</td>
			<td class="auction-timer">
				<?if ($arAuction["ACTIVE"] === "Y"):?>
					<div id="timer_<?=$arAuction["PRODUCT_ID"]?>"></div>
					<div class="timer-val" id="<?=$arAuction["PRODUCT_ID"]?>"><?=$arAuction["COUNT_DOWN"]?></div>
				<?elseif (strlen($arAuction["DATE_BEGIN"]) > 0):?>
					<div class="auction-begin"><?=GetMessage('A_BEGIN');?></div>
					<div class="auction-timer end"><?=$arAuction["DATE_BEGIN"]?></div>
				<?else:?>
					<div class="auction-end"><?=GetMessage('А_CONFIRM');?></div>
				<?endif;?>
			</td>
			<td class="auction-begin-price">
				<?=$arAuction["PRICE_BEGIN_FORMAT"];?>
			</td>
		</tr>
		<?endforeach;?>
	</table>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br><?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<?=GetMessage('A_LIST_NULL');?>
<?endif;?>