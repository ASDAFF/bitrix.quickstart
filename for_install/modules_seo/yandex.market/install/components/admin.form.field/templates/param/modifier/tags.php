<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;
use Bitrix\Main;

$context = $arParams['CONTEXT'];
$arResult['TAGS'] = [];
$arResult['DOCUMENTATION_LINK'] = null;

try
{
	$typeTitle = Market\Export\Xml\Format\Manager::getTypeTitle($context['EXPORT_FORMAT']);
	$format = Market\Export\Xml\Format\Manager::getEntity(
		$context['EXPORT_SERVICE'],
		$context['EXPORT_FORMAT']
	);

	$arResult['DOCUMENTATION_LINK'] = $format->getDocumentationLink();
	$arResult['DOCUMENTATION_BETA'] = (strpos($typeTitle, '(beta)') !== false);

	$offerTag = $format->getOffer();

	if (!$offerTag->isDefined())
	{
		$arResult['TAGS'][] = $offerTag;
	}

	foreach ($offerTag->getChildren() as $childTag)
	{
		if (!$childTag->isDefined())
		{
			$arResult['TAGS'][] = $childTag;
		}
	}
}
catch (Main\SystemException $exception)
{
	$arResult['ERRORS'][] = $exception->getMessage();
}