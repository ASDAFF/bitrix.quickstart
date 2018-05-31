<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["AUCTION"]) && $arResult["ACCESS"] >= "R"):?>
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?><br>
	<?endif;?>

	<table class="auction-list">
		<?foreach($arResult["AUCTION"] as $cell=>$arAuction):?>
		<?$arElement = $arAuction["PRODUCT"];?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr>
		<?endif;?>

		<td width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%">
			<div class="auction-item">
				<?if (is_array($arElement["RESIZE_PICTURE"])):?>
					<a class="auction-lot-img" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>"></a>
				<?endif;?>
			
				<?if ($arParams["AUCTION_NAME"] === "Y"):?>
					<a class="auction-name" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><?=$arAuction["NAME"]?></a>
				<?endif;?>
				
				<?if ($arParams["AUCTION_LOT"] === "Y"):?>
					<a class="auction-lot" href="<?=$arAuction["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
				<?endif;?>				
				
				<div class="auction-begin-price_title"><?=GetMessage('A_BEGIN_PRICE');?></div>
				<div class="auction-begin-price">
					<?=$arAuction["PRICE_BEGIN_FORMAT"];?>
				</div>
					
				<?if ($arParams["AUCTION_LAST_BETS"] === "Y" && strlen($arAuction["LAST_PRICE_FORMAT"]) > 0):?>
					<div class="auction-last-bets">
						<div><?=GetMessage('A_LAST_BETS');?></div>
						<?=$arAuction["LAST_PRICE_FORMAT"];?>
					</div>
				<?endif;?>
				
				<?if ($arAuction["ACTIVE"] === "Y"):?>
					<div class="auction-timer" id="timer_<?=$arAuction["PRODUCT_ID"]?>"></div>
					<div class="timer-val" id="<?=$arAuction["PRODUCT_ID"]?>"><?=$arAuction["COUNT_DOWN"]?></div>
				<?elseif (strlen($arAuction["DATE_BEGIN"]) > 0):?>
					<div class="auction-begin"><?=GetMessage('A_BEGIN');?></div>
					<div class="auction-timer end"><?=$arAuction["DATE_BEGIN"]?></div>
				<?else:?>
					<div class="auction-timer end"><?=GetMessage('А_CONFIRM');?></div>
				<?endif;?>
						
				<a href="<?=$arAuction["DETAIL_PAGE_URL"]?>" class="auction-btn"><?=GetMessage("A_MORE")?></a>
			</div>
		</td>

		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
			</tr>
		<?endif?>

		<?endforeach;?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
			<?while(($cell++)%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
				<td>&nbsp;</td>
			<?endwhile;?>
			</tr>
		<?endif?>
	</table>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br><?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<?=GetMessage('A_LIST_NULL');?>
<?endif;?>