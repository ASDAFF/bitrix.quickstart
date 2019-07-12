<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?




$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);






?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>


<div class="p-block pb-catalog">


						<ul id="c-list">

							<?foreach($arResult["ITEMS"] as $cell=>$arElement){?>
							<?

//                            print_r($arElement); die();

							$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
							?>

							<li>
								<div class="dataitem-<?=$arElement["ID"]?> item<?if(!(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) && !$arElement["CAN_BUY"]):?> unavailable<?endif?>">
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
										<span class="img">
											<?if (!empty($arElement['PROPERTIES']['NEWPRODUCT']['VALUE']) || ($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] < $arElement["MIN_PRODUCT_PRICE"] && $arElement["MIN_PRODUCT_PRICE"] > 0 && $arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0) ): ?>
												<div class="lbls">
													<?if ($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] < $arElement["MIN_PRODUCT_PRICE"] && $arElement["MIN_PRODUCT_PRICE"] > 0 && $arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0): ?><span class="lbl perc">-<?= _emisc::pf(100 - $arElement["MIN_PRODUCT_DISCOUNT_PRICE"]/$arElement["MIN_PRODUCT_PRICE"]*100) ?>%</span><? endif; ?>
													<?if (!empty($arElement['PROPERTIES']['NEWPRODUCT']['VALUE'])): ?><span class="lbl news"></span><? endif; ?>
												</div>
											<? endif; ?>

											<?if(is_array($arElement["PREVIEW_IMG"])){?><img src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" alt="" /><?}?>

											<?if (!empty($arElement['PROPERTIES']['SALELEADER']['VALUE'])): ?><span class="lbl hit"></span><? endif; ?>
										</span>
										<span class="lnk"><span><?=$arElement["NAME"]?></span></span>
									</a>
									<small><?=GetMessage('ARTICUL')?> <?=$arElement['PROPERTIES']["ARTNUMBER"]['VALUE'];?></small>

									<?if($arElement["CAN_BUY"]){?>
										<?if ($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] < $arElement["MIN_PRODUCT_PRICE"] && $arElement["MIN_PRODUCT_PRICE"] > 0 && $arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0):?>
											<span class="price">
												<span class="p-wrap">
													<?if($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0):?>
														<strong pprice="<?= $arElement["MIN_PRODUCT_DISCOUNT_PRICE"];?>" itemprop = "price"><?= $arElement["MIN_PRODUCT_DISCOUNT_PRICE"];?> <span class="rubl">A</span></strong>
														<span class="pay"><a rel="id<?= $arElement["ID"]; ?>"><?=GetMessage('CATALOG_ADD')?></a></span>
													<?endif?>
												</span>
											</span>
											<br />
											<? /*<span class="quick-pay" onclick="getPopup('quick-pay', this, true)">Быстрая покупка</span>*/ ?>
											<s><?if($arElement["MIN_PRODUCT_PRICE"] > 0) echo $arElement["MIN_PRODUCT_PRICE"];?> <span class="rubl">A</span></s>
										<?elseif ($arElement["MIN_PRODUCT_PRICE"] > 0):?>
											<span class="price">
												<span class="p-wrap">
													<?if($arElement["MIN_PRODUCT_PRICE"] > 0): ?>
														<strong pprice="<?= $arElement["MIN_PRODUCT_PRICE"];?>" itemprop = "price"><?= $arElement["MIN_PRODUCT_PRICE"];?> <span class="rubl">A</span></strong>
														<span class="pay"><a rel="id<?= $arElement["ID"]; ?>"><?=GetMessage('CATALOG_ADD')?></a></span>
													<? endif;?>
												</span>
											</span>
											<br />
											<? /*<span class="quick-pay" onclick="getPopup('quick-pay', this, true)">Быстрая покупка</span>*/ ?>
										<?endif?>
									<?}?>


								</div>
							</li>



						<?}?>


						</ul>








<?if(sizeof($arResult["ITEMS"]) < 1){
	?><?=GetMessage('CATALOG_NOT_FOUND')?><?
}?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>



</div>

<div class="clear"></div>



<? if(!empty($arResult["DESCRIPTION"])&&(empty($_REQUEST['PAGEN_1'])||(!empty($_REQUEST['PAGEN_1'])&&$_REQUEST['PAGEN_1']=='1'))): ?>

	<div id="i-text">
		<h1><?= (!empty($arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"]) ? $arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"] : $arResult["NAME"]); ?></h1>
		<?= ($arResult['DESCRIPTION_TYPE'] == 'text' ? nl2br($arResult["DESCRIPTION"]) : $arResult["DESCRIPTION"]); ?>
	</div>

<? endif; ?>

