<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(strlen($arResult["ERROR_MESSAGE"])<=0)
{
	?><div class="personalacc"><?
		?><div class="title"><?=GetMessage('PERSONAL_ACCOUNT')?>:</div><?
		if( is_array($arResult['ACCOUNT_LIST']) && count($arResult['ACCOUNT_LIST'])>0 )
		{
			?><ul><?
			foreach($arResult['ACCOUNT_LIST'] as $val)
			{
				?><li><?=$val['FORMAT_NAME']?> &mdash; <span class="nowrap"><?=$val['FORMAT_VALUE']?></span></li><?
			}
			?></ul><?
		}
	?></div><?
} else {
	//echo ShowError($arResult['ERROR_MESSAGE']);
}