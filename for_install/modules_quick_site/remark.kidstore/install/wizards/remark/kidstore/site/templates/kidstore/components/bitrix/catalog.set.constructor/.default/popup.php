<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

if (SITE_CHARSET != "utf-8")
	$_REQUEST["arParams"] = $APPLICATION->ConvertCharsetArray($_REQUEST["arParams"], "utf-8", SITE_CHARSET);

if (!is_array($_REQUEST["arParams"]["ELEMENT"]))
	return;

$curElementId = intval($_REQUEST["arParams"]["ELEMENT"]["ID"]);
$arCurElementInfo = $_REQUEST["arParams"]["ELEMENT"];
$arSetItemsInfo = $_REQUEST["arParams"]["SET_ITEMS"];
$arMessage = $_REQUEST["arParams"]["MESS"];
$curTemplatePath = $_REQUEST["arParams"]["CURRENT_TEMPLATE_PATH"];

$arSetElementsDefault = $_REQUEST["arParams"]["SET_ITEMS"]["DEFAULT"];
$arSetElementsOther = $_REQUEST["arParams"]["SET_ITEMS"]["OTHER"];

$setPrice = $_REQUEST["arParams"]["SET_ITEMS"]["PRICE"];
$setOldPrice = $_REQUEST["arParams"]["SET_ITEMS"]["OLD_PRICE"];
$setPriceDiscountDifference = $_REQUEST["arParams"]["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"];
?>

