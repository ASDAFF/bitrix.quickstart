<?php

namespace Yandex\Market\Components;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

class AdminFormField extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		$arParams['INPUT_NAME'] = str_replace('[]', '', $arParams['INPUT_NAME']);
		$arParams['MULTIPLE'] = ($arParams['MULTIPLE'] === 'Y');
		$arParams['PLACEHOLDER'] = ($arParams['PLACEHOLDER'] === 'Y');
		$arParams['CHILD'] = ($arParams['CHILD'] === 'Y');
		$arParams['CHILD_CLASS_NAME'] = trim($arParams['CHILD_CLASS_NAME']);
		$arParams['HAS_ERROR'] = !empty($arParams['HAS_ERROR']);

		if ($arParams['MULTIPLE'])
		{
			$arParams['VALUE'] = (array)$arParams['VALUE'];
			$arParams['HAS_VALUE'] = true;

			if (empty($arParams['VALUE']))
			{
				$arParams['HAS_VALUE'] = false;
				$arParams['VALUE'][] = [ 'PLACEHOLDER' => true ];
			}
		}
		else
		{
			$arParams['HAS_VALUE'] = !empty($arParams['VALUE']);
		}

		return $arParams;
	}

	public function executeComponent()
	{
		$this->includeComponentTemplate();
	}
}