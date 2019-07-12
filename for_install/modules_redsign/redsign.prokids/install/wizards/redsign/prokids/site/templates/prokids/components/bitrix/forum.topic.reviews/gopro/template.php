<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div id="detailreviews"><?

	// ERRORS
	if(!empty($arResult['ERROR_MESSAGE']))
	{
		?><div class="contentinner"><?
			?><?=ShowError($arResult['ERROR_MESSAGE']);?><?
		?></div><?
	}

	// FORM
	?><div class="contentinner"><?
		include(__DIR__."/form.php");
	?></div><?

	// MESSAGES
	if(!empty($arResult['MESSAGES']))
	{
		?><div class="reviewmessages"><?
			// NAV
			if($arResult['NAV_RESULT'] && $arResult['NAV_RESULT']->NavPageCount > 1)
			{
				?><?=$arResult['NAV_STRING']?><?
			}
			
			// REVIEW
			foreach($arResult['MESSAGES'] as $arMessage)
			{
				?><div class="message" id="message<?=$arMessage['ID']?>"><?
					?><div class="head clearfix"><?
						?><div class="name"><i class="icon pngicons"></i><?=$arMessage['AUTHOR_NAME']?></div><?
						?><div class="date"><?=$arMessage['POST_DATE_EXT']?></div><?
					?></div><?
					?><div class="contentinner"><?
						?><div class="line rating"><? // RATING
							if($arMessage['POST_MESSAGE_TEXT_EXT']['RATING']>0)
							{
								for($i=1;$i<6;$i++)
								{
									?><i class="icon pngicons<?if($i<=$arMessage['POST_MESSAGE_TEXT_EXT']['RATING']):?> active<?endif;?>"></i><?
								}
							}
						?></div><?
						if( isset($arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']) && $arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']!='' )
						{
							?><div class="line"><? // PLUS
								?><div class="part"><?=GetMessage('POST_MSG_TEXT_PLUS')?>:</div><?
								?><?=$arMessage['POST_MESSAGE_TEXT_EXT']['PLUS']?><?
							?></div><?
						}
						if( isset($arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']) && $arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']!='' )
						{
							?><div class="line"><? // MINUS
								?><div class="part"><?=GetMessage('POST_MSG_TEXT_MINUS')?>:</div><?
								?><?=$arMessage['POST_MESSAGE_TEXT_EXT']['MINUS']?><?
							?></div><?
						}
						?><div class="line"><? // COMMENT
							?><?=$arMessage['POST_MESSAGE_TEXT_EXT']['COMMENT']?><?
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
		?></div><?
	} else {
		?><?=ShowError(GetMessage('NO_MESSAGES'));?><?
		?><script>
		$('#detailreviews').find('.reviewform').removeClass('noned');
		</script><?
	}
	
?></div><?