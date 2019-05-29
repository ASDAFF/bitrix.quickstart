<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** @var $tag \Yandex\Market\Export\Xml\Tag\Base */
/** @var $attribute \Yandex\Market\Export\Xml\Attribute\Base */
/** @var $isAttribute bool */
/** @var $isRequired bool */

if ($isAttribute)
{
	$attributeDescription = $attribute->getDescription();

	if (strlen($attributeDescription) > 0)
	{
		?>
		<span class="b-icon icon--question indent--right b-tag-tooltip--holder">
			<span class="b-tag-tooltip--content"><?= $attributeDescription; ?></span>
		</span>
		<?
	}

	echo $attribute->getName() . '=';
}
else
{
	$tagNameDisplay = htmlspecialcharsbx('<' . $tag->getName() . '>');
	$tagDescription = $tag->getDescription();

	if (strlen($tagDescription) > 0)
	{
		?>
		<span class="b-icon icon--question indent--right b-tag-tooltip--holder">
			<span class="b-tag-tooltip--content"><?= $tagDescription; ?></span>
		</span>
		<?
	}

	if ($isRequired)
	{
		?>
		<strong><?= $tagNameDisplay; ?></strong>
		<?
	}
	else
	{
		echo $tagNameDisplay;
	}
}