<div class="bx_modal_container bx_kit">
	<strong class="bx_modal_small_title"><?=$arMessage["CATALOG_SET_POPUP_TITLE"]?></strong>
	<span class="bx_modal_description"><?=$arMessage["CATALOG_SET_POPUP_DESC"]?></span>

	<div class="bx_modal_body" id="bx_catalog_set_construct_popup_<?=$curElementId?>">
		<div class="bx_kit_one_section">
			<div class="bx_kit_item">
				<div class="bx_kit_item_children">
					<?if ($arCurElementInfo["DETAIL_PICTURE"]["src"]):?>
						<div class="bx_kit_img_container <?=($arCurElementInfo["DETAIL_PICTURE"]["width"] >= $arCurElementInfo["DETAIL_PICTURE"]["height"]) ? "bx_kit_img_landscape" : "bx_kit_img_portrait"?>" style="background-image:url('<?=$arCurElementInfo["DETAIL_PICTURE"]["src"]?>');"></div>
					<?else:?>
						<div class="bx_kit_img_container" style="background-image: url('<?=$curTemplatePath?>/images/no_foto.png')"></div>
					<?endif?>
					<div class="bx_kit_item_title"><a href="<?=$arCurElementInfo["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arCurElementInfo["NAME"]?></a></div>
					<div class="bx_kit_item_price"><div class="bx_price"><?=$arCurElementInfo["PRICE_PRINT_DISCOUNT_VALUE"]?></div></div>
				</div>
				<?if ($arCurElementInfo["PRICE_DISCOUNT_DIFFERENCE_VALUE"]):?><div class="bx_kit_item_discount" style="padding-top: 3px;"><?=$arCurElementInfo["PRICE_DISCOUNT_DIFFERENCE"]?></div><?endif?>
			</div>
			<div class="bx_kit_item_plus"></div>

			<?
			$curCountDefaultSetItems = 0;
			?>
			<?foreach($arSetElementsDefault as $arItem):?>
				<div class="bx_kit_item bx_drag_dest<?if ($arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]):?> discount<?endif?>">
					<div class="bx_kit_item_children bx_kit_item_border">
						<?if ($arItem["DETAIL_PICTURE"]["src"]):?>
							<div class="bx_kit_img_container <?=($arItem["DETAIL_PICTURE"]["width"] >= $arItem["DETAIL_PICTURE"]["height"]) ? "bx_kit_img_landscape" : "bx_kit_img_portrait"?>" style="background-image:url('<?=$arItem["DETAIL_PICTURE"]["src"]?>');"></div>
						<?else:?>
							<div class="bx_kit_img_container" style="background-image: url('<?=$curTemplatePath?>/images/no_foto.png')"></div>
						<?endif?>
						<div class="bx_kit_item_title" data-item-id="<?=$arItem["ID"]?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arItem["NAME"]?></a></div>
						<div class="bx_kit_item_price"
							data-discount-price="<?=($arItem["PRICE_CONVERT_DISCOUNT_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_VALUE"] : $arItem["PRICE_DISCOUNT_VALUE"]?>"
							data-price="<?=($arItem["PRICE_CONVERT_VALUE"]) ? $arItem["PRICE_CONVERT_VALUE"] : $arItem["PRICE_VALUE"]?>"
							data-discount-diff-price="<?=($arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"] : $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"><div class="bx_price"><?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?></div></div>
						<div class="bx_kit_item_del" onclick="catalogSetPopupObj.catalogSetDelete(this.parentNode);"></div>
					</div>
					<?if ($arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]):?><div class="bx_kit_item_discount">-<?=$arItem["PRICE_DISCOUNT_DIFFERENCE"]?></div><?endif?>
				</div>
				<?
				$curCountDefaultSetItems++;
				if ($curCountDefaultSetItems<3):?>
				<div class="bx_kit_item_plus"></div>
				<?endif?>
			<?endforeach?>

			<?if ($curCountDefaultSetItems<3):
				for($j=1; $j<=(3-$curCountDefaultSetItems); $j++)
				{
			?>
					<div class="bx_kit_item bx_kit_item_border bx_kit_item_empty bx_drag_dest"></div>
					<?if ($j<3-$curCountDefaultSetItems):?><div class="bx_kit_item_plus"></div><?endif?>
			<?
				}
			?>
			<?endif?>

			<div class="bx_kit_item_equally"></div>

			<div class="bx_kit_item" style="padding-top:0;">
				<div class="bx_kit_result <?if (!$setOldPrice && !$setPriceDiscountDifference):?>not_sale<?endif?>" id="bx_catalog_set_construct_price_block_<?=$curElementId?>">
					<div class="bx_kit_result_one" <?if (!$setOldPrice):?>style="display: none;"<?endif?>>
						<?=$arMessage["CATALOG_SET_WITHOUT_DISCOUNT"]?> <br />
						<strong id="bx_catalog_set_construct_sum_old_price_<?=$curElementId?>"><?=$setOldPrice?></strong>
					</div>
					<div class="bx_kit_result_two">
						<?=$arMessage["CATALOG_SET_SUM"]?>:<br />
						<strong id="bx_catalog_set_construct_sum_price_<?=$curElementId?>"><?=$setPrice?></strong>
					</div>
					<div class="bx_kit_result_tre" <?if (!$setPriceDiscountDifference):?>style="display: none;"<?endif?>>
						<?=$arMessage["CATALOG_SET_DISCOUNT"]?>:<br />
						<strong id="bx_catalog_set_construct_sum_diff_price_<?=$curElementId?>"><?=$setPriceDiscountDifference?></strong>
					</div>
					<a href="javascript:void(0)" class="bx_bt_blue bx_medium" onclick="catalogSetPopupObj.Add2Basket();"><span class="bx_icon_cart"></span><span><?=$arMessage["CATALOG_SET_BUY"]?></span></a>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>

		<div class="bx_kit_two_section">
			<div class="bx_kit_two_section_ova">
				<div class="bx_kit_two_item_slider" id="bx_catalog_set_construct_slider_<?=$curElementId?>" data-style-left="0" style="left:0%;width:<?=(count($arSetElementsOther) <=5) ? 100 : 100 + 20*(count($arSetElementsOther)-5)?>%">
				<?if (is_array($arSetElementsOther)):?>
					<?foreach($arSetElementsOther as $arItem):?>
					<div class="bx_kit_item_slider bx_drag_obj" style="width:<?=(count($arSetElementsOther) <=5) ? "20" : (100/count($arSetElementsOther))?>%" data-main-element-id="<?=$curElementId?>">
						<div class="bx_kit_item bx_kit_item_border<?if ($arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]):?> discount<?endif?>">
							<?if ($arItem["DETAIL_PICTURE"]["src"]):?>
								<div class="bx_kit_img_container <?=($arItem["DETAIL_PICTURE"]["width"] >= $arItem["DETAIL_PICTURE"]["height"]) ? "bx_kit_img_landscape" : "bx_kit_img_portrait"?>" style="background-image:url('<?=$arItem["DETAIL_PICTURE"]["src"]?>');"></div>
							<?else:?>
								<div class="bx_kit_img_container" style="background-image: url('<?=$curTemplatePath?>/images/no_foto.png')"></div>
							<?endif?>
							<div class="bx_kit_item_title" data-item-id="<?=$arItem["ID"]?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arItem["NAME"]?></a></div>
							<div class="bx_kit_item_price"
								data-discount-price="<?=($arItem["PRICE_CONVERT_DISCOUNT_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_VALUE"] : $arItem["PRICE_DISCOUNT_VALUE"]?>"
								data-price="<?=($arItem["PRICE_CONVERT_VALUE"]) ? $arItem["PRICE_CONVERT_VALUE"] : $arItem["PRICE_VALUE"]?>"
								data-discount-diff-price="<?=($arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"] : $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"><div class="bx_price"><?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?></div></div>
							<div class="bx_kit_item_add" onclick="catalogSetPopupObj.catalogSetAdd(this.parentNode);"></div>
						</div>
						<?if ($arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]):?><div class="bx_kit_item_discount">-<?=$arItem["PRICE_DISCOUNT_DIFFERENCE"]?></div><?endif?>
					</div>
					<?endforeach;?>
				<?endif?>
				</div>
			</div>
			<div class="bx_kit_item_slider_arrow_left" id="bx_catalog_set_construct_slider_left_<?=$curElementId?>" <?if (count($arSetElementsOther) < 5):?>style="display:none"<?endif?> onclick="catalogSetPopupObj.scrollItems('left')"></div>
			<div class="bx_kit_item_slider_arrow_right" id="bx_catalog_set_construct_slider_right_<?=$curElementId?>" <?if (count($arSetElementsOther) < 5):?>style="display:none"<?endif?> onclick="catalogSetPopupObj.scrollItems('right')"></div>
		</div>
	</div>
