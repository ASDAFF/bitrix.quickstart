<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="pcontent"><?

	?><div class="someform confim clearfix"><?
		
		?><p><?=$arResult["MESSAGE_TEXT"]?></p>
		<?//here you can place your own messages
			switch($arResult["MESSAGE_CODE"])
			{
			case "E01":
				?><? //When user not found
				break;
			case "E02":
				?><? //User was successfully authorized after confirmation
				break;
			case "E03":
				?><? //User already confirm his registration
				break;
			case "E04":
				?><? //Missed confirmation code
				break;
			case "E05":
				?><? //Confirmation code provided does not match stored one
				break;
			case "E06":
				?><? //Confirmation was successfull
				break;
			case "E07":
				?><? //Some error occured during confirmation
				break;
			}

		if($arResult['SHOW_FORM'])
		{
			?><form method="post" action="<?=$arResult['FORM_ACTION']?>"><?
				
				?><input type="hidden" name="<?=$arParams["USER_ID"]?>" value="<?=$arResult['USER_ID']?>" /><?
				
				?><div class="line clearfix"><?
					?><input type="text" name="<?=$arParams['LOGIN']?>" maxlength="50" value="<?=(strlen($arResult['LOGIN']) > 0 ? $arResult['LOGIN']: $arResult['USER']['LOGIN'])?>" size="17" placeholder="<?=GetMessage('CT_BSAC_LOGIN')?>:" /><?
				?></div><?
				
				?><div class="line clearfix"><?
					?><input type="text" name="<?=$arParams['CONFIRM_CODE']?>" maxlength="50" value="<?=$arResult['CONFIRM_CODE']?>" size="17" placeholder="<?=GetMessage('CT_BSAC_CONFIRM_CODE')?>:" /><?
				?></div><?
				
				?><div class="line buttons clearfix"><?
					?><input class="btn btn1" type="submit" value="<?=GetMessage('CT_BSAC_CONFIRM')?>" /><?
				?></div><?
				
			?></form><?
			
		} elseif(!$USER->IsAuthorized()) {
			?><?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "", array());?><?
		}
		
	?></div><?

?></div>