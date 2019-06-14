<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent         $component
 *
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $result
 *
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $parentTemplateFolder
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

//$this - объект шаблона
//$component - объект компонента

//$this->GetFolder()
//$tplId = $this->GetEditAreaId($component->randString());

//Объект родительского компонента
//$parent = $component->getParent();
//$parentPath = $parent->getPath();

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!function_exists("getColumnName")) {
	function getColumnName($arHeader)
	{
		return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : Loc::getMessage("SALE_" . $arHeader["id"]);
	}
}

if(!function_exists("getBasketBlockTitle")) {
	function getBasketBlockTitle($mess,$count = 0)
	{
		if($count == 1)
			$goods = Loc::getMessage('MESS_BASKET_BLOCK_GOODS_1');
		elseif($count == 0 || $count >= 5)
			$goods = Loc::getMessage('MESS_BASKET_BLOCK_GOODS_5');
		else
			$goods = Loc::getMessage('MESS_BASKET_BLOCK_GOODS_2');

		$mess = str_replace('#COUNT#',$count,$mess);
		$mess = str_replace('#GOODS#',$goods,$mess);

		return $mess;
	}
}


$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();
//$scheme = $request->isHttps() ? 'https' : 'http';

//plugins
$this->addExternalCss($templateFolder . '/plugins/modal/api.modal.css');
//$this->addExternalCss($templateFolder . '/plugins/form/api.form.css');
//$this->addExternalCss($templateFolder . '/plugins/button/api.button.css');
$this->addExternalJs($templateFolder . '/plugins/modal/api.modal.js');


