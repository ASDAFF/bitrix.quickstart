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

$bFoundItemProps = false;
foreach($arResult['BASKET_ITEMS'] as $item){
	if($item['DISPLAY_PROPERTIES']){
		$bFoundItemProps = true;
		break;
	}
}
?>
<? if($arResult): ?>
	<table style="margin:0;padding:0;border-collapse:collapse;border-width:1px;border-style:solid;border-color:#E0EAF0;"
	       width="100%"
	       border="0"
	       cellpadding="0"
	       cellspacing="0">
		<thead>
		<tr>
			<th style="border:none;background-color:#E7F2F2;padding:10px;text-align:left;text-transform:uppercase"><?=Loc::getMessage('AOS_TPL_BASKET_BLOCK_NAME')?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border: 0;padding:10px;background-color:#F7FAFA">
				<?
				$tdStyle = 'padding:10px;vertical-align:middle;border:0';
				?>
				<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; border-width:1px; border-style:solid; border-color:#E3E8EA; border-bottom:0;margin:0;padding:0">
					<thead>
					<tr style="background-color:#E3E8EA;color: #646d7a;border-bottom:1px solid #c9d0d1;">
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_COL_PICTURE')?></td>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_COL_NAME')?></td>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_COL_QUANTITY')?></td>
						<?if($bFoundItemProps):?>
							<td style="<?=$tdStyle?>;text-align:center"><?=Loc::getMessage('AOS_TPL_BASKET_COL_PROPS')?></td>
						<?endif?>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_COL_PRICE')?></td>
						<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_COL_SUM_TOTAL')?></td>
					</tr>
					</thead>
					<tfoot>
					<tr style="background-color:#FFF;font-weight:bold">
						<td colspan="1" style="<?=$tdStyle?>;white-space:nowrap"><?=Loc::getMessage('AOS_TPL_BASKET_BASKET_ITEMS_COUNT', array('#COUNT#' => intval(count($arResult['BASKET_ITEMS']))))?></td>
						<td colspan="<?=($bFoundItemProps ? 5 : 4)?>" style="<?=$tdStyle?>;padding:10px 5px"><?=htmlspecialcharsback($arResult['BASKET_DISCOUNT_FORMATED'])?></td>
					</tr>
					</tfoot>
					<? foreach($arResult['BASKET_ITEMS'] as $arItem): ?>
						<tbody style="border-bottom:1px solid #c9d0d1;">
						<tr style="background-color:#FFF">
							<td style="<?=$tdStyle?>;text-align:center">
								<img src="<?=$arResult['SALE_URL'] . $arItem['RESIZE_PICTURE']['SRC']?>" style="border:0;max-width:80px" alt="<?=$arItem['NAME']?>">
							</td>
							<td style="<?=$tdStyle?>">
								<a rel="noopener noreferrer" target="_blank" href="<?=$arResult['SALE_URL'] . $arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
								<? if($arItem['PROPS']): ?>
									<? foreach($arItem['PROPS'] as $arProp): ?>
										<p style="margin:0;padding:0"><b><?=$arProp['NAME']?>:</b> <?=$arProp['VALUE']?></p>
									<? endforeach; ?>
								<? endif ?>
							</td>
							<td style="<?=$tdStyle?>;text-align:center"><?=$arItem['QUANTITY']?> <?=$arItem['MEASURE_NAME']?></td>
							<?if($bFoundItemProps):?>
								<td style="<?=$tdStyle?>;text-align:left; font-size: 13px">
									<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
										 <div style="margin-bottom: 3px;">
												<strong><?=$arProperty["NAME"]?></strong>:&nbsp;<?
												if(is_array($arProperty["DISPLAY_VALUE"]))
													echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
												else
													echo $arProperty["DISPLAY_VALUE"];?>
										 </div>
									<?endforeach?>
								</td>
							<?endif?>
							<td style="<?=$tdStyle?>;white-space:nowrap">
								<? if($arItem['DISCOUNT_PRICE'] > 0): ?>
									<span style="text-decoration:line-through; font-size:small;"><?=$arItem['BASE_PRICE_FORMATED']?></span>
									<br>
								<? endif ?>
								<?=$arItem['PRICE_FORMATED']?>
							</td>
							<td style="<?=$tdStyle?>;white-space:nowrap"><?=$arItem['SUM_TOTAL_FORMATED']?></td>
						</tr>
						<? if($arItem['DISCOUNTS']): ?>
							<tr style="background-color:#FFF">
								<td colspan="1" style="border:0"></td>
								<td colspan="<?=($bFoundItemProps ? 5 : 4)?>" style="border:0;border-top: 1px solid #ddd;font-weight:bold;padding:5px"><?=implode('<br>', $arItem['DISCOUNTS'])?></td>
							</tr>
						<? endif ?>
						</tbody>
					<? endforeach; ?>
				</table>
				<?
				//////////////////////////ORDER TOTAL//////////////////////////
				$tdStyle      = 'border:none;padding:10px;vertical-align:middle';
				$tdStyleValue = 'border:none;padding:10px;vertical-align:middle;text-align:right;';
				?>
				<div style="overflow:hidden;margin-top:30px">
					<div style="float:right;min-width:500px;background:#ecf1d4;padding:10px;border-radius:3px;">
						<table style="width:100%;" cellpadding="0" cellspacing="0">
							<tbody>
							<tr>
								<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_ITEMS')?></td>
								<td style="<?=$tdStyleValue?>">
									<? if($arResult['PRICE_BASKET'] != $arResult['PRICE_BASKET_DISCOUNTED']): ?>
										<span style="font-size:small;text-decoration:line-through;"><?=$arResult['PRICE_BASKET_FORMATED']?></span>
										<br>
										<?=$arResult['PRICE_BASKET_DISCOUNTED_FORMATED']?>
									<? else: ?>
										<?=$arResult['PRICE_BASKET_FORMATED']?>
									<? endif ?>
								</td>
							</tr>
							<tr>
								<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_DISCOUNT')?></td>
								<td style="<?=$tdStyleValue?>"><?=$arResult['PRICE_DISCOUNT_FORMATED']?></td>
							</tr>
							<tr>
								<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_DELIVERY')?></td>
								<td style="<?=$tdStyleValue?>">
									<? if($arResult['PRICE_DELIVERY'] != $arResult['PRICE_DELIVERY_DISCOUNTED']): ?>
										<span style="font-size:small;text-decoration:line-through;"><?=$arResult['PRICE_DELIVERY_FORMATED']?></span>
										<br>
										<?=$arResult['PRICE_DELIVERY_DISCOUNTED_FORMATED']?>
									<? else: ?>
										<?=$arResult['PRICE_DELIVERY_FORMATED']?>
									<? endif ?>
								</td>
							</tr>

							<? if($arResult['BASKET_WEIGHT'] > 0): ?>
								<tr>
									<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_WEIGHT')?></td>
									<td style="<?=$tdStyleValue?>"><?=$arResult['BASKET_WEIGHT_FORMATED']?></td>
								</tr>
							<? endif ?>

							<? if($arResult['SUM_PAID'] > 0): ?>
								<tr>
									<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_SUM_PAID')?></td>
									<td style="<?=$tdStyleValue?>"><?=$arResult['SUM_PAID_FORMATED']?></td>
								</tr>
							<? endif ?>
							<tr style="color:#000;font-weight:bold;font-size:16px;background:#dbe3b9;">
								<td style="<?=$tdStyle?>"><?=Loc::getMessage('AOS_TPL_BASKET_TOTAL_SUM_UNPAID')?></td>
								<td style="<?=$tdStyleValue?>"><?=$arResult['SUM_UNPAID_FORMATED']?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
<? endif ?>