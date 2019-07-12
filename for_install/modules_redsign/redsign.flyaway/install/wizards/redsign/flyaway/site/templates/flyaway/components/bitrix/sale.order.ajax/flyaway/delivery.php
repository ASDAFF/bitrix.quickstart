<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<script>
	function fShowStore(id, showImages, formWidth, siteId)
	{
		var strUrl = '<?=$templateFolder?>' + '/map.php';
		var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

		var storeForm = new BX.CDialog({
					'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
					head: '',
					'content_url': strUrl,
					'content_post': strUrlPost,
					'width': formWidth,
					'height':450,
					'resizable':false,
					'draggable':false
				});

		var button = [
				{
					title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
					id: 'crmOk',
					'action': function ()
					{
						GetBuyerStore();
						BX.WindowManager.Get().Close();
					}
				},
				BX.CDialog.btnCancel
			];
		storeForm.ClearButtons();
		storeForm.SetButtons(button);
		storeForm.Show();
	}

	function GetBuyerStore()
	{
		BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
		//BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
		BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
		BX.show(BX('select_store'));
	}

	function showExtraParamsDialog(deliveryId)
	{
		var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
		var formName = 'extra_params_form';
		var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

		if(window.BX.SaleDeliveryExtraParams)
		{
			for(var i in window.BX.SaleDeliveryExtraParams)
			{
				strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
			}
		}

		var paramsDialog = new BX.CDialog({
			'title': '<?=GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
			head: '',
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width': 500,
			'height':200,
			'resizable':true,
			'draggable':false
		});

		var button = [
			{
				title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
				id: 'saleDeliveryExtraParamsOk',
				'action': function ()
				{
					insertParamsToForm(deliveryId, formName);
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];

		paramsDialog.ClearButtons();
		paramsDialog.SetButtons(button);
		//paramsDialog.adjustSizeEx();
		paramsDialog.Show();
	}

	function insertParamsToForm(deliveryId, paramsFormName)
	{
		var orderForm = BX("ORDER_FORM"),
			paramsForm = BX(paramsFormName);
			wrapDivId = deliveryId + "_extra_params";

		var wrapDiv = BX(wrapDivId);
		window.BX.SaleDeliveryExtraParams = {};

		if(wrapDiv)
			wrapDiv.parentNode.removeChild(wrapDiv);

		wrapDiv = BX.create('div', {props: { id: wrapDivId}});

		for(var i = paramsForm.elements.length-1; i >= 0; i--)
		{
			var input = BX.create('input', {
				props: {
					type: 'hidden',
					name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
					value: paramsForm.elements[i].value
					}
				}
			);

			window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

			wrapDiv.appendChild(input);
		}

		orderForm.appendChild(wrapDiv);

		BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
	}
</script>
<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>">

<table class="table table-order">
    <tbody>
        <?php foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery): ?>
            <?php if ($delivery_id !== 0 && intval($delivery_id) <= 0): ?>
                <?php foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile): ?>
                <?php
                if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
                    $extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
                else
                    $extraParams = "";
                ?>
                <tr onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();">
                    <td class="gui-box table-order__radio">
                        <label class="gui-radiobox" for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
                            <input type="radio"
                                id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
                                name="PAY_SYSTEM_ID"
                                class="gui-radiobox-item"
                                value="<?=$delivery_id.":".$profile_id;?>"
                                <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
                            ><span class="gui-out"><span class="gui-inside"></span></span>
                        </label>
                    </td>
                    <td class="table-order__picture hidden-xs hidden-sm">
                        <?php
                            if (count($arDelivery["LOGOTIP"]) > 0):

                                $arFileTmp = CFile::ResizeImageGet(
                                    $arDelivery["LOGOTIP"]["ID"],
                                    array("width" => "95", "height" =>"55"),
                                    BX_RESIZE_IMAGE_PROPORTIONAL,
                                    true
                                );

                                $deliveryImgURL = $arFileTmp["src"];
                            else:
                                $deliveryImgURL = $arResult['NO_PHOTO'];
                            endif;
                        ?>
                         <span class="table-order__img" style="background-image: url(<?=$deliveryImgURL?>)"></span>
                    </td>
                    <td>
                        <b><?=htmlspecialcharsbx($arDelivery["TITLE"])." (".htmlspecialcharsbx($arProfile["TITLE"]).")";?></b>
                        <div class="price" onclick="event.preventDefault(); event.stopPropagation()">
                            <?php if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0): ?>
                                <div><?=Loc::getMessage("SALE_DELIV_PRICE")?>: <b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b></div>
                                <?php if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1): ?>
                                    <?=Loc::getMessage('SALE_PACKS_COUNT')?>: <b><?=$arResult["PACKS_COUNT"]?></b>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                    "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                    "DELIVERY" => $delivery_id,
                                    "PROFILE" => $profile_id,
                                    "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                    "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                    "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                    "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                    "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                    "ITEMS" => $arResult["BASKET_ITEMS"],
                                    "EXTRA_PARAMS_CALLBACK" => $extraParams
                                ), null, array('HIDE_ICONS' => 'Y')); ?>
                            <?php endif; ?>
                        </div>
                        <p>
                            <?php if (strlen($arProfile["DESCRIPTION"]) > 0): ?>
                                <?=nl2br($arProfile["DESCRIPTION"])?>
                            <?php else: ?>
                                <?=nl2br($arDelivery["DESCRIPTION"])?>
                            <?php endif;?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                if (count($arDelivery["STORE"]) > 0)
                    $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."');BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm()\";";
                else
                    $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
                ?>
                <tr <?=$clickHandler?>>
                    <td class="gui-box table-order__radio">
                        <label class="gui-radiobox" for="ID_DELIVERY_ID_<?= $arDelivery["ID"]?>">
                            <input type="radio"
                                id="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>"
                                name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                                class="gui-radiobox-item"
                                value="<?=$arDelivery["ID"]?>"
                                <?php if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
                                onclick="event.stopPropagation()"
                            ><span class="gui-out"><span class="gui-inside"></span></span>
                        </label>
                    </td>
                    <td class="table-order__picture hidden-xs hidden-sm">
                        <?php
                        if (count($arDelivery["LOGOTIP"]) > 0):

                            $arFileTmp = CFile::ResizeImageGet(
                                $arDelivery["LOGOTIP"]["ID"],
                                array("width" => "95", "height" =>"55"),
                                BX_RESIZE_IMAGE_PROPORTIONAL,
                                true
                            );

                            $deliveryImgURL = $arFileTmp["src"];
                        else:
                            $deliveryImgURL = $arResult['NO_PHOTO'];
                        endif;
                        ?>
                        <span class="table-order__img" style="background-image: url(<?=$deliveryImgURL?>)"></span>
                    </td>
                    <td>
                        <b><?=htmlspecialcharsbx($arDelivery["NAME"])?></b>
                        <?php if($arDelivery["PERIOD_TEXT"]): ?>
                            <div><?=$arDelivery["PERIOD_TEXT"]?></div>
                        <?php endif; ?>
                        <div><?=Loc::getMessage("SALE_DELIV_PRICE");?>: <b><?=$arDelivery["PRICE_FORMATED"]?></b></div>
                        <p>
                            <?=$arDelivery["DESCRIPTION"]?>
                        </p>
                        <?php if (count($arDelivery["STORE"]) > 0): ?>
                            <span id="select_store"<?if(strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
                                <span class="select_store"><?=Loc::getMessage('SOA_ORDER_GIVE_TITLE');?>: </span>
                                <span class="ora-store" id="store_desc"><?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?></span>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
