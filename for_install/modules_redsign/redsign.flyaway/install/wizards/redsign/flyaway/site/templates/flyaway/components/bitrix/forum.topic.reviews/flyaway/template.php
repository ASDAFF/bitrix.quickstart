<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
	
	<div class="reviews-bar row">
		<div class="col col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<h2 class="product-content__title"><?=GetMessage('SET_DETAIL_REVIEWS')?></h2>
	  	<a class="gui-button reviews-bar__button JS-Popup" href="#review"><?=GetMessage('ADD_REVIEW')?></a>
	  </div>
	  <div class="col col-xs-12 col-sm-12 col-md-9 col-lg-9">
			<div class="form-panel">
	  		<a class="form-title JS-Popup" href="#form_reviews" title="<?=GetMessage('TITLE_FORM')?>"><?=GetMessage('TITLE_FORM')?></a>
	  	</div>
		</div>
	</div>

	<?// ERRORS
	if(!empty($arResult['ERROR_MESSAGE']))
	{
		?><div class="contentinner"><?
			?><?=ShowError($arResult['ERROR_MESSAGE']);?><?
		?></div><?
	}
	// FORM
	?><div class="row"><?
		?><div class="col col-xs-12 col-sm-12 col-md-2 col-lg-3"><?
		?></div><?
		?><div class="col col-xs-12 col-sm-12 col-md-2 col-lg-9"><?
			include(__DIR__."/form.php");
		?></div><?
	?></div><?
	// MESSAGES
	if(!empty($arResult['MESSAGES']))
	{
			// REVIEW
    $frame = $this->createFrame()->begin("");
			foreach($arResult['MESSAGES'] as $arMessage)
			{
				$datePost = explode(".",$arMessage['POST_DATE']);
				$datePost = explode(' ', $datePost[2]);
				?><div class="reviews__item" id="message<?=$arMessage['ID']?>"><?
					?><div class="row"><?
						?><div class="col col-xs-12 col-sm-4 col-md-3 col-lg-3 reviews__user"><?
							?><div class="reviews__image"><?
								?><span class="reviews__image-avatar"><img src="/bitrix/templates/flyaway/images/no-name.png" width="94px" alt=""></span><?
							?></div><?
							?><div class="reviews__info"><?
								?><span class="reviews__date"><?=$arMessage['POST_DATE_EXT']?></span><?
								?><span class="reviews__user-name"><?=$arMessage['AUTHOR_NAME']?></span><?
								?><span class="reviews__mail"><?=$arMessage['EMAIL']?></span><?
							?></div><?
						?></div><?
						?><div class="col col-xs-12 col-sm-8 col-md-9 col-lg-9 reviews__rating"><?
							?><div class = "reviews__stars stars-rating rating-<?=$arMessage['POST_MESSAGE_TEXT_EXT']['RATING'];?>">
								<? for ($i = 1; $i <= 5; $i++): ?>
									<span class = "star" data-index=<?=$i?>></span>
								<? endfor; ?>
							</div><?
							if($arMessage['POST_MESSAGE_TEXT_EXT']['RATING']>0)
							{
							?><div class="reviews__rating-item">
	                			<ul class="rating JS-RatingDecor"><? // RATING
									for($i=1;$i<6;$i++)
									{
										?> <li class="rating__item JS-RatingDecor-Item rating_message"><a class="rating__label <?if($i>$arMessage['POST_MESSAGE_TEXT_EXT']['RATING']):?> rating__label_empty<?endif;?>" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH.'/images/1x1.gif'?>" alt="" /></a></li><?
									}
								?></ul><?
							?></div><?
							}

							if( isset($arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']) && $arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']!='' )
							{
								?><div class="reviews__detail"><? // PLUS
									?><span class="reviews__detail-name"><?=GetMessage('POST_MSG_TEXT_PLUS')?></span><?
									?><span class="reviews__detail-content"><?=$arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']?></span><?
								?></div><?
							}
							if( isset($arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']) && $arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']!='' )
							{
								?><div class="reviews__detail"><? // MINUS
									?><span class="reviews__detail-name"><?=GetMessage('POST_MSG_TEXT_MINUS')?></span><?
									?><span class="reviews__detail-content"><?=$arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']?></span><?
								?></div><?
							}
							?><div class="reviews__detail detail JS-Drop"><?
	              ?><div class="reviews__detail-info js-reviews__detail-info detail-bar JS-Drop-Bar" id="link<?=$arMessage['ID']?>"><?
	                
	                /*?><div class="detail-content detail__commit JS-Drop-Content" data-id="link<?=$arMessage['ID']?>"><?
	                  ?><span class="reviews__detail-name"><?=GetMessage('POST_MSG_TEXT_COMMENT')?></span><?
	                  /*?><span class="reviews__detailon-ctent"><?=$arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT']?></span><?*/
	                /*?></div><?*/
	                ?><span class="reviews__detail-text js-reviews__detail-text"><?=$arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT']?></span><?

	                if(strlen($arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT'])> 300){
                  	$rest = substr($arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT'], 0, 300);
                  	?><span class="reviews__detail-content js-reviews__detail-content"><?=$rest;?>...</span><?
                  	?><br/><?
                  	?><span class="js-reviews__link reviews__link" data-id="link<?=$arMessage['ID']?>"><?=GetMessage('POST_LINK')?></span><?
                  	?><span class="js-reviews__link-close reviews__link-close" data-ids="link<?=$arMessage['ID']?>"><?=GetMessage('POST_LINK_CLOSE')?></span><?
                	}
                	else {
                  	?><span class="reviews__detail-content js-reviews__detail-content"><?=$arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT']?></span><?
                	}
	              ?></div><?
	              /*?><span class="reviews__more detail-label"><?
	                ?><a class="g-switcher detail-switcher JS-Drop-Switcher" href="javascript:;"><?
	                  ?><span class="detail-switcher__label detail-switcher__label_open"><?=GetMessage('FORUM_SHOW_FULL_TEXT')?></span><?
	                  ?><span class="detail-switcher__label detail-switcher__label_close"><?=GetMessage('FORUM_SHOW_NOT_FULL_TEXT')?></span><?
	                ?></a><?
	              ?></span><?*/
	            ?></div><?
						?></div><?

							// moderator panel
							if ($arResult['PANELS']['MODERATE']=='Y' || $arResult['PANELS']['DELETE']=='Y')
							{
								?><div class="line"><?
									if ($arResult['PANELS']['MODERATE']=='Y')
									{
										?><a rel="nofollow" href="<?=$arMessage['URL']['MODERATE']?>#postform"><?=GetMessage((($arMessage['APPROVED'] == 'Y') ? 'F_HIDE' : 'F_SHOW'))?></a><?
										if ($arResult['PANELS']['DELETE']=='Y') { ?> &nbsp; | &nbsp; <? }
									}
									if ($arResult['PANELS']['DELETE']=='Y')
									{
										?><a rel="nofollow" href="<?=$arMessage['URL']['DELETE']?>#postform"><?=GetMessage('F_DELETE')?></a><?
									}
								?></div><?
							}
					?></div><?
				?></div><?
			}
    $frame->end();

		?><div class="row"><?
			?><div class="col col-xs-12 col-sm-4 col-md-3 col-lg-3"></div><?
			?><div class="col col-xs-12 col-sm-8 col-md-9 col-lg-9"><?
				// NAV
				if($arResult['NAV_RESULT'] && $arResult['NAV_RESULT']->NavPageCount > 1)
				{
					?><?=$arResult['NAV_STRING']?><?
				}
			?></div><?
		?></div><?
	} else {
		?><div class="reviews__item"><div class="reviews__message"><div class="reviews__message_error"><?=ShowError(GetMessage('NO_MESSAGES'));?></div></div></div><?

		?><script>
		$('#detailreviews').find('.reviewform').removeClass('noned');
		</script><?
	}

	
