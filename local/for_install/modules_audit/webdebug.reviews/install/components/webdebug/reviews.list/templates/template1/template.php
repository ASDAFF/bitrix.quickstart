<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<h2><?=GetMessage("WEBDEBUG_REVIEWS_HEADER")?><?=$arResult["ELEMENT_NAME"]?></h2>
<br/>

<?if(is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])):?>
	<?if($arParams["DISPLAY_TOP_PAGER"]=="Y"):?><?=$arResult["NAV_STRING"];?><br/><?endif?>
	<div id="webdebug-reviews-list">
		<?foreach ($arResult["ITEMS"] as $arItem):?>
			<div class="webdebug-reviews-item">
				<a name="review_<?=$arItem["ID"]?>"></a>
				<div class="webdebug-reviews-item-votes">
					<?if(in_array("VOTE_0",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_0"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_0"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_1",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_1"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_1"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_2",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_2"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_2"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_3",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_3"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_3"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_4",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_4"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_4"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_5",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_5"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_5"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_6",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_6"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_6"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_7",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_7"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_7"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_8",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_8"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_8"])?>"></span></span></div><?endif?>
					<?if(in_array("VOTE_9",$arParams["DISPLAY_FIELDS"])):?><div class="webdebug-reviews-item-vote"><?=$arResult["VOTE_NAME_9"]?>: <span class="webdebug-reviews-item-stars"><span class="webdebug-reviews-item-star-<?=IntVal($arItem["VOTE_9"])?>"></span></span></div><?endif?>
				</div>
				<div class="webdebug-reviews-item-text">
					<div class="webdebug-reviews-item-header">
						<?if(in_array("NAME",$arParams["DISPLAY_FIELDS"])):?>
							<span class="webdebug-reviews-item-author-name">
								<?if($arParams["EMAIL_PUBLIC"]!="N" && $arItem["EMAIL_PUBLIC"]=="Y" && trim($arItem["EMAIL"])!="" && check_email($arItem["EMAIL"])):?><a href="mailto:<?=trim($arItem["EMAIL"]);?>"><?endif?><?=$arItem["NAME"]?><?if($arParams["EMAIL_PUBLIC"]!="N" && $arItem["EMAIL_PUBLIC"]=="Y" && trim($arItem["EMAIL"])!="" && check_email($arItem["EMAIL"])):?></a><?endif?>
							</span>
						<?endif?>
						<?if(in_array("DATETIME",$arParams["DISPLAY_FIELDS"]) && $arItem["DATETIME"]):?><span class="webdebug-reviews-item-date">(<?=$arItem["DATETIME"]?>)</span><?endif?>
						<?if(in_array("WWW",$arParams["DISPLAY_FIELDS"]) && $arItem["WWW"]):?><!--noindex--><span class="webdebug-reviews-item-www"><a href="<?=$arItem["URL"]?>" target="_blank" rel="nofollow"><?=$arItem["WWW"]?></a></span><!--/noindex--><?endif?>
					</div>
					<?if(in_array("TEXT_PLUS",$arParams["DISPLAY_FIELDS"]) && trim($arItem["TEXT_PLUS"])!=""):?><div class="webdebug-reviews-item-text-plus"><div class="h"><?=GetMessage("WEBDEBUG_REVIEWS_TEXT_PLUS")?></div><?=$arItem["TEXT_PLUS"]?></div><?endif?>
					<?if(in_array("TEXT_MINUS",$arParams["DISPLAY_FIELDS"]) && trim($arItem["TEXT_MINUS"])!=""):?><div class="webdebug-reviews-item-text-minus"><div class="h"><?=GetMessage("WEBDEBUG_REVIEWS_TEXT_MINUS")?></div><?=$arItem["TEXT_MINUS"]?></div><?endif?>
					<?if(in_array("TEXT_COMMENTS",$arParams["DISPLAY_FIELDS"]) && trim($arItem["TEXT_COMMENTS"])!=""):?><div class="webdebug-reviews-item-text-comments"><div class="h"><?=GetMessage("WEBDEBUG_REVIEWS_TEXT_COMMENTS")?></div><?=$arItem["TEXT_COMMENTS"]?></div><?endif?>
				</div>
				<div class="webdebug-reviews-item-clear"></div>
			</div>
		<?endforeach?>
	</div>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]=="Y"):?><br/><?=$arResult["NAV_STRING"];?><?endif?>
<?else:?>
	<div><?=sprintf(GetMessage("WEBDEBUG_REVIEWS_NO_ITEMS"), $arResult["ELEMENT_NAME"])?></div>
	<br/><br/>
<?endif?>