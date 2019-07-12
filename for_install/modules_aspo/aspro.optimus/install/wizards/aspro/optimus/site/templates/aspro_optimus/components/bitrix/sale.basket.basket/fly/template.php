<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
$iblockID=\Bitrix\Main\Config\Option::get("aspro.optimus", "CATALOG_IBLOCK_ID", COptimusCache::$arIBlocks[SITE_ID]['aspro_optimus_catalog']['aspro_optimus_catalog'][0], SITE_ID );
$compareCount = count($_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"]);
$arParamsExport=$arParams;
unset($arParamsExport['INNER']);
$paramsString = urlencode(serialize($arParamsExport));

// update basket counters
CModule::IncludeModule('aspro.optimus');
$arCounters = COptimus::updateBasketCounters(array('READY' => array('COUNT' => $normalCount, 'TITLE' => $arResult['allSum_FORMATED'], 'HREF' => $arParams["PATH_TO_BASKET"]), 'DELAY' => array('COUNT' => $delayCount, 'TITLE' => $arResult["DELAY_PRICE"]["SUMM_FORMATED"], 'HREF' => $arParams["PATH_TO_BASKET"]), 'COMPARE' => array('COUNT' => $compareCount, 'HREF' => $arParams["PATH_TO_COMPARE"]), 'PERSONAL' => array('HREF' => $arParams["PATH_TO_AUTH"])));
?>
<?if($arParams['INNER'] !==true && $_SERVER['REQUEST_METHOD'] !== 'POST'):?>
	<div class="basket_fly loaded<?if (strlen($arResult["ERROR_MESSAGE"]) > 0):?> basket_empty<?endif;?>">
<?endif;?>
	<?$frame = $this->createFrame()->begin('');?>
	<input type="hidden" name="total_price" value="<?=$arResult['allSum_FORMATED']?>" />
	<input type="hidden" name="total_discount_price" value="<?=$arResult['allSum_FORMATED']?>" />
	<input type="hidden" name="total_count" value="<?=$normalCount;?>" />
	<input type="hidden" name="delay_count" value="<?=$delayCount;?>" />

	<div class="opener">
		<div title="<?=$arCounters['READY']['TITLE']?>" data-type="AnDelCanBuy" class="basket_count small clicked<?=(!$arCounters['READY']['COUNT'] ? ' empty' : '')?>">
			<a href="<?=$arCounters['READY']['HREF']?>"></a>
			<div class="wraps_icon_block basket">
				<div class="count<?=(!$arCounters['READY']['COUNT'] ? ' empty_items' : '')?>">
					<span>
						<span class="items">
							<span><?=$arCounters['READY']['COUNT']?></span>
						</span>
					</span>
				</div>
			</div>
		</div>
		<div title="<?=$arCounters['DELAY']['TITLE']?>" data-type="DelDelCanBuy" class="wish_count small clicked<?=(!$arCounters['DELAY']['COUNT'] ? ' empty' : '')?>">
			<a href="<?=$arCounters['DELAY']['HREF']?>"></a>
			<div class="wraps_icon_block delay">
				<div class="count<?=(!$arCounters['DELAY']['COUNT'] ? ' empty_items' : '')?>">
					<span>
						<span class="items">
							<span><?=$arCounters['DELAY']['COUNT']?></span>
						</span>
					</span>
				</div>
			</div>
		</div>
		<div title="<?=$arCounters['COMPARE']['TITLE']?>" class="compare_count small">
			<a href="<?=$arCounters['COMPARE']['HREF']?>"></a>
			<div id="compare_fly" class="wraps_icon_block compare">
				<div class="count<?=(!$arCounters['COMPARE']['COUNT'] ? ' empty_items' : '')?>">
					<span>
						<span class="items">
							<span><?=$arCounters['COMPARE']['COUNT']?></span>
						</span>
					</span>
				</div>
			</div>
		</div>
		<div title="<?=$arCounters['PERSONAL']['TITLE']?>" class="user_block small">
			<a href="<?=$arCounters['PERSONAL']['HREF']?>"></a>
			<div class="wraps_icon_block<?=($arCounters['PERSONAL']['SRC'] ? ' w_img' : ' no_img')?><?=($arCounters['PERSONAL']['ID'] ? ' user_auth' : ' user_reg')?>"<?=($arCounters['PERSONAL']['SRC'] ? " style='background:url(".$arCounters['PERSONAL']['SRC'].") center center no-repeat;'" : "")?>></div>
		</div>
	</div>
	<script src="<?=(((COption::GetOptionString('main', 'use_minified_assets', 'N', $siteID) === 'Y') && file_exists($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/script.min.js')) ? $templateFolder.'/script.min.js' : $templateFolder.'/script.js')?>" type="text/javascript"></script>
	<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/functions.php");
		$arUrls = Array("delete" => SITE_DIR."ajax/show_basket_fly.php?action=delete&id=#ID#",
						"delay" => SITE_DIR."ajax/show_basket_fly.php?action=delay&id=#ID#",
						"add" => SITE_DIR."ajax/show_basket_fly.php?action=add&id=#ID#");


		if (is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"])) { foreach ($arResult["WARNING_MESSAGE"] as $v) { echo ShowError($v); } }

		$arMenu = array(array("ID"=>"AnDelCanBuy", "TITLE"=>GetMessage("SALE_BASKET_ITEMS"), "COUNT"=>$normalCount, "FILE"=>"/basket_items.php"));
		if ($delayCount) { $arMenu[] = array("ID"=>"DelDelCanBuy", "TITLE"=>GetMessage("SALE_BASKET_ITEMS_DELAYED"), "COUNT"=>$delayCount, "FILE"=>"/basket_items_delayed.php"); }
		//if ($subscribeCount) { $arMenu[] = array("ID"=>"ProdSubscribe", "TITLE"=>GetMessage("SALE_BASKET_ITEMS_SUBSCRIBED"), "COUNT"=>$subscribeCount, "FILE"=>"/basket_items_subscribed.php"); }
		//if ($naCount) { $arMenu[] = array("ID"=>"nAnCanBuy", "TITLE"=>GetMessage("SALE_BASKET_ITEMS_NOT_AVAILABLE"), "COUNT"=>$naCount, "FILE"=>"/basket_items_not_available.php"); }

	?>
		<div class="basket_sort">
			<span class="basket_title"><?=GetMessage("BASKET_TITLE");?></span>
			<ul class="tabs">
				<?if (strlen($arResult["ERROR_MESSAGE"]) <= 0){?>
					<?foreach($arMenu as $key => $arElement){?>
						<li<?=($arElement["SELECTED"] ? ' class="cur"' : '');?> item-section="<?=$arElement["ID"]?>" data-type="<?=$arElement["ID"]?>">
							<div class="wrap_li">
								<span><?=$arElement["TITLE"]?></span>
								<span class="quantity">&nbsp;(<span class="count"><?=$arElement["COUNT"]?></span>)</span>
							</div>
						</li>
					<?}?>
				<?}?>
			</ul>
			<span class="wrap_remove_button">
				<?if($normalCount){?>
					<span class="button grey_br transparent remove_all_basket AnDelCanBuy cur" data-type="basket"><?=GetMessage('CLEAR_BASKET')?></span>
				<?}?>
				<?if($delayCount){?>
					<span class="button grey_br transparent remove_all_basket DelDelCanBuy" data-type="delay"><?=GetMessage('CLEAR_BASKET')?></span>
				<?}?>
			</span>
		</div>

		<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form" class="basket_wrapp">
			<?if (strlen($arResult["ERROR_MESSAGE"]) <= 0){?>
				<ul class="tabs_content basket">
					<?foreach($arMenu as $key => $arElement){?>
						<li<?=($arElement["SELECTED"] ? ' class="cur"' : '');?> item-section="<?=$arElement["ID"]?>"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder.$arElement["FILE"]);?></li>
					<?}?>
				</ul>
			<?}else{?>
				<ul class="tabs_content basket"><li class="cur" item-section="AnDelCanBuy"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");?></ul>
			<?}?>
			<input id="fly_basket_params" type="hidden" name="PARAMS" value='<?=$paramsString?>' />
		</form>

		<script>
			$(document).ready(function(){
				$("#basket_line .basket_fly").live('submit', function(e) {
					e.preventDefault();
				});
				$('#basket_line .basket_fly a.apply-button').click(function()
				{
					$('#basket_line .basket_fly form[name^=basket_form]').prepend('<input type="hidden" name="BasketRefresh" value="Y" />');
					$.post( arOptimusOptions['SITE_DIR']+'basket/', $("#basket_line .basket_fly form[name^=basket_form]").serialize(), $.proxy(
					function( data)
					{
						$('#basket_line .basket_fly form[name^=basket_form] input[name=BasketRefresh]').remove();
					}));
				});

				$("#basket_line .basket_fly .tabs > li").live("click", function()
				{
					$("#basket_line .basket_fly .tabs > li").removeClass("cur");
					$("#basket_line .basket_fly .tabs_content > li").removeClass("cur");
					$("#basket_line .basket_fly .basket_sort .remove_all_basket").removeClass("cur");
					$("#basket_line .basket_fly .tabs > li:eq("+$(this).index()+")").addClass("cur");
					$("#basket_line .basket_fly .tabs_content > li:eq("+$(this).index()+")").addClass("cur");
					$("#basket_line .basket_fly .basket_sort .remove_all_basket."+$(this).data('type')).addClass("cur");
				});

				$("#basket_line .basket_fly .back_button, #basket_line .basket_fly .close").live("click", function(){
					$("#basket_line .basket_fly .opener > div").trigger('click');
				});
			});


			<?if ($arParams["AJAX_MODE_CUSTOM"]=="Y"):?>
				var animateRow = function(row)
				{
					$(row).find("td.thumb-cell img").css({"maxHeight": "inherit", "maxWidth": "inherit"}).fadeTo(50, 0);
					var columns = $(row).find("td");
					$(columns).wrapInner('<div class="slide"></div>');
					$(row).find(".summ-cell").wrapInner('<div class="slide"></div>');
					setTimeout(function(){$(columns).animate({"paddingTop": 0, "paddingBottom": 0}, 50)}, 0);
					$(columns).find(".slide").slideUp(333);
				}

				$("#basket_form").ready(function()
				{
					$('form[name^=basket_form] .counter_block input[type=text]').change( function(e)
					{
						e.preventDefault();
						// updateQuantity($(this).attr("id"), $(this).attr("data-id"), $(this).attr("step"));
					});

					$('.basket_sort .remove_all_basket').click(function(e){
						if(!$(this).hasClass('disabled')){
							$(this).addClass('disabled');
							delete_all_items($(this).data("type"), $(".tabs_content li:eq("+$(this).index()+")").attr("item-section"), 333);

						}
						$(this).removeClass('disabled');
					})

					$('form[name^=basket_form] .remove').unbind('click').click(function(e){
						e.preventDefault();
						var row = $(this).parents("tr").first();
						row.fadeTo(100 , 0.05, function() {});
						deleteProduct($(this).parents("tr[data-id]").attr('data-id'), $(this).parents("li").attr("item-section"), $(this).parents("tr[data-id]").attr('product-id'), $(this).parents("tr[data-id]"));
						markProductRemoveBasket($(this).parents("tr[data-id]").attr('product-id'));
						return false;
					});

					$('form[name^=basket_form] .delay .wish_item').unbind('click').click(function(e){
						delayProduct($(this).parents("tr[data-id]").attr('data-id'), $(this).parents("li").attr("item-section"), $(this).parents("tr[data-id]"));
						var row = $(this).parents("tr").first();
						row.fadeTo(100 , 0.05, function() {});
						markProductDelay($(this).parents("tr[data-id]").attr('product-id'));
					});

					$('form[name^=basket_form] .add .wish_item').unbind('click').click(function(e){
						var basketId = $(this).parents("tr[data-id]").attr('data-id');
						var controlId =  "QUANTITY_INPUT_"+basketId;
						var ratio =  $(this).parents("tr[data-id]").find("#"+controlId).attr("step");
						var quantity =  $(this).parents("tr[data-id]").find("#"+controlId).attr("value");
						var row = $(this).parents("tr").first();
						row.fadeTo(100 , 0.05, function() {});
						addProduct(basketId, $(this).parents("li").attr("item-section"), $(this).parents("tr[data-id]"));
						markProductAddBasket($(this).parents("tr[data-id]").attr('product-id'));
					});
				});
			<?endif;?>
		</script>
	<?$frame->end();?>
<?if($arParams['INNER'] !==true && $_SERVER['REQUEST_METHOD'] !== 'POST'):?>
	</div>
<?endif;?>