<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><script type="text/javascript">
	function fShowStore(id, showImages, formWidth, siteId)
	{
		console.log( 'fShowStore' );
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
</script><?

?><div class="section delivery"><?
	?><input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>" /><?
	
	if(!empty($arResult['DELIVERY']))
	{
		$width = ($arParams['SHOW_STORES_IMAGES']=='Y')?850:700;
		
		?><h4><?=GetMessage('SOA_TEMPL_DELIVERY')?></h4><?
		?><div class="body"><?
			foreach($arResult['DELIVERY'] as $delivery_id => $arDelivery)
			{
				if($delivery_id!==0 && IntVal($delivery_id)<=0)
				{
					foreach($arDelivery['PROFILES'] as $profile_id => $arProfile)
					{
						?><div class="item clearfix"><?
							if(count($arDelivery['LOGOTIP'])>0)
							{
								$arFileTmp = CFile::ResizeImageGet(
									$arDelivery['LOGOTIP']['ID'],
									array('width'=>'95','height'=>'55'),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);
								$deliveryImgURL = $arFileTmp['src'];
							} else {
								$deliveryImgURL = $arResult['NO_PHOTO']['src'];
							}
							if($arDelivery['ISNEEDEXTRAINFO']=='Y')
							{
								$extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
							} else {
								$extraParams = '';
							}
							?><table><?
								?><tr><?
									?><td><?
										?><input <?
											?>type="radio" <?
											?>id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" <?
											?>name="<?=htmlspecialcharsbx($arProfile['FIELD_NAME'])?>" <?
											?>value="<?=$delivery_id.':'.$profile_id;?>" <?
											?><?=$arProfile['CHECKED']=='Y'?'checked=\"checked\"':'';?> <?
											?>onclick="submitForm();" /><?
										?><label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"></label><?
									?></td><?
									?><td><?
										?><div class="img" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();"><?
											?><span style='background-image:url(<?=$deliveryImgURL?>);'></span><?
										?></div><?
									?></td><?
									?><td><?
										?><div class="data"><?
											?><strong onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();"><?
												?><?=htmlspecialcharsbx($arDelivery['TITLE']).' ('.htmlspecialcharsbx($arProfile['TITLE']).')';?><?
											?></strong><?
											?><span><!-- click on this should not cause form submit --><?
												if($arProfile['CHECKED']=='Y' && doubleval($arResult['DELIVERY_PRICE'])>0)
												{
													?><div><?=GetMessage('SALE_DELIV_PRICE')?>:&nbsp;<b><?=$arResult['DELIVERY_PRICE_FORMATED']?></b></div><?
													if((isset($arResult['PACKS_COUNT']) && $arResult['PACKS_COUNT'])>1)
													{
														echo GetMessage('SALE_PACKS_COUNT').': <b>'.$arResult["PACKS_COUNT"].'</b>';
													}
												} else {
													$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator','',array(
														'NO_AJAX' => $arParams['DELIVERY_NO_AJAX'],
														'DELIVERY' => $delivery_id,
														'PROFILE' => $profile_id,
														'ORDER_WEIGHT' => $arResult['ORDER_WEIGHT'],
														'ORDER_PRICE' => $arResult['ORDER_PRICE'],
														'LOCATION_TO' => $arResult['USER_VALS']['DELIVERY_LOCATION'],
														'LOCATION_ZIP' => $arResult['USER_VALS']['DELIVERY_LOCATION_ZIP'],
														'CURRENCY' => $arResult['BASE_LANG_CURRENCY'],
														'ITEMS' => $arResult['BASKET_ITEMS'],
														'EXTRA_PARAMS_CALLBACK' => $extraParams
													),
													null,
													array('HIDE_ICONS'=>'Y')
													);
												}
											?></span><?
											?><p class="note" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;submitForm();"><?
												if(strlen($arProfile["DESCRIPTION"]) > 0)
												{
													?><?=nl2br($arProfile["DESCRIPTION"])?><?
												} else {
													?><?=nl2br($arDelivery["DESCRIPTION"])?><?
												}
											?></p><?
										?></div><?
									?></td><?
								?></tr><?
							?></table><?
						?></div><?
					} // endforeach
				} else { // stores and courier
					if(count($arDelivery['STORE'])>0)
					{
						$clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery['ID']."').checked=true;fShowStore('".$arDelivery['ID']."','".$arParams['SHOW_STORES_IMAGES']."','".$width."','".SITE_ID."')\";";
					} else {
						$clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery['ID']."').checked=true;submitForm();\"";
					}
					?><div class="item clearfix"><?
						if(count($arDelivery['LOGOTIP']) > 0)
						{
							$arFileTmp = CFile::ResizeImageGet(
								$arDelivery['LOGOTIP']['ID'],
								array('width'=>'95','height'=>'55'),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);
							$deliveryImgURL = $arFileTmp['src'];
						} else {
							$deliveryImgURL = $arResult['NO_PHOTO']['src'];
						}
						?><table><?
							?><tr><?
								?><td><?
									?><input <?
										?>type="radio" <?
										?>id="ID_DELIVERY_ID_<?=$arDelivery['ID']?>" <?
										?>name="<?=htmlspecialcharsbx($arDelivery['FIELD_NAME'])?>" <?
										?>value="<?=$arDelivery['ID']?>"<?if($arDelivery['CHECKED']=='Y') echo ' checked';?> /><?
									?><label for="ID_DELIVERY_ID_<?=$arDelivery['ID']?>" <?=$clickHandler?>></label><?
								?></td><?
								?><td><?
									?><div class="img" <?=$clickHandler?>><?
										?><span style='background-image:url(<?=$deliveryImgURL?>);'></span><?
									?></div><?
								?></td><?
								?><td><?
									?><div class="data"><?
										?><div><?
											?><strong <?=$clickHandler?>><?
												?><?=htmlspecialcharsbx($arDelivery['NAME'])?><?
											?></strong><?
											if(strlen($arDelivery['PERIOD_TEXT'])>0)
											{
												?>, <?=$arDelivery['PERIOD_FROM']?>-<?=$arDelivery['PERIOD_TO']?> <?=GetMessage('SALE_DELIVERY_DAYS')?><?
											}
											?><b> &mdash; <?=$arDelivery['PRICE_FORMATED']?></b><?
										?></div><?
										?><p class="note"<?=$clickHandler?>><?
											if(strlen($arDelivery['DESCRIPTION'])>0)
											{
												?><?=$arDelivery['DESCRIPTION']?><br /><?
											}
											if(count($arDelivery['STORE'])>0)
											{
												?><span id="select_store"<?if(strlen($arResult['STORE_LIST'][$arResult['BUYER_STORE']]['TITLE'])<=0) echo ' style="display:none;"';?>><?
													?><span><?=GetMessage('SOA_ORDER_GIVE_TITLE');?>: </span><?
													?><span id="store_desc"><?=htmlspecialcharsbx($arResult['STORE_LIST'][$arResult['BUYER_STORE']]['TITLE'])?></span><?
												?></span><?
											}
										?></p><?
									?></div><?
								?></td><?
							?></tr><?
						?></table><?
					?></div><?
				}
			}
		?></div><?
	}
?></div>