<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;
use Bitrix\Main\Localization\Loc;

if (!empty($arResult['ERRORS']))
{
	\CAdminMessage::ShowMessage([
		'TYPE' => 'ERROR',
		'MESSAGE' => implode('<br />', $arResult['ERRORS']),
		'HTML' => true
	]);

	return;
}

$this->addExternalJs('/bitrix/js/yandex.market/source/manager.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/param/tag.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/param/tagcollection.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/param/node.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/param/nodecollection.js');

$lang = [
	'SELECT_PLACEHOLDER' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_SELECT_PLACEHOLDER'),
	'PARAM_SIZE_NAME' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_PARAM_SIZE_NAME'),
	'PARAM_SIZE_WARNINIG_REQUIRE_UNIT' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_PARAM_SIZE_WARNINIG_REQUIRE_UNIT'),
];

$langStatic = [
	'HEADER_TAG' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_HEADER_TAG'),
	'HEADER_SOURCE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_HEADER_SOURCE'),
	'HEADER_FIELD' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_HEADER_FIELD'),
	'ADD_TAG' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_ADD_TAG'),
	'SETTINGS_UTM_TOGGLE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_SETTINGS_UTM_TOGGLE'),
	'SETTINGS_UTM_TOGGLE_FILL' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_SETTINGS_UTM_TOGGLE_FILL'),
	'SETTINGS_UTM_TOGGLE_ALT' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_SETTINGS_UTM_TOGGLE_ALT'),
];

$fieldId = 'param-' . $this->randString(5);
$addTagList = [];
$hasActiveAddTag = false;

