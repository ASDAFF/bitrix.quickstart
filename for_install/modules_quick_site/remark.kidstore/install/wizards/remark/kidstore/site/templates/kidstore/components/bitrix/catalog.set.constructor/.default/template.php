<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$intElementID = intval($arParams["ELEMENT_ID"]);
CJSCore::Init(array("popup"));
$countDefSetItems = count($arResult["SET_ITEMS"]["DEFAULT"]);
$blockWidth = 87/(1+$countDefSetItems);
?>
<div class="bx_item_set_hor_container_big">
	<span class="bx_item_section_name_gray"><?=GetMessage("CATALOG_SET_BUY_SET")?></span>

	<div class="bx_item_set_hor">
		<div class="bx_item_set_hor_item plus" style="width:<?=$blockWidth?>%;" data-price="<?=$arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"]?>" data-old-price="<?=$arResult["ELEMENT"]["PRICE_VALUE"]?>" data-discount-diff-price="<?=$arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>">
			<div class="bx_item_set_img_container">
				<?if ($arResult["ELEMENT"]["DETAIL_PICTURE"]["src"]):?>
					<a class="<?=($arResult["ELEMENT"]["DETAIL_PICTURE"]["width"] >= $arResult["ELEMENT"]["DETAIL_PICTURE"]["height"]) ? "bx_kit_img_landscape" : "bx_kit_img_portrait"?>" href="<?=$arResult["ELEMENT"]["DETAIL_PAGE_URL"]?>" style="background-image: url('<?=$arResult["ELEMENT"]["DETAIL_PICTURE"]["src"]?>');"></a>
				<?else:?>
					<a href="<?=$arResult["ELEMENT"]["DETAIL_PAGE_URL"]?>" style="background-image: url('<?=$this->GetFolder();?>/images/no_foto.png')"></a>
				<?endif?>
			</div>
			<a class="bx_item_set_linkitem" href="<?=$arResult["ELEMENT"]["DETAIL_PAGE_URL"]?>"><?=$arResult["ELEMENT"]["NAME"]?> <br /><br />
				<span class="bx_item_set_price"><strong><?=$arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"]?></strong></span>
				<?if (!($arResult["ELEMENT"]["PRICE_VALUE"] == $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"])):?><span class="bx_item_set_price old"><strong><?=$arResult["ELEMENT"]["PRICE_PRINT_VALUE"]?></strong></span><?endif?>
			</a>
			<div style="clear: both;"></div>
		</div>

		<?foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem):?>
			<div class="bx_item_set_hor_item <?if ($key<$countDefSetItems-1) echo "plus"; else echo "equally"?> bx_default_set_items"
				style="width:<?=$blockWidth?>%;"
				data-price="<?=(($arItem["PRICE_CONVERT_DISCOUNT_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_VALUE"] : $arItem["PRICE_DISCOUNT_VALUE"])?>"
				data-old-price="<?=(($arItem["PRICE_CONVERT_VALUE"]) ? $arItem["PRICE_CONVERT_VALUE"] : $arItem["PRICE_VALUE"])?>"
				data-discount-diff-price="<?=(($arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"]) ? $arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"] : $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"])?>">
				<div class="bx_item_set_img_container">
					<?if ($arItem["DETAIL_PICTURE"]["src"]):?>
						<a class="<?=($arItem["DETAIL_PICTURE"]["width"] >= $arItem["DETAIL_PICTURE"]["height"]) ? "bx_kit_img_landscape" : "bx_kit_img_portrait"?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>" style="background-image: url('<?=$arItem["DETAIL_PICTURE"]["src"]?>');"></a>
					<?else:?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" style="background-image: url('<?=$this->GetFolder();?>/images/no_foto.png')"></a>
					<?endif?>
				</div>
				<a class="bx_item_set_linkitem" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?><br /><br />
					<span class="bx_item_set_price"><strong><?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?></strong></span>
					<span  class="bx_item_set_price old" <?if ($arItem["PRICE_VALUE"] == $arItem["PRICE_DISCOUNT_VALUE"]):?>style="display:none"<?endif?>><strong><?=$arItem["PRICE_PRINT_VALUE"]?></strong></span>
				</a>
				<div class="bx_item_set_del" onclick="catalogSetDefaultObj_<? echo $intElementID; ?>.DeleteItem(this.parentNode, '<?=$arItem["ID"]?>')"></div>
				<div style="clear: both;"></div>
			</div>
		<?endforeach?>

		<div class="bx_item_set_hor_item result">
			<span class="bx_item_set_result_block">
				<span class="bx_item_set_current_price"><?=$arResult["SET_ITEMS"]["PRICE"]?></span>
				<?if ($arResult["SET_ITEMS"]["OLD_PRICE"]):?>
				<br/><span class="bx_item_set_old_price"><?=$arResult["SET_ITEMS"]["OLD_PRICE"]?></span>
				<?endif?>
				<?if ($arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"]):?>
				<br/><span class="bx_item_set_economy_price"><?=GetMessage("CATALOG_SET_DISCOUNT_DIFF", array("#PRICE#" => $arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"]))?></span>
				<?endif?>
			</span>
			<a href="javascript:void(0)" onclick="catalogSetDefaultObj_<? echo $intElementID; ?>.Add2Basket();" class="bx_bt_white bx_medium"><?=GetMessage("CATALOG_SET_BUY")?></a>
		</div>

		<div style="clear: both;"></div>
	</div>
	<a class="bx_item_set_creator_link" href="javascript:void(0)" onclick="OpenCatalogSetPopup('<?=$intElementID?>');"><?=GetMessage("CATALOG_SET_CONSTRUCT")?></a>
</div>

<?
$popupParams["AJAX_PATH"] = $this->GetFolder()."/ajax.php";
$popupParams["SITE_ID"] = SITE_ID;
$popupParams["CURRENT_TEMPLATE_PATH"] = $this->GetFolder();
$popupParams["MESS"] = array(
	"CATALOG_SET_POPUP_TITLE" => GetMessage("CATALOG_SET_POPUP_TITLE"),
	"CATALOG_SET_POPUP_DESC" => GetMessage("CATALOG_SET_POPUP_DESC"),
	"CATALOG_SET_BUY" => GetMessage("CATALOG_SET_BUY"),
	"CATALOG_SET_SUM" => GetMessage("CATALOG_SET_SUM"),
	"CATALOG_SET_DISCOUNT" => GetMessage("CATALOG_SET_DISCOUNT"),
	"CATALOG_SET_WITHOUT_DISCOUNT" => GetMessage("CATALOG_SET_WITHOUT_DISCOUNT"),
);
$popupParams["ELEMENT"] = $arResult["ELEMENT"];
$popupParams["SET_ITEMS"] = $arResult["SET_ITEMS"];
$popupParams["DEFAULT_SET_IDS"] = $arResult["DEFAULT_SET_IDS"];
$popupParams["ITEMS_RATIO"] = $arResult["ITEMS_RATIO"];
?>

<script>
	BX.message({
		setItemAdded2Basket: '<?=GetMessageJS("CATALOG_SET_ADDED2BASKET")?>',
		setButtonBuyName: '<?=GetMessageJS("CATALOG_SET_BUTTON_BUY")?>',
		setButtonBuyUrl: '<?=$arParams["BASKET_URL"]?>',
		setIblockId: '<?=$arParams["IBLOCK_ID"]?>',
		setOffersCartProps: <?=CUtil::PhpToJSObject($arParams["OFFERS_CART_PROPERTIES"])?>
	});

	BX.ready(function(){
		catalogSetDefaultObj_<?=$intElementID; ?> = new catalogSetConstructDefault(
			<?=CUtil::PhpToJSObject($arResult["DEFAULT_SET_IDS"])?>,
			'<? echo $this->GetFolder(); ?>/ajax.php',
			'<?=$arResult["ELEMENT"]["PRICE_CURRENCY"]?>',
			'<?=SITE_ID?>',
			'<?=$intElementID?>',
			'<?=($arResult["ELEMENT"]["DETAIL_PICTURE"]["src"] ? $arResult["ELEMENT"]["DETAIL_PICTURE"]["src"] : $this->GetFolder().'/images/no_foto.png')?>',
			<?=CUtil::PhpToJSObject($arResult["ITEMS_RATIO"])?>
		);
	});

	if (!window.arSetParams)
	{
		window.arSetParams = [{'<?=$intElementID?>' : <?echo CUtil::PhpToJSObject($popupParams)?>}];
	}
	else
	{
		window.arSetParams.push({'<?=$intElementID?>' : <?echo CUtil::PhpToJSObject($popupParams)?>});
	}

	function OpenCatalogSetPopup(element_id)
	{
		if (window.arSetParams)
		{
			for(var obj in window.arSetParams)
			{
				for(var obj2 in window.arSetParams[obj])
				{
					if (obj2 == element_id)
						var curSetParams = window.arSetParams[obj][obj2]
				}
			}
		}

		BX.CatalogSetConstructor =
		{
			bInit: false,
			popup: null,
			arParams: {}
		}
		BX.CatalogSetConstructor.popup = BX.PopupWindowManager.create("CatalogSetConstructor_"+element_id, null, {
			autoHide: false,
			offsetLeft: 0,
			offsetTop: 0,
			overlay : true,
			draggable: {restrict:true},
			closeByEsc: false,
			closeIcon: { right : "12px", top : "10px"},
			titleBar: {content: BX.create("span", {html: "<div><?=GetMessage("CATALOG_SET_POPUP_TITLE_BAR")?></div>"})},
			content: '<div style="width:250px;height:250px; text-align: center;"><span style="position:absolute;left:50%; top:50%"><img src="<?=$this->GetFolder()?>/images/wait.gif"/></span></div>',
			events: {
				onAfterPopupShow: function()
				{
					BX.ajax.post(
						'<? echo $this->GetFolder(); ?>/popup.php',
						{
							lang: BX.message('LANGUAGE_ID'),
							site_id: BX.message('SITE_ID') || '',
							arParams:curSetParams
						},
						BX.delegate(function(result)
						{
							this.setContent(result);
							BX("CatalogSetConstructor_"+element_id).style.left = (window.innerWidth - BX("CatalogSetConstructor_"+element_id).offsetWidth)/2 +"px";
							var popupTop = document.body.scrollTop + (window.innerHeight - BX("CatalogSetConstructor_"+element_id).offsetHeight)/2;
							BX("CatalogSetConstructor_"+element_id).style.top = popupTop > 0 ? popupTop+"px" : 0;
						},
						this)
					);
				}
			}
		});

		BX.CatalogSetConstructor.popup.show();
	}
</script>