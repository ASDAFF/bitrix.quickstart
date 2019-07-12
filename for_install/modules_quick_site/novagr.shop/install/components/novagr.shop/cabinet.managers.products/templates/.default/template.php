<?php 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->AddHeadScript('/include/tinymce/tinymce.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/autosuggest/bootstrap-tree.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/autosuggest/prettify.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/select2-1.0.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/scrollTo.js');
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.smooth-scroll.min.js');


$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/autosuggest/bootstrap-editable.js');

$formAction = $templateFolder . '/ajax.php';
$APPLICATION->AddHeadScript($templateFolder . '/ng_products.js');
?>
<div class="row">
	<div class="span2">
	 <?$APPLICATION->IncludeComponent(
	"novagr.shop:managers.menu",
	"",
	Array(
		"PRODUCT_IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
		"COUNT_ELEMENTS" => "Y",
		"CACHE_FOR_REQUEST_URI" => "N",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "86400"
	)
);?>
	</div>
	<div class="span8">
		<div class="row">
			<div class="span">
				<form class="form-horizontal add_prod_main">
									<a href="#editItem"  class="btn addForm" ><?=GetMessage("LABEL_ADD")?></a>
								</form>
							</div>

							<div class="check-my">
								<label class="checkbox">
									<input type="checkbox" <?=($_COOKIE['show_foto'] == 1 ? "checked" : '')?> id="optionsCheckbox" value="option1" name="show_foto" >
									<?=GetMessage("LABEL_PHOTO_SHOW")?>
								</label>
							</div>
						</div>
			<div class="row ">
				<div class="span8">
				<table id="products-t" class="table table-striped table-bordered table-condensed product-table">
					<thead>
						<tr>
							<th><?=GetMessage("LABEL_ON")?></th>
							<th class="photo-td <?=($_COOKIE['show_foto'] == 1 ? "" : "hidden")?>"><?=GetMessage("LABEL_PHOTO")?></th>
							<th><?=GetMessage("LABEL_NAME")?></th>
							<th class="wr-sp"><?=GetMessage("LABEL_PRICE_RUB")?></th>
							<th colspan="2"><?=GetMessage("LABEL_ACTIONS")?></th>
						</tr>
					</thead>
					<tbody>
<?php 
foreach ($arResult["ELEMENTS"] as $key => $item) {
	$productLink = '/catalog/'.$arResult["SECTION_CODES"][$item["IBLOCK_SECTION_ID"]].'/'.$item["CODE"].'/';
	
	
	if (!empty($item["PROPERTY_PHOTO_COLOR_1_VALUE"][0])) {
		$productPhoto = CFile::GetPath($item["PROPERTY_PHOTO_COLOR_1_VALUE"][0]);
	} else {
		$productPhoto = '';
	}
	//deb($item);
	?>
	<tr>
	<td>
	<input data-product-id="<?=$item['ID']?>" type="checkbox" value="" name="A" <?=( $item["ACTIVE"] == "Y" ? "checked='checked'" : "")?> class="set-active" >
	<br>
	</td>
	<td class="photo-td <?=($_COOKIE['show_foto'] == 1 ? "" : "hidden")?>"><a class="hidden_img" href="<?=$productLink?>"> <img width="65" height="86" src="<?=$productPhoto?>" alt="" /> </a></td>
	<td class="td-condensed ">
	
	<a href="#editItem" data-item-id=<?=$item["ID"]?> data-iblock-id="<?=$arParams["PRODUCT_IBLOCK_ID"]?>" data-content="<img src='<?=$productPhoto?>' width='65' height='86' >" class="pic_popup editlink" data-original-title=""><?=$item["NAME"]?></a></td>

	<td class="wr-sp"><span id="snyat-s-<?=$item['ID']?>" <?=( $item["ACTIVE"] == "Y" ? "class='hidden'" : "")?>><?=GetMessage("LABEL_PRODUCT_OFF")?></span><span <?=( $item["ACTIVE"] == "Y" ? "" : "class='hidden'")?> id="price-s-<?=$item['ID']?>"><?=$item["PRICE"]?></span></td>

	<td class="tooltip-demo">
	<div class="action_l">
		<a data-item-id=<?=$item["ID"]?> data-iblock-id="<?=$arParams["PRODUCT_IBLOCK_ID"]?>" rel="tooltip" data-placement="top" data-original-title="<?=GetMessage("LABEL_PRODUCT_EDIT")?>" href="#editItem" class="olbox editlink"> <span class="icon-pencil"></span></a>
		<a rel="tooltip" data-placement="top" data-original-title="<?=GetMessage("LABEL_GO_TO_PRODUCT")?>" href="<?=$productLink?>" target="_blank"><span class="icon-search"></span></a>
	</div></td>
	
</tr>
	<?php 
}	
?>
</tbody>
</table>

