<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
{
	?><h3><? echo GetMessage('BX_CMP_CDI_TPL_MESS_NO_DISCOUNT_SAVE'); ?></h3><?
}
else
{
	?><h3><? echo GetMessage('BX_CMP_CDI_TPL_MESS_DISCOUNT_SAVE'); ?></h3><?
	foreach ($arResult as &$arDiscountSave)
	{
		?><h4><? echo $arDiscountSave['NAME']; ?></h4><?
		?><p><? echo GetMessage('BX_CMP_CDI_TPL_MESS_SIZE'); ?> <?
		if ('P' == $arDiscountSave['VALUE_TYPE'])
		{
			echo $arDiscountSave['VALUE']; ?>&nbsp;%<?
		}
		else
		{
			echo FormatCurrency($arDiscountSave['VALUE'], $arDiscountSave['CURRENCY']);
		}
		if (isset($arDiscountSave['NEXT_LEVEL']) && is_array($arDiscountSave['NEXT_LEVEL']))
		{
			$strNextLevel = '';
			if ('P' == $arDiscountSave['NEXT_LEVEL']['VALUE_TYPE'])
			{
				$strNextLevel = $arDiscountSave['NEXT_LEVEL']['VALUE'].'&nbsp;%';
			}
			else
			{
				$strNextLevel = FormatCurrency($arDiscountSave['NEXT_LEVEL']['VALUE'], $arDiscountSave['CURRENCY']);
			}

			?><br /><? echo str_replace(array('#SIZE#', '#SUMM#'), array($strNextLevel, FormatCurrency(($arDiscountSave['NEXT_LEVEL']['RANGE_FROM'] - $arDiscountSave['RANGE_SUMM']),$arDiscountSave['CURRENCY'])), GetMessage('BX_CMP_CDI_TPL_MESS_NEXT_LEVEL')); ?><?
		}
		?></p><br /><?
	}
}
?>