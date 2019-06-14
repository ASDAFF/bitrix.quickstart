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
	<table style="margin:0;padding:0;border-collapse:collapse;border-width:1px;border-style:solid;border-color:#E0EAF0;"
	       width="100%"
	       border="0"
	       cellpadding="0"
	       cellspacing="0">
		<thead>
		<tr>
			<th style="border: none;background-color:#E7F2F2; padding:10px; text-align:left;text-transform:uppercase"><?=Loc::getMessage('AOS_TPL_SHIPMENT_BLOCK')?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border: none;padding:10px;padding-top:0;background-color:#F7FAFA">
				<table width="100%" cellpadding="0" cellspacing="0" style="border-width:1px; border-style:solid; border-color:#E3E8EA;margin:10px 0 0 0; padding:0">
					<? foreach($arResult['SHIPMENT'] as $shipment): ?>
						<?
						$dateInsert = new \Bitrix\Main\Type\Date($shipment['DATE_INSERT']);

						$allowDeliveryString = ($shipment['ALLOW_DELIVERY'] == 'Y') ? 'YES' : 'NO';
						$deductedString      = ($shipment['DEDUCTED'] == 'Y') ? 'YES' : 'NO';

						$styleDelivery = ($shipment['ALLOW_DELIVERY'] == 'Y' ? 'style="color:#658d0f"' : 'style="color:#cc2020"');
						$styleDeducted = ($shipment['DEDUCTED'] == 'Y' ? 'style="color:#658d0f"' : 'style="color:#cc2020"');

						$allowDelivery = '<span ' . $styleDelivery . '>' . Loc::getMessage('AOS_TPL_SHIPMENT_ALLOW_DELIVERY_' . $allowDeliveryString) . '</span>';
						$deducted      = '<span ' . $styleDeducted . '>' . Loc::getMessage('AOS_TPL_SHIPMENT_DEDUCTED_' . $deductedString) . '</span>';

						$shipmentStatus = '<span style="color:#658d0f">' . htmlspecialcharsbx($arResult['SHIPMENT_STATUS'][ $shipment['STATUS_ID'] ]) . '</span>';

						$shortLogoPath = $arResult['SALE_URL'] . '/bitrix/images/sale/logo-default-d.gif';
						if($shipment['DELIVERY_ID'])
						{
							$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($shipment['DELIVERY_ID']);
							if($service && $service->getLogotip() > 0)
							{
								$shortLogo     = \CFile::ResizeImageGet($service->getLogotip(), array('width' => 80, 'height' => 60));
								$shortLogoPath = $arResult['SALE_URL'] . $shortLogo['src'];
							}
						}

						$tdStyle = 'border: none;padding:5px;';
						?>
						<tbody style="border-bottom:1px solid #c9d0d1;">
						<tr style="background-color:#E3E8EA; font-size: 13px;line-height: 25px;min-height: 25px;;vertical-align: middle;color: #646d7a;">
							<td style="border: none;padding:0 10px" colspan="6">
								<?=Loc::getMessage('AOS_TPL_SHIPMENT_BLOCK_EDIT_SHIPMENT_TITLE', array("#ID#" => $shipment['ID'], '#DATE_INSERT#' => $dateInsert))?>
							</td>
						</tr>
						<tr style="background-color:#F7FAFA">
							<td style="<?=$tdStyle?>" width="10%">
								<img src="<?=$shortLogoPath?>" style="border: 1px solid #acbbc0;max-width:80px" alt="<?=$shipment['DELIVERY_NAME']?>">
							</td>
							<td style="<?=$tdStyle?>" width="18%"><?=htmlspecialcharsbx($shipment['DELIVERY_NAME'])?></td>
							<td style="<?=$tdStyle?>" width="18%"><?=Loc::getMessage('AOS_TPL_SHIPMENT_ALLOW_DELIVERY') . ': ' . $allowDelivery?></td>
							<td style="<?=$tdStyle?>" width="18%"><?=Loc::getMessage('AOS_TPL_SHIPMENT_DEDUCTED') . ': ' . $deducted?></td>
							<td style="<?=$tdStyle?>" width="18%"><?=Loc::getMessage('AOS_TPL_SHIPMENT_DELIVERY_STATUS') . ': ' . $shipmentStatus?></td>
							<td style="<?=$tdStyle?>" width="18%"><?=Loc::getMessage('AOS_TPL_SHIPMENT_DELIVERY_PRICE') . ': ' . SaleFormatCurrency(floatval($shipment['BASE_PRICE_DELIVERY']), $shipment['CURRENCY'])?></td>
						</tr>
						</tbody>
					<? endforeach; ?>
				</table>
			</td>
		</tr>
		</tbody>
	</table>
<? endif ?>