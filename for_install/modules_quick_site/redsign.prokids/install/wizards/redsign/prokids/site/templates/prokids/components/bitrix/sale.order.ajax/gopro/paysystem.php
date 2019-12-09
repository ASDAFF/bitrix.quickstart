<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><script type="text/javascript">
	function changePaySystem(param)
	{
		console.log( 'changePaySystem' );
		if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
		{
			if (param == 'account')
			{
				if (BX("PAY_CURRENT_ACCOUNT"))
				{
					BX("PAY_CURRENT_ACCOUNT").checked = true;
					BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

					// deselect all other
					var el = document.getElementsByName("PAY_SYSTEM_ID");
					for(var i=0; i<el.length; i++)
						el[i].checked = false;
				}
			}
			else
			{
				BX("PAY_CURRENT_ACCOUNT").checked = false;
				BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
			}
		}
		else if (BX("account_only") && BX("account_only").value == 'N')
		{
			if (param == 'account')
			{
				if (BX("PAY_CURRENT_ACCOUNT"))
				{
					BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

					if (BX("PAY_CURRENT_ACCOUNT").checked)
					{
						BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
					else
					{
						BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
						BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
				}
			}
		}

		submitForm();
	}
</script><?

?><div class="section paysystem"><?
	?><h4><?=GetMessage('SOA_TEMPL_PAY_SYSTEM')?></h4><?
	?><div class="body"><?
		if($arResult['PAY_FROM_ACCOUNT']=='Y')
		{
			$accountOnly = ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y')?'Y':'N';
			$onclick = 'onclick="changePaySystem(\'account\');return false;"';
			?><input type="hidden" id="account_only" value="<?=$accountOnly?>" /><?
			?><div class="item clearfix"><?
				?><input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N"><?
				?><table><?
					?><tr><?
						?><td><?
							?><input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y') echo ' checked="checked"';?>><?
							?><label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" <?=$onclick?> class="<?if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y') echo ' selected'?>"></label><?
						?></td><?
						?><td><?
							?><div class="img" <?=$onclick?>><?
								?><span style="background-image:url(<?=$templateFolder?>/images/logo-default-ps.gif);"></span><?
							?></div><?
						?></td><?
						?><td><?
							?><div class="data"><?
								?><strong <?=$onclick?>><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT')?></strong><?
								?><p><?
									?><div><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT1').' <b>'.$arResult['CURRENT_BUDGET_FORMATED']?></b></div><?
									if($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y')
									{
										?><div><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT3')?></div><?
									} else {
										?><div><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT2')?></div><?
									}
								?></p><?
							?></div><?
						?></td><?
					?></tr><?
				?></table><?
			?></div><?
		}

		uasort($arResult['PAY_SYSTEM'], 'cmpBySort'); // resort arrays according to SORT value

		foreach($arResult['PAY_SYSTEM'] as $arPaySystem)
		{
			if(strlen(trim(str_replace('<br />', '', $arPaySystem['DESCRIPTION'])))>0 || intval($arPaySystem['PRICE'])>0)
			{
				if(count($arResult['PAY_SYSTEM'])==1)
				{
					$onclick = 'onclick="BX(\'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'].'\').checked=true;changePaySystem();return false;"';
					?><div class="item clearfix"><?
						?><input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem['ID']?>" /><?
						?><table><?
							?><tr><?
								?><td><?
									?><input <?
										?>type="radio" <?
										?>id="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?
										?>name="PAY_SYSTEM_ID" <?
										?>value="<?=$arPaySystem['ID']?>" <?
										if($arPaySystem['CHECKED']=='Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y')) echo ' checked="checked"';?> /><?
									?><label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?=$onclick?>></label><?
								?></td><?
								?><td><?
									if(count($arPaySystem['PSA_LOGOTIP'])>0)
									{
										$imgUrl = $arPaySystem['PSA_LOGOTIP']['SRC'];
									} else {
										$imgUrl = $templateFolder.'/images/logo-default-ps.gif';
									}
									?><div class="img" <?=$onclick?>><?
										?><span style="background-image:url(<?=$imgUrl?>);"></span><?
									?></div><?
								?></td><?
								?><td><?
									?><div class="data"><?
										if($arParams['SHOW_PAYMENT_SERVICES_NAMES']!='N')
										{
											?><strong <?=$onclick?>><?=$arPaySystem['PSA_NAME'];?></strong><?
										}
										?><p class="note" <?=$onclick?>><?
											if(intval($arPaySystem['PRICE'])>0)
											{
												echo str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($arPaySystem['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'));
											} else {
												echo $arPaySystem['DESCRIPTION'];
											}
										?></p><?
									?></div><?
								?></td><?
							?></tr><?
						?></table><?
					?></div><?
				} else { // more than one
					$onclick = 'onclick="BX(\'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'].'\').checked=true;changePaySystem();return false;"';
					?><div class="item clearfix"><?
						?><table><?
							?><tr><?
								?><td><?
									?><input <?
										?>type="radio" <?
										?>id="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?
										?>name="PAY_SYSTEM_ID" <?
										?>value="<?=$arPaySystem['ID']?>" <?
										if($arPaySystem['CHECKED']=='Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y')) echo ' checked="checked"';?> /><?
									?><label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?=$onclick?>></label><?
								?></td><?
								?><td><?
									if(count($arPaySystem['PSA_LOGOTIP'])>0)
									{
										$imgUrl = $arPaySystem['PSA_LOGOTIP']['SRC'];
									} else {
										$imgUrl = $templateFolder.'/images/logo-default-ps.gif';
									}
									?><div class="img" <?=$onclick?>><?
										?><span style='background-image:url(<?=$imgUrl?>);'></span><?
									?></div><?
								?></td><?
								?><td><?
									?><div class="data"><?
										if($arParams['SHOW_PAYMENT_SERVICES_NAMES']!='N')
										{
											?><strong <?=$onclick?>><?=$arPaySystem['PSA_NAME'];?></strong><?
										}
										?><p class="note" <?=$onclick?>><?
											if(IntVal($arPaySystem['PRICE'])>0)
											{
												echo str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($arPaySystem['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'));
											} else {
												echo $arPaySystem['DESCRIPTION'];
											}
										?></p><?
									?></div><?
								?></td><?
							?></tr><?
						?></table><?
					?></div><?
				}
			}

			if(strlen(trim(str_replace('<br />', '', $arPaySystem['DESCRIPTION'])))==0 && IntVal($arPaySystem['PRICE'])==0)
			{
				if(count($arResult['PAY_SYSTEM'])==1)
				{
					$onclick = 'onclick="BX(\'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'].'\').checked=true;changePaySystem();return false;"';
					?><div class="item clearfix"><?
						?><input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem['ID']?>"><?
						?><table><?
							?><tr><?
								?><td><?
									?><input <?
										?>type="radio" <?
										?>id="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?
										?>name="PAY_SYSTEM_ID" <?
										?>value="<?=$arPaySystem['ID']?>" <?
										if($arPaySystem['CHECKED']=='Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y')) echo ' checked="checked"';?> /><?
									?><label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?=$onclick?>></label><?
								?></td><?
								?><td><?
									if(count($arPaySystem['PSA_LOGOTIP'])>0)
									{
										$imgUrl = $arPaySystem['PSA_LOGOTIP']['SRC'];
									} else {
										$imgUrl = $templateFolder.'/images/logo-default-ps.gif';
									}
									?><div class="img" <?=$onclick?>><?
										?><span style='background-image:url(<?=$imgUrl?>);'></span><?
									?></div><?
								?></td><?
								?><td><?
									if($arParams['SHOW_PAYMENT_SERVICES_NAMES']!='N')
									{
										?><div class="data" <?=$onclick?>><?
											?><strong><?=$arPaySystem['PSA_NAME'];?></strong><?
										?></div><?
									}
								?></td><?
							?></tr><?
						?></table><?
					?></div><?
				} else { // more than one
					$onclick = 'onclick="BX(\'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'].'\').checked=true;changePaySystem();return false;"';
					?><div class="item clearfix"><?
						?><table><?
							?><tr><?
								?><td><?
									?><input <?
										?>type="radio" <?
										?>id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" <?
										?>name="PAY_SYSTEM_ID" <?
										?>value="<?=$arPaySystem["ID"]?>" <?
										if($arPaySystem['CHECKED']=='Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y')) echo ' checked="checked"';?> /><?
									?><label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>" <?=$onclick?>></label><?
								?></td><?
								?><td><?
									if(count($arPaySystem['PSA_LOGOTIP'])>0)
									{
										$imgUrl = $arPaySystem['PSA_LOGOTIP']['SRC'];
									} else {
										$imgUrl = $templateFolder.'/images/logo-default-ps.gif';
									}
									?><div class="img" <?=$onclick?>><?
										?><span style='background-image:url(<?=$imgUrl?>);'></span><?
									?></div><?
								?></td><?
								?><td><?
									if($arParams['SHOW_PAYMENT_SERVICES_NAMES']!='N')
									{
										?><div class="data"><?
											?><strong <?=$onclick?>><?
												if($arParams['SHOW_PAYMENT_SERVICES_NAMES']!='N')
												{
													?><?=$arPaySystem['PSA_NAME'];?><?
												} else {
													?><?='&nbsp;'?><?
												}
											?></strong><?
										?></div><?
									}
								?></td><?
							?></tr><?
						?></table><?
					?></div><?
				}
			}
		}
	?></div><?
?></div>