</div>

<?
CJSCore::Init(array("popup"));
?>
<script>
	var catalogSetPopupObj = new catalogSetConstructPopup(<?=count($arSetElementsOther)?>,
		<?=(count($arSetElementsOther) > 5) ? (100/count($arSetElementsOther)) : 20?>,
		"<?=CUtil::JSEscape($arCurElementInfo["PRICE_CURRENCY"])?>",
		"<?=CUtil::JSEscape($arCurElementInfo["PRICE_VALUE"])?>",
		"<?=CUtil::JSEscape($arCurElementInfo["PRICE_DISCOUNT_VALUE"])?>",
		"<?=CUtil::JSEscape($arCurElementInfo["PRICE_DISCOUNT_DIFFERENCE_VALUE"])?>",
		"<?=$_REQUEST["arParams"]["AJAX_PATH"]?>",
		<?=CUtil::PhpToJSObject($_REQUEST["arParams"]["DEFAULT_SET_IDS"])?>,
		"<?=$_REQUEST["arParams"]["SITE_ID"]?>",
		"<?=$curElementId?>",
		<?=CUtil::PhpToJSObject($_REQUEST["arParams"]["ITEMS_RATIO"])?>,
		"<?=$arCurElementInfo["DETAIL_PICTURE"]["src"] ? $arCurElementInfo["DETAIL_PICTURE"]["src"] : $curTemplatePath."/images/no_foto.png"?>"
	);

	BX.ready(function(){
		jsDD.Enable();

		var destObj = BX.findChildren(BX("bx_catalog_set_construct_popup_<?=$curElementId?>"), {className:"bx_drag_dest"}, true);
		for (var i=0; i<destObj.length; i++)
		{
			jsDD.registerDest(destObj[i]);
			destObj[i].onbxdestdragfinish =  catalogSetConstructDestFinish;  //node was thrown inside of dest
		}
		var dragObj = BX.findChildren(BX("bx_catalog_set_construct_popup_<?=$curElementId?>"), {className:"bx_drag_obj"}, true);
		for (var i=0; i<dragObj.length; i++)
		{
			dragObj[i].onbxdragstart = catalogSetConstructDragStart;
			dragObj[i].onbxdrag = catalogSetConstructDragMove;
			dragObj[i].onbxdraghover = catalogSetConstructDragHover;
			dragObj[i].onbxdraghout = catalogSetConstructDragOut;
			dragObj[i].onbxdragrelease = catalogSetConstructDragRelease;   //node was thrown outside of dest
			jsDD.registerObject(dragObj[i]);
		}
	});
</script>