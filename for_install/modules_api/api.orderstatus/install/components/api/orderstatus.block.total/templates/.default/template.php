<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 * @var CUserTypeManager         $USER_FIELD_MANAGER
 */
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if($arResult): ?>
	<?
	$tdStyle      = 'border:none;padding:10px;vertical-align:middle';
	$tdStyleValue = 'border:none;padding:10px;vertical-align:middle;text-align:right;';
	?>
	<div style="overflow:hidden;margin-bottom:30px;">
		<div style="float:right;min-width:500px;background:#ecf1d4;padding:10px;border-radius:3px;">
			<table width="100%" style="margin:0;padding:0" cellpadding="0" cellspacing="0" border="0">
				<tbody>
				<tr>
					<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_ITEMS')?></td>
					<td style="<?=$tdStyleValue?>">
						<?if($arResult['PRICE_BASKET'] != $arResult['PRICE_BASKET_DISCOUNTED']):?>
							<span style="font-size:small;text-decoration:line-through;"><?=$arResult['PRICE_BASKET_FORMATED']?></span>
							<br>
							<?=$arResult['PRICE_BASKET_DISCOUNTED_FORMATED']?>
						<?else:?>
							<?=$arResult['PRICE_BASKET_FORMATED']?>
						<?endif?>
					</td>
				</tr>
				<tr>
					<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_DISCOUNT')?></td>
					<td style="<?=$tdStyleValue?>"><?=$arResult['PRICE_DISCOUNT_FORMATED']?></td>
				</tr>
				<tr>
					<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_DELIVERY')?></td>
					<td style="<?=$tdStyleValue?>">
						<?if($arResult['PRICE_DELIVERY'] != $arResult['PRICE_DELIVERY_DISCOUNTED']):?>
							<span style="font-size:small;text-decoration:line-through;"><?=$arResult['PRICE_DELIVERY_FORMATED']?></span>
							<br>
							<?=$arResult['PRICE_DELIVERY_DISCOUNTED_FORMATED']?>
						<?else:?>
							<?=$arResult['PRICE_DELIVERY_FORMATED']?>
						<?endif?>
					</td>
				</tr>

				<?if($arResult['BASKET_WEIGHT']>0):?>
					<tr>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_WEIGHT')?></td>
						<td style="<?=$tdStyleValue?>"><?=$arResult['BASKET_WEIGHT_FORMATED']?></td>
					</tr>
				<?endif?>

				<?if($arResult['SUM_PAID']>0):?>
					<tr>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_SUM_PAID')?></td>
						<td style="<?=$tdStyleValue?>"><?=$arResult['SUM_PAID_FORMATED']?></td>
					</tr>
				<?endif?>
				<tr style="color:#000;font-weight:bold;font-size:16px;background:#dbe3b9;">
					<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_TOTAL_SUM_UNPAID')?></td>
					<td style="<?=$tdStyleValue?>"><?=$arResult['SUM_UNPAID_FORMATED']?></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
<? endif ?>