<?
use Bitrix\Main\Localization\Loc;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
//PShowWaitMessage('wait_container_postcalc_30', true);
		function changePaySystem(param)
		{
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
		
		function calculatePaymentCount(n)
		{
			$("input[name=payment_count]").val(n);	
		}
</script>
<?
$showCount = $arParams["ORDER_ITEM_SHOW_COUNT"];
$count = 0;
$bool = false;
if(isset($_REQUEST["payment_count"]) && $_REQUEST["payment_count"]>0 && $showCount<$_REQUEST["payment_count"])
	$showCount = $_REQUEST["payment_count"];	
?>
<input name="payment_count" value="<?=isset($_REQUEST["payment_count"])?$_REQUEST["payment_count"]:0?>" type="hidden" />
<div class="col-sm-12 <?if(isset($d2p)):?>sm-padding-right-no<?else:?>sm-padding-left-no<?endif?>">
	<div class="section block_payment js_radio">
		<div class="section_title">
			<div class="section_title_in">
				<span class="desc_fly_1_bg"><?=Loc::getMessage("MS_ORDER_PAYMENT")?></span>
			</div>
		</div>
		<?php 
		if ($arResult["PAY_FROM_ACCOUNT"] == "Y")
		{
			$accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N";
			?>
			<input type="hidden" id="account_only" value="<?=$accountOnly?>" />
			<div class="wrap_item_payment">
				<div class="bx_element">
					<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
					<label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" onclick="changePaySystem('account');" class="<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo "selected"?>">
						<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
						
						<div class="label_content">
								<div class="block_img_wrap">
									<div class="block_img">
										<?
										if ($arResult["INNER_PAY_SYSTEM"]['LOGOTIP']["SRC"])
										{
											$imgUrl = $arResult["INNER_PAY_SYSTEM"]['LOGOTIP']["SRC"];
										}
										else
										{
											$imgUrl = $templateFolder."/images/logo-default-ps.gif";
										}
										?>
										<img class="img-responsive" src="<?=$imgUrl?>" height="50px"  alt=""/>
									</div>
								</div>
								<div class="block_text_wrap">
									<div class="block_text">
										<h3 class="title"><?=Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT")?></h3>
										<p class="payment_text">
											<? echo Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT1"), ' <b>',$arResult["CURRENT_BUDGET_FORMATED"];?></b><br>
											<?if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
											{
												echo Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT3");
											}
											else
											{
												echo Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT2");
											}?>
										</p>
									</div> <!--end block_text-->
								</div>
						</div>
					</label>
				</div>
			</div>
			<?
			$count = 1;
		}
		foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
		{
			if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) > 0 || intval($arPaySystem["PRICE"]) > 0)
			{
				if (count($arResult["PAY_SYSTEM"]) == 1)
				{
					$count++;
					if($count>$showCount && !$bool)
					{
						$bool = true;
					?>
						<div id="close_payment" class="close_block">
					<?
					}
					?>
					<div class="wrap_item_payment">
						<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
						<input type="radio"
							id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
							name="PAY_SYSTEM_ID"
							value="<?=$arPaySystem["ID"]?>"
							<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
							onclick="calculatePaymentCount(<?=$count?>);changePaySystem();"
						/>
						<label class="item_payment <?if($arPaySystem["CHECKED"] == "Y"):?>label-active<?endif;?>" onclick="BX('ID_PAY_SYSTEM_ID_1').checked=true;calculatePaymentCount(<?=$count?>);changePaySystem();" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>">
							<div class="label_content">
								<div class="block_img_wrap">
									<div class="block_img">
										<?
										if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
											$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
										else:
											$imgUrl = $templateFolder."/images/logo-default-ps.gif";
										endif;
										?>
										<img class="img-responsive" src="<?=$imgUrl?>" width="23px" height="auto"  alt=""/>
									</div>
								</div>
								<div class="block_text_wrap">
									<div class="block_text">
										<h3 class="title"><?=$arPaySystem["PSA_NAME"];?></h3>
										<p class="payment_text">
										<?
										if (intval($arPaySystem["PRICE"]) > 0)
											echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), Loc::getMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
										else
											echo $arPaySystem["DESCRIPTION"];
										?>
										</p>
									</div> <!--end block_text-->
								</div>
							</div>
						</label>
					</div> <!--end wrap_item_payment -->
					<?
				}else{
					$count++;
					if($count>$showCount && !$bool)
					{
						$bool = true;
					?>
						<div id="close_payment" class="close_block">
					<?
					}
					?>
					<div class="wrap_item_payment">
						<input type="radio"
							id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
							name="PAY_SYSTEM_ID"
							value="<?=$arPaySystem["ID"]?>"
							<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
							onclick="calculatePaymentCount(<?=$count?>);changePaySystem();"
						/>
						<label class="item_payment <?if($arPaySystem["CHECKED"] == "Y"):?>label-active<?endif;?>" onclick="BX('ID_PAY_SYSTEM_ID_1').checked=true;calculatePaymentCount(<?=$count?>);changePaySystem();" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>">
							<div class="label_content">
								<div class="block_img_wrap">
									<div class="block_img">
										<?
										if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
											$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
										else:
											$imgUrl = $templateFolder."/images/logo-default-ps.gif";
										endif;
										?>
										<img class="img-responsive" src="<?=$imgUrl?>" width="23px" height="auto"  alt=""/>
									</div>
								</div>
								<div class="block_text_wrap">
									<div class="block_text">
										<h3 class="title"><?=$arPaySystem["PSA_NAME"];?></h3>
										<p class="payment_text">
										<?
										if (intval($arPaySystem["PRICE"]) > 0)
											echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), Loc::getMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
										else
											echo $arPaySystem["DESCRIPTION"];
										?>
										</p>
									</div>
								</div>
							</div>
						</label>
					</div>
					<?
				}
			}

			if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) == 0 && intval($arPaySystem["PRICE"]) == 0)
			{
				if (count($arResult["PAY_SYSTEM"]) == 1)
				{
					$count++;
					if($count>$showCount && !$bool)
					{
						$bool = true;
					?>
						<div id="close_payment" class="close_block">
					<?
					}
					?>
					<div class="wrap_item_payment">
						<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
						<input type="radio"
							id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
							name="PAY_SYSTEM_ID"
							value="<?=$arPaySystem["ID"]?>"
							<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
							onclick="calculatePaymentCount(<?=$count?>);changePaySystem();"
						/>
						<label class="item_payment <?if($arPaySystem["CHECKED"] == "Y"):?>label-active<?endif;?>" onclick="BX('ID_PAY_SYSTEM_ID_1').checked=true;calculatePaymentCount(<?=$count?>);changePaySystem();" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>">
							<div class="label_content">
								<div class="block_img_wrap">
									<div class="block_img">
										<?
										if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
											$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
										else:
											$imgUrl = $templateFolder."/images/logo-default-ps.gif";
										endif;
										?>
										<img class="img-responsive" src="<?=$imgUrl?>" width="23px" height="auto"  alt=""/>
									</div>
								</div>
								<div class="block_text_wrap">
									<div class="block_text">
										<h3 class="title"><?=$arPaySystem["PSA_NAME"];?></h3>
									</div>
								</div>
							</div>
						</label>
					</div>
					<?
				}else{
					$count++;
					if($count>$showCount && !$bool)
					{
						$bool = true;
					?>
						<div id="close_payment" class="close_block">
					<?
					}
					?>
					<div class="wrap_item_payment">
						<input type="radio"
							id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
							name="PAY_SYSTEM_ID"
							value="<?=$arPaySystem["ID"]?>"
							<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
							onclick="calculatePaymentCount(<?=$count?>);changePaySystem();"
						/>
						<label class="item_payment <?if($arPaySystem["CHECKED"] == "Y"):?>label-active<?endif;?>" onclick="BX('ID_PAY_SYSTEM_ID_1').checked=true;calculatePaymentCount(<?=$count?>);changePaySystem();" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>">
							<div class="label_content">
								<div class="block_img_wrap">
									<div class="block_img">
										<?
										if (count($arPaySystem["PSA_LOGOTIP"]) > 0)
										{
											$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
										}
										else
										{
											$imgUrl = $templateFolder."/images/logo-default-ps.gif";
										}
										?>
										<img class="img-responsive" src="<?=$imgUrl?>" width="23px" height="auto"  alt=""/>
									</div>
								</div>
								<div class="block_text_wrap">
									<div class="block_text">
										<h3 class="title"><?=$arPaySystem["PSA_NAME"];?></h3>
									</div>
								</div>
							</div>
						</label>
					</div>
					<?
				}
			}
		} 
		if($count>$showCount)
		{
			?>
			</div>
			<div class="wrap_block_btn">
				<span class="display_close_block close" onclick="open_close_block(this, '#close_payment')">
					<span class="first"><?=Loc::getMessage("MS_ORDER_SHOW")?></span>
					<span class="second"><?=Loc::getMessage("MS_ORDER_HIDE")?></span>
				</span>
			</div>
			<?
		}?>
	</div>
</div>
