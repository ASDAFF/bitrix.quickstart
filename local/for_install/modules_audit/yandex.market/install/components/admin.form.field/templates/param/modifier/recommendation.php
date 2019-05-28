<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;
use Bitrix\Main;

$arResult['RECOMMENDATION'] = [];
$context = $arParams['CONTEXT'];
$tagRecommendationList = [];

/** @var Market\Export\Xml\Tag\Base $tag */
foreach ($arResult['TAGS'] as $tag)
{
	$tagId = $tag->getId();
	$nodeList = [ $tag ];

	if ($tag->hasAttributes())
	{
		array_splice($nodeList, 1, 0, $tag->getAttributes());
	}

	foreach ($nodeList as $node)
	{
		$nodeFullType = ($node === $tag ? $tagId : $tagId . '.' . $node->getId());
		$nodeRecommendation = $node->getSourceRecommendation($context);

		if (!empty($nodeRecommendation))
		{
			$tagRecommendationList[$nodeFullType] = $nodeRecommendation;

			$arResult['NODE_AVAILABLE_SOURCES'][$nodeFullType][$arResult['RECOMMENDATION_TYPE']] = true;
		}
	}
}

foreach ($tagRecommendationList as $nodeName => $recommendationList)
{
	$newRecommendationList = [];

	foreach ($recommendationList as $recommendation)
	{
		if (isset($arResult['SOURCE_TYPE_ENUM'][$recommendation['TYPE']]))
		{
			$typeEnum = $arResult['SOURCE_TYPE_ENUM'][$recommendation['TYPE']];

			if ($typeEnum['VARIABLE'])
			{
				$newRecommendationList[] = [
					'ID' => htmlspecialcharsbx($recommendation['TYPE'] . '|' . $recommendation['VALUE']),
					'VALUE' => $recommendation['VALUE']
				];
			}
			else
			{
				$fieldKey = $recommendation['TYPE'] . '.' . $recommendation['FIELD'];

				if (isset($arResult['SOURCE_FIELD_ENUM'][$fieldKey]))
				{
					$fieldEnum = $arResult['SOURCE_FIELD_ENUM'][$fieldKey];

					$newRecommendationList[] = [
						'ID' => $typeEnum['ID'] . '|' . $fieldEnum['ID'],
						'VALUE' => $typeEnum['VALUE'] . ': '. $fieldEnum['VALUE']
					];
				}
			}
		}
	}

	if (!empty($newRecommendationList))
	{
		$arResult['RECOMMENDATION'][$nodeName] = $newRecommendationList;
	}
}