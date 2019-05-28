<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;
use Bitrix\Main;

$arResult['DEFAULT_SOURCES'] = [];
$context = $arParams['CONTEXT'];

/** @var Market\Export\Xml\Tag\Base $tag */
foreach ($arResult['TAGS'] as $tag)
{
	$tagId = $tag->getId();

	$arResult['DEFAULT_SOURCES'][$tagId] = (
		isset($arResult['NODE_AVAILABLE_SOURCES'][$tagId][$arResult['RECOMMENDATION_TYPE']])
			? $arResult['RECOMMENDATION_TYPE']
			: $tag->getDefaultSource($context)
	);

	if ($tag->hasAttributes())
	{
		foreach ($tag->getAttributes() as $attribute)
		{
			$attributeFullName = $tagId . '.' . $attribute->getId();

			$arResult['DEFAULT_SOURCES'][$attributeFullName] = (
				isset($arResult['NODE_AVAILABLE_SOURCES'][$attributeFullName][$arResult['RECOMMENDATION_TYPE']])
					? $arResult['RECOMMENDATION_TYPE']
					: $attribute->getDefaultSource($context)
			);
		}
	}
}