if(strlen($request->get('ORDER_ID')) > 0) {
	include($server->getDocumentRoot() . $templateFolder . '/confirm.php');
}
elseif($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
	include($server->getDocumentRoot() . $templateFolder . '/empty.php');
}
else {
	$result = $arResult['JS_DATA'];

	$countGridRows = count($result['GRID']['ROWS']);

//	$tttfile = dirname(__FILE__) . '/1_txt.php';
//	file_put_contents($tttfile, "<pre>" . print_r($result, 1) . "</pre>\n");

	?>
	<div id="API_CHECKOUT" class="api_checkout">
		<div class="api_alert"></div>
		<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" class="api_form" enctype="multipart/form-data">
			<?
			echo bitrix_sessid_post();
			if(strlen($arResult['PREPAY_ADIT_FIELDS']) > 0) {
				echo $arResult['PREPAY_ADIT_FIELDS'];
			}
			?>
			<!--<input type="hidden" name="json" value="Y">-->
			<!--<input type="hidden" name="via_ajax" value="Y">-->
			<!--<input type="hidden" name="is_ajax_post" value="Y">-->
			<!--<input type="hidden" name="confirmorder" value="Y">-->

			<input type="hidden" name="action" value="saveOrderAjax">
			<input type="hidden" name="location_type" value="code">
			<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>">

			<? if($result['IS_AUTHORIZED']): ?>
				<? if($result['LAST_ORDER_DATA']['PERSON_TYPE']): ?>
					<input type="hidden" name="PERSON_TYPE" value="<?=$result['LAST_ORDER_DATA']['PERSON_TYPE']?>">
				<? endif ?>
				<? if($result['LAST_ORDER_DATA']['DELIVERY']): ?>
					<input type="hidden" name="DELIVERY" value="<?=$result['LAST_ORDER_DATA']['DELIVERY']?>">
				<? endif ?>
				<? if($result['LAST_ORDER_DATA']['PAY_SYSTEM']): ?>
					<input type="hidden" name="DELIVERY" value="<?=$result['LAST_ORDER_DATA']['PAY_SYSTEM']?>">
				<? endif ?>
				<? if($result['LAST_ORDER_DATA']['PICK_UP']): ?>
					<input type="hidden" name="DELIVERY" value="<?=$result['LAST_ORDER_DATA']['PICK_UP']?>">
				<? endif ?>
			<? endif ?>

			<div class="api_block api_block_basket">
				<div class="api_block_title">
					<div class="api_js_basket_toggle">
						<?=getBasketBlockTitle($arParams["~MESS_BASKET_BLOCK_TITLE"], $result['COUNT_BASKET_ITEMS'])?>
						<?if($countGridRows>=3):?>
							<span class="api_link"><?=$arParams['~MESS_BASKET_SHOW']?></span>
						<?endif?>
					</div>
				</div>
				<div class="api_block_content <?if($countGridRows>=3):?>api_hidden<?endif?>">
					<? if($result['GRID']['ROWS']): ?>
						<table class="api_table">
							<thead>
							<tr>
								<?
								$bPropsColumn    = false;
								$bUseDiscount    = false;
								$bPriceType      = false;
								$bPreviewPicture = false;
								$bDetailPicture  = false;
								$imgCount        = 0;
								?>
								<? foreach($result['GRID']['HEADERS'] as $id => $arHeader): ?>
									<?
									if ($arHeader["id"] == "PROPS")
										$bPropsColumn = true;

									if ($arHeader["id"] == "NOTES")
										$bPriceType = true;

									if ($arHeader["id"] == "PREVIEW_PICTURE")
										$bPreviewPicture = true;

									if ($arHeader["id"] == "DETAIL_PICTURE")
										$bDetailPicture = true;

									if(in_array($arHeader["id"], array("PROPS", "TYPE", "NOTES"))) // some values are not shown in columns in this template
										continue;
									?>
									<th class="api_th_<?=ToLower($arHeader['id'])?>"><?=$arHeader['name']?></th>
								<? endforeach; ?>
							</tr>
							</thead>
							<tbody>
							<? foreach($result['GRID']['ROWS'] as $k => $arData): ?>
								<tr>
									<? foreach($result['GRID']['HEADERS'] as $id => $arColumn): ?>
										<?
										if(in_array($arColumn["id"], array("PROPS", "TYPE", "NOTES"))) // some values are not shown in columns in this template
											continue;

										$arItem = (isset($arData["columns"][ $arColumn["id"] ])) ? $arData["columns"] : $arData["data"];
										?>
										<td class="api_td_<?=ToLower($arColumn['id'])?>">
											<?
											if($arColumn["id"] == "PICTURE"):
												?>
												<?
												if(strlen($arData["data"]["PREVIEW_PICTURE_SRC"]) > 0):
													$url = $arData["data"]["PREVIEW_PICTURE_SRC"];
												elseif(strlen($arData["data"]["DETAIL_PICTURE_SRC"]) > 0):
													$url = $arData["data"]["DETAIL_PICTURE_SRC"];
												else:
													$url = $templateFolder . "/images/no_photo.png";
												endif;
												if(strlen($arData["data"]["DETAIL_PAGE_URL"]) > 0):?>
													<a href="<?=$arData["data"]["DETAIL_PAGE_URL"]?>">
														<img src="<?=$url?>" alt="">
													</a>
												<? else: ?>
													<img src="<?=$url?>" alt="">
												<? endif; ?>
												<?
											elseif($arColumn["id"] == "NAME"):
												?>
												<div class="api_basket_item_name">
													<? if(strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?>
														<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
													<? else: ?>
														<?=$arItem["NAME"]?>
													<? endif; ?>
												</div>
												<? if($bPropsColumn): ?>
													<div class="api_basket_props">
														<? foreach($arItem["PROPS"] as $val):?>
															<div class="api_prop">
																<span class="api_prop_name"><?=$val["NAME"]?>:</span>
																<span class="api_prop_value"><?=$val["VALUE"]?><span>
															</div>
														<?endforeach; ?>
													</div>
												<? endif; ?>
												<? if(is_array($arItem["SKU_DATA"])): ?>
													<? foreach($arItem["SKU_DATA"] as $propId => $arProp): ?>
														<?
														// is image property
														$isImgProperty = false;
														foreach($arProp["VALUES"] as $id => $arVal) {
															if(isset($arVal["PICT"]) && !empty($arVal["PICT"])) {
																$isImgProperty = true;
																break;
															}
														}

														$full = (count($arProp["VALUES"]) > 5) ? "full" : "";
														// iblock element relation property
														?>
														<? if($isImgProperty): ?>
															<div class="bx_item_detail_scu_small_noadaptive <?=$full?>">
																<span class="bx_item_section_name_gray"><?=$arProp["NAME"]?>:</span>
																<div class="bx_scu_scroller_container">
																	<div class="bx_scu">
																		<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>" style="width: 200%;margin-left:0%;">
																			<?
																			foreach($arProp["VALUES"] as $valueId => $arSkuValue):

																				$selected = "";
																				foreach($arItem["PROPS"] as $arItemProp):
																					if($arItemProp["CODE"] == $arItem["SKU_DATA"][ $propId ]["CODE"]) {
																						if($arItemProp["VALUE"] == $arSkuValue["NAME"])
																							$selected = "class=\"bx_active\"";
																					}
																				endforeach;
																				?>
																				<li style="width:10%;" <?=$selected?>>
																					<a href="javascript:void(0);">
																						<span style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>)"></span>
																					</a>
																				</li>
																				<?
																			endforeach;
																			?>
																		</ul>
																	</div>
																	<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
																	<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
																</div>
															</div>
														<? else: ?>
															<div class="bx_item_detail_size_small_noadaptive <?=$full?>">
																<span class="bx_item_section_name_gray"><?=$arProp["NAME"]?>:</span>
																<div class="bx_size_scroller_container">
																	<div class="bx_size">
																		<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>" style="width: 200%; margin-left:0%;">
																			<?
																			foreach($arProp["VALUES"] as $valueId => $arSkuValue):
																				$selected = "";
																				foreach($arItem["PROPS"] as $arItemProp):
																					if($arItemProp["CODE"] == $arItem["SKU_DATA"][ $propId ]["CODE"]) {
																						if($arItemProp["VALUE"] == $arSkuValue["NAME"])
																							$selected = "class=\"bx_active\"";
																					}
																				endforeach;
																				?>
																				<li style="width:10%;" <?=$selected?>>
																					<a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
																				</li>
																				<?
																			endforeach;
																			?>
																		</ul>
																	</div>
																	<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
																	<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
																</div>
															</div>
														<? endif; ?>
													<? endforeach; ?>
												<? endif; ?>
												<?
											elseif($arColumn["id"] == "PRICE_FORMATED"):
												?>
												<div class="current_price"><?=$arItem["PRICE_FORMATED"]?></div>
												<div class="old_price right">
													<?
													if(doubleval($arItem["DISCOUNT_PRICE"]) > 0):
														echo SaleFormatCurrency($arItem["PRICE"] + $arItem["DISCOUNT_PRICE"], $arItem["CURRENCY"]);
														$bUseDiscount = true;
													endif;
													?>
												</div>
												<? if($bPriceType && strlen($arItem["NOTES"]) > 0): ?>
												<div style="text-align: left">
													<div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
													<div class="type_price_value"><?=$arItem["NOTES"]?></div>
												</div>
											<? endif; ?>
												<?
											elseif($arColumn["id"] == "DISCOUNT"):
												?>
												<span><?=getColumnName($arColumn)?>:</span>
												<?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
												<?
											elseif(in_array($arColumn["id"], array("QUANTITY", "WEIGHT_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED", "SUM"))):
												?>
												<div class="custom right">
													<span class="api_hidden"><?=getColumnName($arColumn)?>:</span>
													<?=$arItem[ $arColumn["id"] ]?>
												</div>
												<?
											else: // some property value
												if(is_array($arItem[ $arColumn["id"] ])):
													foreach($arItem[ $arColumn["id"] ] as $arValues)
														if($arValues["type"] == "image")
															$columnStyle = "width:20%";
													?>
													<div class="custom" style="<?=$columnStyle?>">
														<span class="api_hidden"><?=getColumnName($arColumn)?>:</span>
														<?
														foreach($arItem[ $arColumn["id"] ] as $arValues):
															if($arValues["type"] == "image"):
																?>
																<div class="bx_ordercart_photo_container">
																	<div class="bx_ordercart_photo" style="background-image:url('<?=$arValues["value"]?>')"></div>
																</div>
																<?
															else: // not image
																echo $arValues["value"] . "<br/>";
															endif;
														endforeach;
														?>
													</div>
													<?
												else: // not array, but simple value
													?>
													<div class="custom" style="<?=$columnStyle?>">
														<span class="api_hidden"><?=getColumnName($arColumn)?>:</span>
														<?
														echo $arItem[ $arColumn["id"] ];
														?>
													</div>
													<?
												endif;
											endif;
											?>
										</td>
									<? endforeach; ?>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					<? endif ?>
				</div>
			</div>

			<div class="api_hidden">
				<select name="PERSON_TYPE">
					<? if($result['PERSON_TYPE']): ?>
						<? foreach($result['PERSON_TYPE'] as $arPerson): ?>
							<option value="<?=$arPerson['ID']?>"<?=($arPerson['CHECKED'] == 'Y' ? ' selected=""' : '')?>>[<?=$arPerson['ID']?>] <?=$arPerson['NAME']?></option>
						<? endforeach; ?>
					<? endif; ?>
				</select>
			</div>
			<div class="api_hidden">
				<select name="PROFILE_ID">
					<option value="0">Новый профиль</option>
					<? if($result['USER_PROFILES']): ?>
						<? foreach($result['USER_PROFILES'] as $arProfile): ?>
							<option value="<?=$arProfile['ID']?>"<?=($arProfile['CHECKED'] == 'Y' ? ' selected=""' : '')?>>[<?=$arProfile['ID']?>] <?=$arProfile['NAME']?></option>
						<? endforeach; ?>
					<? endif; ?>
				</select>
			</div>
			<div class="api_hidden">
				<select name="DELIVERY_ID">
					<? if($result['DELIVERY']): ?>
						<? foreach($result['DELIVERY'] as $arDelivery): ?>
							<option value="<?=$arDelivery['ID']?>"<?=($arDelivery['CHECKED'] == 'Y' ? ' selected=""' : '')?>>[<?=$arDelivery['ID']?>] <?=$arDelivery['NAME']?></option>
						<? endforeach; ?>
					<? endif; ?>
				</select>
			</div>
			<div class="api_hidden">
				<select name="PAY_SYSTEM_ID">
					<? if($result['PAY_SYSTEM']): ?>
						<? foreach($result['PAY_SYSTEM'] as $arPaySystem): ?>
							<option value="<?=$arPaySystem['ID']?>"<?=($arPaySystem['CHECKED'] == 'Y' ? ' selected=""' : '')?>>[<?=$arPaySystem['ID']?>] <?=$arPaySystem['NAME']?></option>
						<? endforeach; ?>
					<? endif; ?>
				</select>
			</div>

			<div class="api_block api_block_order_prop">
				<div class="api_block_title"><?=$arParams['~MESS_ORDER_PROP_BLOCK_TITLE']?></div>
				<div class="api_block_content">
					<? if($result['ORDER_PROP']['properties']): ?>
						<? foreach($result['ORDER_PROP']['properties'] as $arProp): ?>
							<div class="api_row api_clearfix">
								<div class="api_label"><?=$arProp['NAME']?><?=($arProp['REQUIRED'] == 'Y' ? '<span class="api_req">*</span>' : '')?></div>
								<div class="api_controls">
									<div class="api_control">
										<? if($arProp['TYPE'] == 'LOCATION'): ?>
											<? $APPLICATION->IncludeComponent(
												 "bitrix:sale.location.selector.". $arParams['TEMPLATE_LOCATION'],
												 "",
												 Array(
														"CACHE_TIME"                 => "36000000",
														"CACHE_TYPE"                 => "A",
														"PROVIDE_LINK_BY"            => "code",
														"INPUT_NAME"                 => 'ORDER_PROP_' . $arProp['ID'],
														"CODE"                       => (isset($arProp['VALUE'][0]) ? $arProp['VALUE'][0] : $arProp['DEFAULT_VALUE']),
														"FILTER_BY_SITE"             => SITE_ID,
														"FILTER_SITE_ID"             => "current",
														"ID"                         => "",
														"INITIALIZE_BY_GLOBAL_EVENT" => "",
														"SHOW_DEFAULT_LOCATIONS"     => "Y",
														"SUPPRESS_ERRORS"            => "Y"
														//"JS_CALLBACK" => "submitFormProxy",
														//"JS_CONTROL_GLOBAL_ID" => "soa_deferred",
												 ),
												 null,
												 Array('HIDE_ICONS' => 'Y')
											); ?>
										<? else: ?>
											<? if($arProp['MULTIPLE'] == 'Y'): ?>
												<?
												for($key = 0; $key <= count($arProp['VALUE']); $key++):
													?>
													<? if($arProp['MULTILINE'] == 'Y'): ?>
													<textarea name="ORDER_PROP_<?=$arProp['ID']?>[<?=$key?>]"
													          id="ORDER_PROP_<?=$arProp['ID']?>_<?=$key?>"
													          class="api_field ORDER_PROP_<?=$arProp['CODE']?>"
													          placeholder="<?=CUtil::JSEscape($arProp['DESCRIPTION'])?>" data-autoresize><?=$arProp['VALUE'][ $key ]?></textarea>
												<? else: ?>
													<input type="text"
													       name="ORDER_PROP_<?=$arProp['ID']?>[<?=$key?>]"
													       id="ORDER_PROP_<?=$arProp['ID']?>_<?=$key?>"
													       class="api_field ORDER_PROP_<?=$arProp['CODE']?>"
													       placeholder="<?=CUtil::JSEscape($arProp['DESCRIPTION'])?>" value="<?=$arProp['VALUE'][ $key ]?>">
												<? endif ?>
												<? endfor; ?>
											<? else: ?>
												<? if($arProp['MULTILINE'] == 'Y'): ?>
													<textarea name="ORDER_PROP_<?=$arProp['ID']?>"
													          id="ORDER_PROP_<?=$arProp['ID']?>"
													          class="api_field ORDER_PROP_<?=$arProp['CODE']?>"
													          placeholder="<?=CUtil::JSEscape($arProp['DESCRIPTION'])?>" data-autoresize><?=$arProp['VALUE'][0]?></textarea>
												<? else: ?>
													<input type="text"
													       name="ORDER_PROP_<?=$arProp['ID']?>"
													       id="ORDER_PROP_<?=$arProp['ID']?>"
													       class="api_field ORDER_PROP_<?=$arProp['CODE']?>"
													       placeholder="<?=CUtil::JSEscape($arProp['DESCRIPTION'])?>" value="<?=$arProp['VALUE'][0]?>">
												<? endif ?>
											<? endif ?>
										<? endif ?>
										<div class="api_field_alert"></div>
									</div>
								</div>
							</div>
						<? endforeach; ?>
						<div class="api_row api_js_prop_comment_toggle api_hidden">
							<div class="api_label"><?=$arParams['~MESS_PROP_COMMENT']?></div>
							<div class="api_controls">
								<div class="api_control">
									<textarea name="ORDER_DESCRIPTION" class="api_field" data-autoresize><?=$arResult['ORDER_DESCRIPTION']?></textarea>
								</div>
							</div>
						</div>
					<? endif ?>
				</div>
			</div>
			<div class="api_block api_block_summary">
				<div class="api_left">
					<div class="api_cost">
						<span class="api_text"><?=$arParams['~MESS_COST_TEXT']?></span>
						<span class="api_summ"><?=$arResult['ORDER_TOTAL_PRICE_FORMATED']?></span>
					</div>
					<? if($arParams['MESS_PRIVACY_POLICY']): ?>
						<div class="api_privacy_policy"><?=$arParams['~MESS_PRIVACY_POLICY']?></div>
					<? endif ?>
					<? if($arParams['BASKET_URL']): ?>
						<div class="api_back_link">
							<a href="<?=$arParams['~BASKET_URL']?>"><?=$arParams['~MESS_BACK_LINK']?></a>
						</div>
					<? endif ?>
				</div>
				<div class="api_right">
					<div class="api_submit">
						<button type="submit" class="api_button api_button_yellow"><?=$arParams['~MESS_SUBMIT_TEXT_DEFAULT']?></button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script>
		jQuery(document).ready(function ($) {
			$.fn.apiCheckout({
				message: {
					mess_submit_text_default: '<?=CUtil::JSEscape($arParams['~MESS_SUBMIT_TEXT_DEFAULT'])?>',
					mess_submit_text_ajax: '<?=CUtil::JSEscape($arParams['~MESS_SUBMIT_TEXT_AJAX'])?>',
					mess_basket_show: '<?=CUtil::JSEscape($arParams['~MESS_BASKET_SHOW'])?>',
					mess_basket_hide: '<?=CUtil::JSEscape($arParams['~MESS_BASKET_HIDE'])?>',
					mess_prop_comment_link: '<?=CUtil::JSEscape($arParams['~MESS_PROP_COMMENT_LINK'])?>',
				}
			});
		});

		<? if ($arParams['USE_YM_GOALS'] === 'Y'): ?>
		(function bx_counter_waiter(i) {
			i = i || 0;
			if (i > 50)
				return;

			if (typeof window['yaCounter<?=$arParams['YM_GOALS_COUNTER']?>'] !== 'undefined')
				BX.Sale.OrderAjaxComponent.reachGoal('initialization');
			else
				setTimeout(function () {bx_counter_waiter(++i)}, 100);
		})();
		<? endif ?>
	</script>
	<?
}
