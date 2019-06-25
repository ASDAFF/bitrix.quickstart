<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	global $APPLICATION;

	CStartShopTheme::ApplyTheme(SITE_ID);

	$arDefaultParams = array(
			'REQUEST_VARIABLE_ACTION' => 'action',
			'REQUEST_VARIABLE_ITEM' => 'item',
			'REQUEST_VARIABLE_QUANTITY' => 'quantity'
	);

	$arParams = array_merge($arDefaultParams, $arParams);

	$arRequestParametersRemove = array(
			$arParams['REQUEST_VARIABLE_ACTION'],
			$arParams['REQUEST_VARIABLE_ITEM'],
			$arParams['REQUEST_VARIABLE_QUANTITY']
	);

	$arResult['COUNT'] = CStartShopBasket::GetItemsCount(SITE_ID);
	$arResult['SUM'] = CStartShopBasket::GetItemsAmount(SITE_ID, $arParams['CURRENCY']);

	$arResult['ACTIONS'] = array();
	$arResult['ACTIONS']['CLEAR'] = $APPLICATION->GetCurPageParam(
			urlencode($arParams['REQUEST_VARIABLE_ACTION']).'=Clear',
			$arRequestParametersRemove
	);

	$arResult['ITEMS'] = array();

	$bBasketActionComplete = (bool)CStartShopBasket::HandleRequestActions(
			$_REQUEST[$arParams['REQUEST_VARIABLE_ACTION']],
			$_REQUEST[$arParams['REQUEST_VARIABLE_ITEM']],
			$_REQUEST[$arParams['REQUEST_VARIABLE_QUANTITY']],
			false,
			SITE_ID,
			array(
					'Add' => false,
					'Delete' => 'Delete',
					'SetQuantity' => 'SetQuantity',
					'Clear' => 'Clear'
			)
	);

	if ($bBasketActionComplete) {
		LocalRedirect($APPLICATION->GetCurPageParam('', $arRequestParametersRemove));
		die();
	}

	$dbItems = CStartShopBasket::GetList(
			array('NAME' => 'ASC'),
			array(),
			array(),
			array(),
			$arParams['CURRENCY'],
			SITE_ID
	);

	$arIBlockElementsLinked = array();

	while ($arItem = $dbItems->Fetch()) {
		$arItem['ACTIONS'] = array();
		$arItem['ACTIONS']['DELETE'] = $APPLICATION->GetCurPageParam(
				urlencode($arParams['REQUEST_VARIABLE_ACTION']).'=Delete&'.
				urlencode($arParams['REQUEST_VARIABLE_ITEM']).'='.urlencode($arItem['ID']),
				$arRequestParametersRemove
		);

		$arItem['ACTIONS']['SET_QUANTITY'] = $APPLICATION->GetCurPageParam(
				urlencode($arParams['REQUEST_VARIABLE_ACTION']).'=SetQuantity&'.
				urlencode($arParams['REQUEST_VARIABLE_ITEM']).'='.urlencode($arItem['ID']).'&'.
				urlencode($arParams['REQUEST_VARIABLE_QUANTITY']).'=#QUANTITY#',
				$arRequestParametersRemove
		);

		if ($arItem['STARTSHOP']['OFFER']['OFFER'])
			if (!in_array($arItem['STARTSHOP']['OFFER']['LINK'], $arIBlockElementsLinked))
				$arIBlockElementsLinked[] = $arItem['STARTSHOP']['OFFER']['LINK'];

		$arResult['ITEMS'][$arItem['ID']] = $arItem;
	}

	$this->IncludeComponentTemplate();
?>