<div id="navigate">
<?php echo $arResult["NAV_STRING"];?>
</div>
							
			</div>
		</div>		
	</div>
</div>		

		
		<div aria-hidden="false" role="dialog" tabindex="-1" class="modal hide fade officeDealer" id="editItem">		
		</div>

		<div class="modal fade" id="modal_div_confirm"></div>

		<div id="modal_div" role="dialog" tabindex="-1" class="modal hide fade" aria-hidden="true"></div>
<script type="text/javascript">
$(document).ready(function() {
	var messages = {
		AJAX_FORM_ADD_COLOR: '<?=GetMessage("AJAX_FORM_ADD_COLOR")?>',
		LABEL_FIL_FIELD: '<?=GetMessage("LABEL_FIL_FIELD")?>',
		LABEL_DEL: '<?=GetMessage("LABEL_DEL")?>',
		AJAX_FORM_TITLE_MESS: '<?=GetMessage("AJAX_FORM_TITLE_MESS")?>',
		ADD_VALUE_LABEL: '<?=GetMessage("ADD_VALUE_LABEL")?>',
		LABEL_BTN_SEND: '<?=GetMessage("LABEL_BTN_SEND")?>',
		AJAX_FORM_QUANTINY: '<?=GetMessage("AJAX_FORM_QUANTINY")?>',
		AJAX_FORM_COLOR: '<?=GetMessage("AJAX_FORM_COLOR")?>',
		LABEL_CHANGE_MIND: '<?=GetMessage("LABEL_CHANGE_MIND")?>',
		WARNING_NAME: '<?=GetMessage("WARNING_NAME")?>',
		WARNING_PRICE: '<?=GetMessage("WARNING_PRICE")?>',
		WARNING_GROUP: '<?=GetMessage("WARNING_GROUP")?>',
		WARNING_BRAND: '<?=GetMessage("WARNING_BRAND")?>',
		WARNING_MATERIAL: '<?=GetMessage("WARNING_MATERIAL")?>',
		WARNING_COLOR: '<?=GetMessage("WARNING_COLOR")?>',
		OBLIGATORY_ADD_SIZE: '<?=GetMessage("OBLIGATORY_ADD_SIZE")?>',
		WARNING_SIZE: '<?=GetMessage("WARNING_SIZE")?>',
		WARNING_COLOR: '<?=GetMessage("WARNING_COLOR")?>',
		WARNING_PHOTO: '<?=GetMessage("WARNING_PHOTO")?>',
		LABEL_ADD_SIZE: '<?=GetMessage("LABEL_ADD_SIZE")?>',
		LABEL_CHOOSE_COLOR: '<?=GetMessage("LABEL_CHOOSE_COLOR")?>',
		NO_MATCHES_FOUND: '<?=GetMessage("NO_MATCHES_FOUND")?>',
		AJAX_FORM_PRICE: '<?=GetMessage("AJAX_FORM_PRICE")?>',
		AJAX_FORM_FILL_FIELD: '<?=GetMessage("AJAX_FORM_FILL_FIELD")?>',
		WARNING_PRICE_TP: '<?=GetMessage("WARNING_PRICE_TP")?>',
		RECONSIDER: '<?=GetMessage("RECONSIDER")?>',
		LABEL_CHOOSE: '<?=GetMessage("LABEL_CHOOSE")?>',		
		LABEL_ADD_COLOR: '<?=GetMessage("LABEL_ADD_COLOR")?>'
	};
	dcProduct.init(
			'<?=$formAction?>',
			<?=$arParams["PRODUCT_IBLOCK_ID"]?>,
			<?=$arParams["OFFERS_IBLOCK_ID"]?>,
			messages
	);
});
</script>	