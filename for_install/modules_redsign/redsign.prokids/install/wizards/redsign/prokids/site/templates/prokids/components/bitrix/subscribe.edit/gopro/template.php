<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult['MESSAGE'] as $itemID => $itemValue)
{
	ShowMessage( array('MESSAGE'=>$itemValue,'TYPE'=>'OK') );
}

foreach($arResult['ERROR'] as $itemID => $itemValue)
{
	ShowError( $itemValue );
}

?><div class="subscribe"><?
	
	if($arResult['ALLOW_ANONYMOUS']=='N' && !$USER->IsAuthorized())
	{
		ShowError( GetMessage('CT_BSE_AUTH_ERR') );
	} else {
		?><form action="<?=$arResult['FORM_ACTION']?>" method="post"><?
			
			?><?=bitrix_sessid_post();?><?
			?><input type="hidden" name="PostAction" value="<?=( $arResult['ID']>0 ? 'Update' : 'Add' )?>" /><?
			?><input type="hidden" name="ID" value="<?=$arResult['SUBSCRIPTION']['ID'];?>" /><?
			?><input type="hidden" name="RUB_ID[]" value="0" /><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="EMAIL" value="<?=($arResult['SUBSCRIPTION']['EMAIL']!=''?$arResult['SUBSCRIPTION']['EMAIL']:$arResult['REQUEST']['EMAIL'])?>" placeholder="<?=GetMessage('CT_BSE_EMAIL_LABEL')?>" /><?
			?></div><?
			
			?><div class="line type clearfix"><?
				?><div class="title"><?=GetMessage('CT_BSE_FORMAT_LABEL')?></div><?
				?><input type="radio" name="FORMAT" id="MAIL_TYPE_TEXT" value="text" <?if($arResult['SUBSCRIPTION']['FORMAT']!='html') echo 'checked'?> /><label for="MAIL_TYPE_TEXT"><?=GetMessage('CT_BSE_FORMAT_TEXT')?></label><?
				?><input type="radio" name="FORMAT" id="MAIL_TYPE_HTML" value="html" <?if($arResult['SUBSCRIPTION']['FORMAT']=='html') echo 'checked'?> /><label for="MAIL_TYPE_HTML"><?=GetMessage('CT_BSE_FORMAT_HTML')?></label><?
			?></div><?
			
			?><div class="line rubrics clearfix"><?
				?><div class="title"><?=GetMessage('CT_BSE_RUBRIC_LABEL')?></div><?
				foreach($arResult['RUBRICS'] as $itemID => $itemValue)
				{
					?><div class="item"><?
						?><input type="checkbox" id="RUBRIC_<?=$itemID?>" name="RUB_ID[]" value="<?=$itemValue['ID']?>"<?if($itemValue['CHECKED']) echo ' checked'?> /><?
						?><label for="RUBRIC_<?=$itemID?>"><b><?=$itemValue['NAME']?></b><br /><span class="sbscr"><?=$itemValue['DESCRIPTION']?></span></label><?
					?></div><?
				}
			?></div><?
			
			?><div class="line clearfix"><?
				if($arResult['ID']==0)
				{
					?><span class="note"><?=GetMessage('CT_BSE_NEW_NOTE')?></span><?
				} else{
					?><span class="note"><?=GetMessage('CT_BSE_EXIST_NOTE')?></span><?
				}
			?></div><?
			
			?><div class="line buttons clearfix"><?
				?><input type="submit" name="Save" value="<?=( $arResult['ID']>0?GetMessage('CT_BSE_BTN_EDIT_SUBSCRIPTION'):GetMessage('CT_BSE_BTN_ADD_SUBSCRIPTION'))?>" /><?
			?></div><?
			
			if($arResult['ID']>0 && $arResult['SUBSCRIPTION']['CONFIRMED']<>'Y')
			{
				?><div class="line border clearfix"><?
					?><span class="note"><?=GetMessage('CT_BSE_CONF_NOTE')?></span><?
					?><input class="text" name="CONFIRM_CODE" type="text" value="" placeholder="<?=GetMessage('CT_BSE_CONFIRMATION')?>" /><?
					?><input class="btn btn1" type="submit" name="confirm" value="<?=GetMessage('CT_BSE_BTN_CONF')?>" /><?
				?></div><?
			}
			
		?></form><?
			
		if(!CSubscription::IsAuthorized($arResult['ID']))
		{
			?><form action="<?=$arResult['FORM_ACTION']?>" method="post"><?
				
				?><?=bitrix_sessid_post();?><?
				?><input type="hidden" name="action" value="sendcode" /><?
				
				?><div class="line clearfix"><?
					?><span class="note"><?=GetMessage('CT_BSE_SEND_NOTE')?></span><?
					?><input class="text" name="sf_EMAIL" type="text" value="" placeholder="<?=GetMessage('CT_BSE_EMAIL')?>" /><?
					?><input class="btn btn1" type="submit" value="<?=GetMessage('CT_BSE_BTN_SEND')?>" /><?
				?></div><?
				
			?></form><?
		}
	}
	
?></div>