?>
<div class="b-param-table js-plugin js-param-manager" id="<?= $fieldId; ?>" data-plugin="Field.Param.TagCollection" data-base-name="<?= $arParams['INPUT_NAME']; ?>">
	<table width="100%">
		<tr>
			<th align="right" width="40%"><?= $langStatic['HEADER_TAG']; ?></th>
			<th class="width--param-source-cell"><?= $langStatic['HEADER_SOURCE']; ?></th>
			<th class="width--param-field-cell"><?= $langStatic['HEADER_FIELD']; ?></th>
			<th>&nbsp;</th>
		</tr>
	</table>
	<?
	$tagIndex = 0;

	/** @var \Yandex\Market\Export\Xml\Tag\Base $tag */
	foreach ($arResult['TAGS'] as $tag)
	{
		$tagValues = [];
		$tagId = $tag->getId();
		$tagName = $tag->getName();
		$hasTagValues = false;
		$attributes = $tag->getAttributes();

		foreach ($arParams['VALUE'] as $rowValue)
		{
			if ($tagId === $rowValue['XML_TAG'])
			{
				$tagValues[] = $rowValue;
			}
		}

		if (empty($tagValues))
		{
			$tagValues[] = $tag->isRequired() || $tag->isVisible() ? [] : [ 'PLACEHOLDER' => true ];
		}

		foreach ($tagValues as $tagValue)
		{
			$tagInputName = $arParams['INPUT_NAME'] . '[' . $tagIndex . ']';
			$isTagPlaceholder = $arParams['PLACEHOLDER'] || !empty($tagValue['PLACEHOLDER']);
			$attributeIndex = 0;
			$addAttributeList = [];
			$hasActiveAddAttribute = false;

			if (!$isTagPlaceholder)
			{
				$hasTagValues = true;
			}

			?>
			<div class="<?= $isTagPlaceholder ? 'is--hidden' : ''; ?> js-param-tag-collection__item" data-plugin="Field.Param.Tag" data-type="<?= $tagId; ?>" <?= $tag->isMultiple() ? 'data-multiple="true"' : ''; ?> <?= $tag->isRequired() ? 'data-required="true"' : ''; ?>>
				<input class="js-param-tag__input" type="hidden" data-name="ID" <?

					if (!$isTagPlaceholder)
					{
						echo 'name="' . $tagInputName . '[ID]' . '"';
						echo 'value="' . $tagValue['ID'] . '"';
					}

				?> />
				<input class="js-param-tag__input is--persistent" type="hidden" value="<?= $tagId; ?>" data-name="XML_TAG" <?

					if (!$isTagPlaceholder)
					{
						echo 'name="' . $tagInputName . '[XML_TAG]' . '"';
					}

				?> />

				<table class="js-param-tag__child" width="100%" data-plugin="Field.Param.NodeCollection" data-name="PARAM_VALUE">
					<?
					if (!$tag->hasChildren())
					{
						$attributeInputName = $tagInputName . '[PARAM_VALUE][' . $attributeIndex . ']';
						$attributeValue = [];
						$attributeType = Market\Export\ParamValue\Table::XML_TYPE_VALUE;
						$attributeValueType = $tag->getValueType();
						$attributeId = null;
						$attributeName = null;
						$isAttribute = false;
						$isRequired = $tag->isRequired();
						$isDefined = $tag->isDefined();
						$isAttributePlaceholder = false;

						if (!empty($tagValue['PARAM_VALUE']))
						{
							foreach ($tagValue['PARAM_VALUE'] as $paramValue)
							{
								if ($paramValue['XML_TYPE'] === $attributeType)
								{
									$attributeValue = $paramValue;
									break;
								}
							}
						}

						include __DIR__ . '/partials/value.php';

						$attributeIndex++;
					}
					else if ($tagIndex > 0) // is not first
					{
						?>
						<tr>
							<td class="b-param-table__cell" align="right" width="40%">
								<?
								include __DIR__ . '/partials/name.php';
								?>
							</td>
							<td class="b-param-table__cell" colspan="3">&nbsp;</td>
						</tr>
						<?
					}

					if (!empty($attributes))
					{
						foreach ($attributes as $attribute)
						{
							$isDefined = $attribute->isDefined();

							if ($isDefined && !$attribute->isVisible()) { continue; } /* предопределенный аттрибут */

							$attributeInputName = $tagInputName . '[PARAM_VALUE][' . $attributeIndex . ']';
							$attributeValue = null;
							$attributeId = $attribute->getId();
							$attributeName = $attribute->getName();
							$attributeType = Market\Export\ParamValue\Table::XML_TYPE_ATTRIBUTE;
							$attributeValueType = $attribute->getValueType();
							$isAttribute = true;
							$isRequired = $attribute->isRequired();
							$isAttributePlaceholder = false;

							if (!$isTagPlaceholder && !empty($tagValue['PARAM_VALUE']))
							{
								foreach ($tagValue['PARAM_VALUE'] as $paramValue)
								{
									if (
										$paramValue['XML_TYPE'] === $attributeType
										&& $paramValue['XML_ATTRIBUTE_NAME'] === $attributeId
									)
									{
										$attributeValue = $paramValue;
										break;
									}
								}
							}

							if ($attributeValue === null)
							{
								$attributeValue = [];
								$isAttributePlaceholder = (!$attribute->isRequired() && !$attribute->isVisible());
							}

							if ($isDefined)
							{
								$definedSource = $attribute->getDefinedSource();

								$attributeValue['SOURCE_TYPE'] = $definedSource['TYPE'];
								$attributeValue['SOURCE_FIELD'] = (
									(!empty($arResult['SOURCE_TYPE_ENUM'][$definedSource['TYPE']]['VARIABLE']))
										? $definedSource['VALUE']
										: $definedSource['FIELD']
								);
							}

							include __DIR__ . '/partials/value.php';

							if (!$attribute->isRequired())
							{
								$addAttributeList[$attributeId] = $isAttributePlaceholder;

								if ($isAttributePlaceholder)
								{
									$hasActiveAddAttribute = true;
								}
							}

							if (!$isAttributePlaceholder)
							{
								$attributeIndex++;
							}
						}

						if (!empty($addAttributeList))
						{
							?>
							<tr class="<?= $hasActiveAddAttribute ? '' : 'is--hidden'; ?> js-param-node-collection__item-add-holder">
								<td class="b-param-table__cell" align="right" width="40%">&nbsp;</td>
								<td class="b-param-table__cell" colspan="3">
									<span class="js-params--show-hidden-tags">
										<a href="javascript:void(0)"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_ADD_ATTRIBUTE', [
											'#TAG_NAME#' => $tagName
										]); ?></a>
										<span class="js-params--hidden-tags">
											<?
											foreach ($addAttributeList as $attributeId => $isActive)
											{
												?>
												<a class="<?= $isActive ? '' : 'is--hidden'; ?> js-param-node-collection__item-add" href="#" data-type="<?= $tagId . '.' . $attributeId; ?>"><?= $attributeId; ?></a>
												<?
											}
											?>
										</span>
									</span>
								</td>
							</tr>
							<?
						}
					}

					include __DIR__ . '/partials/warning.php';
					include __DIR__ . '/partials/settings.php';
					?>
				</table>
			</div>
			<?

			if (!$isTagPlaceholder)
			{
				$tagIndex++;
			}
		}

		if ($tag->isMultiple())
		{
			$addTagList[$tagId] = true;
			$hasActiveAddTag = true;
		}
		else if (!$tag->isRequired())
		{
			$addTagList[$tagId] = !$hasTagValues;

			if (!$hasTagValues)
			{
				$hasActiveAddTag = true;
			}
		}
	}
	?>
	<div class="b-param-table__footer <?= $hasActiveAddTag ? '' : 'is--hidden'; ?> js-param-tag-collection__item-add-holder">
		<table width="100%">
			<tr>
				<td class="b-param-table__cell" width="40%">&nbsp;</td>
				<td class="b-param-table__cell">
					<span class="js-params--show-hidden-tags">
						<a class="adm-btn" href="javascript:void(0)"><?= $langStatic['ADD_TAG']; ?></a>
						<span class="js-params--hidden-tags">
							<?
							foreach ($addTagList as $tagId => $isActive)
							{
								?>
								<a class="<?= $isActive ? '' : 'is--hidden'; ?> js-param-tag-collection__item-add" href="#" data-type="<?= $tagId; ?>"><?= htmlspecialcharsbx('<' . $tagId . '>'); ?></a>
								<?
							}
							?>
						</span>
					</span>
					<?
					if (!empty($arResult['DOCUMENTATION_LINK']))
					{
						?>
						<div class="b-admin-message-list spacing--1x2">
							<?
							\CAdminMessage::ShowMessage([
								'TYPE' => 'OK',
								'MESSAGE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_DOCUMENTATION_TITLE'),
								'DETAILS' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_DOCUMENTATION_DETAILS', [
									'#URL#' => $arResult['DOCUMENTATION_LINK']
								]),
								'HTML' => true
							]);
							?>
						</div>
						<?
					}

					if (!empty($arResult['DOCUMENTATION_BETA']))
					{
						?>
						<div class="b-admin-message-list">
							<?
							\CAdminMessage::ShowMessage([
								'TYPE' => 'ERROR',
								'MESSAGE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_PARAM_DOCUMENTATION_BETA', [
									'#FORMAT_NAME#' => $arParams['CONTEXT']['EXPORT_FORMAT']
								]),
								'HTML' => true
							]);
							?>
						</div>
						<?
					}
					?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?
$managerData = [
	'types' => array_values($arResult['SOURCE_TYPE_ENUM']),
	'fields' => array_values($arResult['SOURCE_FIELD_ENUM']),
	'recommendation' => $arResult['RECOMMENDATION'],
	'typeMap' => $arResult['TYPE_MAP_JS']
];
?>
<script>
	(function() {
		var Source = BX.namespace('YandexMarket.Source');
		var utils = BX.namespace('YandexMarket.Utils');

		// init source manager

		new Source.Manager('#<?= $fieldId ?>', <?= Market\Utils::jsonEncode($managerData, JSON_UNESCAPED_UNICODE); ?>);

		// extend lang

		utils.registerLang(<?= Market\Utils::jsonEncode($lang, JSON_UNESCAPED_UNICODE); ?>, 'YANDEX_MARKET_FIELD_PARAM_');
	})();
</script>