<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//print '<pre>';print_r($arResult);print '</pre>';
if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}

$arSystemProps = array('models_numvals', 'models_rating', 'models_new', 'models_hit', 'models_video', 'similar_products', 'item_viewed');
function optShowProps($a, $b) {
	$aVal = is_array($a) ? $a['CODE'] : $a;
	$bVal = is_array($b) ? $b['CODE'] : $b;
	
	return strcasecmp($aVal, $bVal);
}
?>
<style type="text/css">#item .list li:before{content:'<?=GetMessage("DASH")?>';left:-1.4em;position:absolute}</style>
<?if (is_array($arResult["OFFERS_COMPACT"]) && !empty($arResult["OFFERS_COMPACT"])){?>

<?$defaultColor = (strlen($arParams["COLOR"]) > 0 ? $arParams["COLOR"] : $arResult["DEFAULT_COLOR"]);?>

<script>
$.preloadImg = function(imgs) {
    $.each(imgs, function(index, value) {
        var loadImgs = $('<img />');
        loadImgs.attr('src', value);
    });
};
</script>
<div id="item" itemscope itemtype="http://schema.org/Product">
    <div id="item-container">
        <div id="item-content">

        <div class="sku"><span class="back"><?=GetMessage("ARTICLE")?><span id="sku">
            <?foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {?>
            <span class="<?if($color == $defaultColor){?>active selected<?}else{?>hide<?}?>" itemprop="identifier" rel="<?=$color?>"><?=$arr["ARTICLE"]?></span>
            <?}?>
			</span></span>
			<div id="add-to-wishlist">
			<? if ($USER->isAuthorized()): ?>
				<? foreach ($arResult["OFFERS_COMPACT"] as $color => $arr): ?>
					<? foreach ($arr["SIZES"] as $key => $size):?>
						<? if($color == $defaultColor): ?>
							<? $requiredProductId = $size["PRODUCT_ID"]; break; ?>
						<? else: ?>
							<? $keys = array_keys($arResult["OFFERS_COMPACT"]); ?>
							<? $requiredProductId = $arResult["OFFERS_COMPACT"][$keys[0]]["SIZES"][0]["PRODUCT_ID"]; ?>
						<? endif; ?>
					<? endforeach; ?>
				<? endforeach; ?>
				<div class="wishlist" id="wishpic" style="display:none;" product-id="<?= $requiredProductId; ?>"></div>
				<div class="wishlist-caption" id="wishloader" style="left:0"><img src="/bitrix/templates/fashion_green/i/wishloader.gif" /></div>
				<div class="wishlist-caption" id="in_wishlist" style="display:none;"><?=GetMessage("ADD_TO_WISHLIST"); ?></div>
				<div class="wishlist-caption" id="not_in_wishlist" style="display:none;"><?=GetMessage("REMOVE_FROM_WISHLIST"); ?></div>	
			<? else: ?>        
				<div class="wishlist-gray"></div>
				<div class="wishlist-caption" id="in_wishlist" style="color:gray;cursor:text"><span title="<?=GetMessage("AUTH_ONLY"); ?>"><?=GetMessage("ADD_TO_WISHLIST"); ?></span></div>        
			<? endif; ?>		
			</div>
		</div>
        <div class="separator"></div>

        <div class="block color">
            <h4><?=GetMessage("COLORS")?></h4>
            <ul id="color" class="selectable">
                <?foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {
                    if (strlen($arr["COLORS"]["PICTURE"]) > 0) {
                        $colorImg = CFile::ResizeImageGet($arr["COLORS"]["PICTURE"], array('width'=>31, 'height'=>25), BX_RESIZE_IMAGE_EXACT, true);
                    ?>
                <li<?if($color == $defaultColor){?> class="active selected"<?}?>><a href="<?=rtrim($arResult["DETAIL_PAGE_URL"], "/")?>/<?=$color?>/" rel="<?=$color?>"><img src="<?=$colorImg["src"]?>" title="<?=$arr["COLORS"]["COLOR"]?>" alt="<?=$arr["COLORS"]["COLOR"]?>" width="<?=$colorImg["width"]?>" height="<?=$colorImg["height"]?>" /></a></li>
                    <?} else {?>
                <li<?if($color == $defaultColor){?> class="active selected"<?}?>><a href="<?=rtrim($arResult["DETAIL_PAGE_URL"], "/")?>/<?=$color?>/" rel="<?=$color?>" style="background-color:#<?=$arr["COLORS"]["HEX"]?>"></a></li>
                    <?}?>
                <?}?>
            </ul>
        </div>

        <div class="block sizes">
            <h4><?=GetMessage("SIZES")?></h4>
            <p class="tip"><a href="<?=SITE_DIR?>include/table-sizes.jpg" target="_blank" alt="<?=GetMessage("SIZING_CHART")?>" title="<?=GetMessage("SIZING_CHART")?>"><?=GetMessage("HOW_TO_CHOOSE")?></a></p>
            <ul id="size" class="selectable">
                <?foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {
                    foreach ($arr["SIZES"] as $key => $size) {?>
                <li class="<?if($key == 0 && $color == $defaultColor){?>active selected<?}elseif($color != $defaultColor){?>hide<?}?>" rel="<?=$color?>"><span class="<?=$size["PRODUCT_ID"]?>"><?=$size["SIZE"]?></span></li>
                    <?}
                }?>
            </ul>
        </div>
        <script>$('.tip').click(function(e){window.open('<?=SITE_DIR?>include/table-sizes.php', '', 'width=565,scrollbars=yes'); e.preventDefault(); return false;});</script>

        <?$isPriceShow = true;
        foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {
            foreach ($arr["SIZES"] as $key => $size) {?>
                <div id="<?=$color . '-' . $size["PRODUCT_ID"]?>" class="<?if($isPriceShow && $color == $defaultColor){$isPriceShow = false; echo "show ";}else{echo "hide ";}?>block price<?=($size["PRICE"] != $size["DISCOUNT"] ? ' new' : '')?>" itemprop="offerDetails" itemscope itemtype="http://schema.org/Offer">
                    <?if ($size["PRICE"] != $size["DISCOUNT"]) {?>
                    <p class="oldprice"><?=CSiteFashionStore::formatMoney($size["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></p>
                    <?} else {?>
                    <p>&nbsp;</p>
                    <?}?>
                    <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
                    <p class="current-price">
                    	<input type="hidden" class="item-price-dis" value="<?=$size["DISCOUNT"]?>">
                        <span itemprop="price" class="item-price"><?=CSiteFashionStore::formatMoney($size["DISCOUNT"])?></span>&nbsp;<span class="rub"><?=GetMessage("RUB")?></span>
                        <span class="times">X</span>
                        <input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" class="item-quantity" value="1" />
                        <span class="count"> <?=GetMessage("CT_BCE_QUANTITY")?></span>
                        <input type="submit" class="add-to-cart" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?=GetMessage("CT_BCE_CATALOG_ADD")?>" />
                        <input type="hidden" name="<?=$arParams["ACTION_VARIABLE"]?>" value="BUY">
                        <input class="h_id" type="hidden" name="<?=$arParams["PRODUCT_ID_VARIABLE"]?>" value="<?=$size["PRODUCT_ID"]?>">
                    </p>
                    <meta itemprop="currency" content="RUB" />
                    </form>
                </div>
            <?}
        }?>
        <script>
            $(".add-to-cart").click(function(){
                cur_pr = $(this).parents('.current-price');
                itemID = cur_pr.find('.h_id').val();
                q = cur_pr.find('.item-quantity');
                if(itemID>0&&q.val()>0){
                    $.post('<?=SITE_DIR?>ajax/index.php', {id: itemID, q: q.val() }, function(data) {$('#top-cart').html(data);q.val('1')});
                }
                $("#cart-confirm h3 strong").html("<?=$arResult['NAME']?>");
                $("#cart-image").attr("src",$("#thumbs li:visible:first img").attr("src"));
                $('#cart-color').empty();
                $('#color li.selected a').clone().appendTo('#cart-color');
                $('#cart-color a').attr('href', 'javascript:void()');
                $('#cart-color img').css('width', 24).css('height', 16);
                $("#cart-size").text($("#size li.selected span").text());
                $("#cart-price").text(cur_pr.find('.item-price').text());
                $("#cart-quantity").text(q.val());
                total = parseInt(cur_pr.find('.item-price-dis').val())*parseInt($("#cart-quantity").text());
				total = total.toString();
				total = total.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1 ');

                $("#cart-overall").text(total);
                $("#cart-confirm").show().css("top",$(window).scrollTop()+($(window).height() - $("#cart-confirm").height())/2);
                $("#overlay").show();
                return false;
            })
        </script>


        <?if (strlen($arResult["DETAIL_TEXT"]) > 0 || strlen($arResult["PREVIEW_TEXT"]) > 0) {?>
        <div class="separator"></div>
        <div class="block description">
            <h3><?=GetMessage("DESCRIPTION")?></h3>
            <p itemprop="description">
            <?=(strlen($arResult["DETAIL_TEXT"]) > 0 ? $arResult["DETAIL_TEXT"] : $arResult["PREVIEW_TEXT"])?>
            </p>
        </div>
        <?}?>		
		
        <div class="separator"></div>
		
		<?// sets ?>
		<? if(!empty($arResult["SET"])): ?>
            <div class="block change-set">
                <h3 class="buy-caption"><?=GetMessage("BUY_WITH_PROFIT"); ?></h3>
                <div class="question-hover" onclick="return false;"></div>
                <?
                ob_start();
                $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/complect.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );
                $content = ob_get_clean();
                $content = str_replace("'", "", $content);
                ?>
                <script type="text/javascript">
                    $(function() {
                        $('.question-hover').qtip({
                            content: '<?= $content; ?>',
                            position: {
                                my: 'bottom middle',
                                at: 'top middle'
                            }
                        });
                    });
                </script>
                <div class="product-margin-top">
                <? foreach($arResult["SET"] as $set): ?>
                    <? $i = 1; foreach($set["PRODUCTS"] as $product): ?>
                        <a href="<?=$product["FIELDS"]["DETAIL_PAGE_URL"]; ?>" class="text-decoration-none set<?=$set["ID"];?>"
                            size="<?=$product["FIELDS"]["SIZE_NAME"];?>"
                            color="<?=$product["FIELDS"]["BACKGROUND"];?>"
                            name="<?=$product["FIELDS"]["NAME"];?>"
                            item-id="<?=$product["FIELDS"]["ID"];?>"

                            >
                            <img src="<?= $product["FIELDS"]["IMAGE"]; ?>" />
                        </a>
                        <? if($i < count($set["PRODUCTS"])):?>
                            <span class="plus">+</span>
                        <?endif;?>
                    <? $i++; endforeach; ?>
                    <br/>
                    <div class="product-width">
                        <span class="price-product-complect item-price">
							<h4><?=GetMessage("PRICE_PER_ONE");?></h4>
                            <span class="text-decoration-striken text">
                                <?=CSiteFashionStore::formatMoney($set["OLD_PRICE"]);?>&nbsp;<span class="rub"><?=GetMessage("RUB")?></span>
                            </span>
                        </span>

                        <span class="product-price-profit item-price">
							<h4><?=GetMessage("YOUR_PROFIT");?></h4>
							<span class="text">
                            <?=CSiteFashionStore::formatMoney($set["OLD_PRICE"] - $set["NEW_PRICE"]);?>&nbsp;<span class="rub"><?=GetMessage("RUB")?></span>
							</span>
                        </span>
                    </div>
                    <div class="complect-price-div item-price">
                        <span class="complect-price-span">
							<h4><?=GetMessage("PRICE_OF_SET");?></h4>
                            <div class="new-price-product"> <?=CSiteFashionStore::formatMoney($set["NEW_PRICE"]); ?>&nbsp;<span class="rub">Р</span></div>
                        </span>
                        <input type="submit" set-id="<?=$set["ID"];?>" set-name="<?=$set["NAME"];?>" price="<?=CSiteFashionStore::formatMoney($set["NEW_PRICE"]);?>&nbsp;<span class='rub'><?=GetMessage('RUB')?></span>" class="add-to-cart-set" value="<?= GetMessage("BUY_SET"); ?>">
                    </div>
                <? endforeach; ?>
                </div>

            </div>
        <? endif; ?>
        <? if(!empty($arResult["SETS"])): ?>
            <? foreach($arResult["SETS"] as $key => $arSet): ?>
                <div class="block change-set" id="change-set<?=$key;?>" style="display:none;">
                    <h3 class="buy-caption"><?=GetMessage("BUY_WITH_PROFIT"); ?></h3>
                    <div class="question-hover" onclick="return false;"></div>
                    <?
                    ob_start();
                    $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/complect.php",
                            "EDIT_TEMPLATE" => ""
                        ),
                        false
                    );
                    $content = ob_get_clean();
                    $content = str_replace("'", "", $content);
                    ?>
                    <script type="text/javascript">
                        $(function() {

                            //$('.question-hover').qtip({
                            //    content: '<?//=$content;?>//',
                            //    position: {
                            //        my: 'bottom middle',
                            //        at: 'top middle'
                            //    }
                            //});

                        });
                    </script>
                    <div class="product-margin-top">
                    <? foreach($arSet as $set): ?>
                        <? $i = 1; foreach($set["PRODUCTS"] as $product): ?>
                            <a href="<?=$product["FIELDS"]["DETAIL_PAGE_URL"]; ?>" class="text-decoration-none set<?=$set["ID"];?>"
                               size="<?=$product["FIELDS"]["SIZE_NAME"];?>"
                               color="<?=$product["FIELDS"]["BACKGROUND"];?>"
                               name="<?=$product["FIELDS"]["NAME"];?>"
                               item-id="<?=$product["FIELDS"]["ID"];?>"
                                >
                                <img src="<?= $product["FIELDS"]["IMAGE"]; ?>" />
                            </a>
                            <? if($i < count($set["PRODUCTS"])):?>
                                <span class="plus">+</span>
                            <?endif;?>
                        <? $i++; endforeach; ?>
                        <br/>
                        <div class="product-width">
                            <span class="price-product-complect item-price"><?=GetMessage("PRICE_PER_ONE");?>
                                <span class="text-decoration-striken">
                                    <?=CSiteFashionStore::formatMoney($set["OLD_PRICE"]); ?>&nbsp;<span class="rub"><?=GetMessage("RUB")?></span>
                                </span>
                            </span>

                            <span class="product-price-profit item-price"><?=GetMessage("YOUR_PROFIT");?>
                                <?= CSiteFashionStore::formatMoney($set["OLD_PRICE"] - $set["NEW_PRICE"]); ?>&nbsp;<span class="rub"><?=GetMessage("RUB")?></span>
                            </span>
                        </div>
                        <div class="complect-price-div item-price">
                            <span class="complect-price-span"><?=GetMessage("PRICE_OF_SET");?>
                                <div class="new-price-product"><?= CSiteFashionStore::formatMoney($set["NEW_PRICE"]); ?>&nbsp;<span class="rub">Р</span></div>
                            </span>
                            <input type="submit" set-id="<?=$set["ID"];?>" set-name="<?=$set["NAME"];?>" price="<?=CSiteFashionStore::formatMoney($set["NEW_PRICE"]);?>&nbsp;<span class='rub'><?=GetMessage('RUB')?></span>" class="add-to-cart-set" value="<?= GetMessage("BUY_SET"); ?>">
                        </div>
                    <? endforeach; ?>
                    </div>
                </div>
            <? endforeach; ?>
        <? endif; ?>
        <script type="text/javascript">
            $(function(){
                $(".add-to-cart-set").click(function(){
					console.log('++');
                    var selector = ".set" + $(this).attr("set-id");
                    var price = $(this).attr("price");
                    var set_name = $(this).attr("set-name");
                    $("#cart-confirm-insert-to").empty();
                    $(selector).each(function() {					
                        var id = $(this).attr("item-id");
                        var q = 1;
                        if(id>0&&q>0){
                            $.post('<?=SITE_DIR?>ajax/index.php', {id: id, q: q }, function(data) {$('#top-cart').html(data);});
                        }
                        var div = $("<div class='appended-div'></div>");
                        div.append("<div><p class='image'><img src='"+ $(this).find("img").attr("src") + "' /><div class='set-size'><?=GetMessage("SET_SIZE");?> <strong>" + $(this).attr("size") + "</strong></div><div class='set-color'><?=GetMessage("SET_COLOR");?> <span class='color'><a class='set-color-a' style='background:"+$(this).attr("color")+";'></a></span></div></p></div>");
                        $("#cart-confirm-insert-to").append(div);
                    });
                    $("#cart-confirm-set h3 strong").text(set_name);
                    $("#total-set").html('<?=GetMessage("TOTALLY");?>' + "<strong>" + price + "</strong>");

                    $("#cart-confirm-set").show().css("top",$(window).scrollTop()-100+($(window).height() - $("#cart-confirm-set").height())/2)
                        .css("left", $(window).scrollLeft()+($(window).width() - $("#cart-confirm-set").width())/2);
                    $("#overlay").show();
                    return false;
                });
            });
        </script>
		
        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/delivery_list.php"), false);?>
        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/return_list.php"), false);?>
		<?
		$arDisplay = array_udiff($arResult["DISPLAY_PROPERTIES"], $arSystemProps, "optShowProps");
		if(!empty($arResult["DISPLAY_PROPERTIES"])&&count($arDisplay)>0):?>
		<div class="list return">
			<h4><?=GetMessage("PROPS")?>:</h4>
			<ul>
				<?foreach($arDisplay as $prop):?>
					<li><?=$prop['NAME']?>:
					<?switch($prop['PROPERTY_TYPE']){
						case 'S':
						case 'N':
						case 'L':
							if($prop['MULTIPLE']=='Y'){
								echo implode(', ', $prop['VALUE']);		
							}else{
								echo $prop['VALUE'];
							}
							
							break;
					}
				?>
				</li>
				<?endforeach?>
			</ul>
		</div>
		<?endif;?>
        <div class="separator"></div>

        <div class="block reviews" id="reviews">
<?		$GLOBALS["arrFilter"]["PROPERTY_reviews_model"] = $arResult["ID"];
        $APPLICATION->IncludeComponent("bitrix:news.list", "reviews", Array(
            "DISPLAY_DATE" => "Y",    // Выводить дату элемента
            "DISPLAY_NAME" => "Y",    // Выводить название элемента
            "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
            "DISPLAY_PREVIEW_TEXT" => "Y",    // Выводить текст анонса
            "AJAX_MODE" => "N",    // Включить режим AJAX
            "IBLOCK_TYPE" => "content",    // Тип информационного блока (используется только для проверки)
            "IBLOCK_ID" => "6",    // Код информационного блока
            "NEWS_COUNT" => "5",    // Количество новостей на странице
            "SORT_BY1" => "ACTIVE_FROM",    // Поле для первой сортировки новостей
            "SORT_ORDER1" => "DESC",    // Направление для первой сортировки новостей
            "SORT_BY2" => "SORT",    // Поле для второй сортировки новостей
            "SORT_ORDER2" => "ASC",    // Направление для второй сортировки новостей
            "FILTER_NAME" => "arrFilter",    // Фильтр
            "FIELD_CODE" => "",    // Поля
            "PROPERTY_CODE" => array(    // Свойства
                0 => "reviews_user",
                1 => "reviews_rate",
            ),
            "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
            "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
            "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
            "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
            "SET_TITLE" => "N",    // Устанавливать заголовок страницы
            "SET_STATUS_404" => "N",    // Устанавливать статус 404, если не найдены элемент или раздел
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
            "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
            "PARENT_SECTION" => "",    // ID раздела
            "PARENT_SECTION_CODE" => "",    // Код раздела
            "CACHE_TYPE" => "N",    // Тип кеширования
            "CACHE_TIME" => "36000000",    // Время кеширования (сек.)
            "CACHE_FILTER" => "N",    // Кешировать при установленном фильтре
            "CACHE_GROUPS" => "Y",    // Учитывать права доступа
            "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
            "DISPLAY_BOTTOM_PAGER" => "Y",    // Выводить под списком
            "PAGER_TITLE" => "",    // Название категорий
            "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
            "PAGER_TEMPLATE" => "reviews",    // Название шаблона
            "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
            "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
            "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
            "AJAX_OPTION_STYLE" => "Y",    // Включить подгрузку стилей
            "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
            ),
            false
        );

        if ($USER->IsAuthorized()) {
        	$arPropCode = array(0, 0, 0);
			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>6, "CODE"=>"reviews_rate"));
			if($prop_fields = $properties->GetNext()){
				$arPropCode[0] = $prop_fields["ID"];
			}

			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>6, "CODE"=>"reviews_model"));
			if($prop_fields = $properties->GetNext()){
				$arPropCode[1] = $prop_fields["ID"];
			}
			
			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>6, "CODE"=>"reviews_user"));
			if($prop_fields = $properties->GetNext()){
				$arPropCode[2] = $prop_fields["ID"];
			}
            $APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "reviews", array(
                "IBLOCK_TYPE" => "content",
                "IBLOCK_ID" => "6",
                "STATUS_NEW" => "N",
                "LIST_URL" => "",
                "USE_CAPTCHA" => "N",
                "USER_MESSAGE_EDIT" => "save",
                "USER_MESSAGE_ADD" => "add",
                "DEFAULT_INPUT_SIZE" => "30",
                "RESIZE_IMAGES" => "N",
                "PROPERTY_CODES" => array(
                    0 => "NAME",
                    1 => "PREVIEW_TEXT",
                    2 => $arPropCode[0],
                    3 => $arPropCode[1],
					4 => $arPropCode[2]
                ),
                "PROPERTY_CODES_REQUIRED" => array(
                    0 => "PREVIEW_TEXT",
                    1 => $arPropCode[0],
					2 => $arPropCode[2]
                ),
                "GROUPS" => array(
                    0 => "2",
                ),
                "STATUS" => "ANY",
                "ELEMENT_ASSOC" => "PROPERTY_ID",
                "ELEMENT_ASSOC_PROPERTY" => "31",
                "MAX_USER_ENTRIES" => "100000",
                "MAX_LEVELS" => "100000",
                "LEVEL_LAST" => "N",
                "MAX_FILE_SIZE" => "0",
                "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
                "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
                "SEF_MODE" => "N",
                "SEF_FOLDER" => "/",
                "CUSTOM_TITLE_NAME" => "",
                "CUSTOM_TITLE_TAGS" => "",
                "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
                "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
                "CUSTOM_TITLE_IBLOCK_SECTION" => "",
                "CUSTOM_TITLE_PREVIEW_TEXT" => GetMessage("REVIEW"),
                "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
                "CUSTOM_TITLE_DETAIL_TEXT" => "",
                "CUSTOM_TITLE_DETAIL_PICTURE" => "",
                "NAME" => $arResult["NAME"],
                "MODEL_ID" => $arResult["ID"],
                "MODEL_IBLOCK_ID" => $arResult["IBLOCK_ID"]
                ),
                false
            );
        } else {?>
            <p class="disclaimer">
                <?=GetMessage("DISCLAIMER")?> <a href="<?=SITE_DIR?>auth/"><?=GetMessage("LOGIN")?></a>.
            </p>
        <?}?>
        </div>

        </div><!-- #item-content-->
    </div><!-- #item-container-->

    <div id="item-sidebar">
        <div class="rating">
            <p itemprop="review" itemscope itemtype="http://schema.org/Review-aggregate">
                <?if (intval($arResult["PROPERTIES"]["models_numvals"]["VALUE"]) > 0) {?>
                <a href="#reviews" class="review-count"><span itemprop="count"><?=CSiteFashionStore::declOfNum(intval($arResult["PROPERTIES"]["models_numvals"]["VALUE"]), array(GetMessage("NUMVAL_1"), GetMessage("NUMVAL_2"), GetMessage("NUMVAL_3")))?></span> &darr;</a>
                <?}?>
                <span itemprop="rating" class="review-value level-<?=intval($arResult["PROPERTIES"]["models_rating"]["VALUE"])?>"><?=intval($arResult["PROPERTIES"]["models_rating"]["VALUE"])?></span>
            </p>
        </div>

        <?if ($arResult["PROPERTIES"]["models_hit"]["VALUE_XML_ID"]=='yes' || $arResult["PROPERTIES"]["models_new"]["VALUE_XML_ID"]=='yes' || $isSale) {?>
        <ul class="shortcuts show">
            <?if ($arResult["PROPERTIES"]["models_hit"]["VALUE_XML_ID"]=='yes') {?><li class="hit show"><?=GetMessage("HIT")?></li><?}?>
            <?if ($arResult["PROPERTIES"]["models_new"]["VALUE_XML_ID"]=='yes') {?><li class="new show"><?=GetMessage("NEW")?></li><?}?>
            <?if ($isSale) {?><li class="discount show"><?=GetMessage("SALE")?></li><?}?>
        </ul>
        <?}?>

        <div class="big-image">
            <div class="zoom-label"><span><?=GetMessage("ZOOM")?></span></div>
            <div id="big-image">
            <?foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {
                if ($color == $defaultColor) {
                    $medium = CFile::ResizeImageGet($arr["PHOTOS"][0]["ID"], array('width'=>450, 'height'=>450), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <a href="<?=$arr["PHOTOS"][0]["SRC"]?>" class="cloud-zoom" id="zoom1" rel="adjustX: 100, adjustY:-4" style="width:<?=$medium["width"]?>px"><img src="<?=$medium["src"]?>" itemprop="image" alt="<?=$arResult["NAME"]?>" width="<?=$medium["width"]?>" height="<?=$medium["height"]?>" /></a>
                    <?break;//first item only
                }
            }?>
            </div>
        </div>


        <?if (is_array($arResult["PROPERTIES"]["models_video"]["VALUE"]) && strlen($arResult["PROPERTIES"]["models_video"]["~VALUE"]["TEXT"]) > 0) {?>
        <div id="video-tab">
            <?=$arResult["PROPERTIES"]["models_video"]["~VALUE"]["TEXT"]?>
        </div>
        <?}?>
        <div class="thumbs">
            <ul id="thumbs" class="selectable">
                <?foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {

                        foreach ($arr["PHOTOS"] as $key => $arrVal) {
                            $medium = CFile::ResizeImageGet($arrVal["ID"], array('width'=>450, 'height'=>450), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            $thumb  = CFile::ResizeImageGet($arrVal["ID"], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, true);

                            $bigJs    .= "'" . $arrVal["SRC"] . "', ";
                            $mediumJs .= "'" . $medium["src"] . "', ";?>
                <li<?if($color == $defaultColor){?> class="<?=($key == 0 ? "active selected " : "")?><?=(count($arr["PHOTOS"]) > 1 ? "show" : "")?>"<?}?> rel="<?=$color?>"><a href="<?=$arrVal["SRC"]?>" class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?=$medium["src"]?>', width: '<?=$medium["width"]?>', height: '<?=$medium["height"]?>'"><span></span><img src="<?=$thumb["src"]?>" width="<?=$thumb["width"]?>" height="<?=$thumb["height"]?>" /></a></li>
                        <?}

                }?>
                <?if (is_array($arResult["PROPERTIES"]["models_video"]["VALUE"]) && strlen($arResult["PROPERTIES"]["models_video"]["~VALUE"]["TEXT"]) > 0) {?>
                <li id="video"><a href="#"><span></span><img src="<?=SITE_TEMPLATE_PATH?>/i/ico-video.png" /></a></li>
                <?}?>
            </ul>
        </div>
        <script>
        (function() {
            var bArr = [<?=rtrim($bigJs, ", ")?>], mArr = [<?=rtrim($mediumJs, ", ")?>];
            $.preloadImg(bArr);
            $.preloadImg(mArr);
        })();
        </script>


        <div class="social-bookmarks">
            <script src="//yandex.st/share/share.js" charset="utf-8"></script>
            <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
        </div>
		<?if (!empty($arResult["VIEW_PRODUCTS"])) {?>
        <div class="block related">
            <h3><?=GetMessage("VIEW")?></h3>
            <ul>
				<?foreach ($arResult["VIEW_PRODUCTS"] as $product) {
                    $simImg = CFile::ResizeImageGet($product["PROPERTIES"]["item_more_photo"]["VALUE"][0], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <li><a href="<?=$product["DETAIL_PAGE_URL"]?>" alt="<?=$product["NAME"]?>" title="<?=$product["NAME"]?>"><span></span><img src="<?=$simImg["src"]?>" width="<?=$simImg["width"]?>" height="<?=$simImg["height"]?>" alt="<?=$product["NAME"]?>" /></a></li>
                <?}?>
            </ul>
        </div>
		<?}?>
		<?if (!empty($arResult["VIEWED_PRODUCTS"])) {?>
        <div class="block related">
            <h3><?=GetMessage("VIEWED")?></h3>
            <ul>
				<?foreach ($arResult["VIEWED_PRODUCTS"] as $product) {
                    $simImg = CFile::ResizeImageGet($product["PROPERTIES"]["item_more_photo"]["VALUE"][0], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <li><a href="<?=$product["DETAIL_PAGE_URL"]?>" alt="<?=$product["NAME"]?>" title="<?=$product["NAME"]?>"><span></span><img src="<?=$simImg["src"]?>" width="<?=$simImg["width"]?>" height="<?=$simImg["height"]?>" alt="<?=$product["NAME"]?>" /></a></li>
                <?}?>
            </ul>
        </div>
		<?}?>
        <?if (!empty($arResult["SIMILAR_PRODUCTS"])) {?>
        <div class="block similar">
            <h3><?=GetMessage("SIMILAR")?></h3>
            <ul>
                <?foreach ($arResult["SIMILAR_PRODUCTS"] as $product) {
                    $simImg = CFile::ResizeImageGet($product["PROPERTIES"]["item_more_photo"]["VALUE"][0], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <li><a href="<?=$product["DETAIL_PAGE_URL"]?>" alt="<?=$product["NAME"]?>" title="<?=$product["NAME"]?>"><span></span><img src="<?=$simImg["src"]?>" width="<?=$simImg["width"]?>" height="<?=$simImg["height"]?>" alt="<?=$product["NAME"]?>" /></a></li>
                <?}?>
            </ul>
        </div>
        <?}?>
    </div><!-- .sidebar#sideLeft -->
</div>
<?}?>
<?if($USER->IsAuthorized()):?>
<script>
function checkWishList()
{
    var product_id = $(".wishlist").attr("product-id");
    var status = "check";
	$("#not_in_wishlist, #in_wishlist, #wishpic").hide();
	$("#wishloader").show();
	
    $.ajax({
        type: "post",
        url: "<?=$templateFolder?>/ajax/wishlist.php",
        data: {productId: product_id, status: status},
        dataType: "json",
        success: function(json) {
            if (json.success)
            {
                if(json.isinwishlist)
                {
                    $(".wishlist").show().addClass("selected");
                    $("#in_wishlist, #wishloader").hide();
                    $("#not_in_wishlist").show();
                }
                else
                {
                    $(".wishlist").show().removeClass("selected");
					$("#not_in_wishlist, #wishloader").hide();
                    $("#in_wishlist").show();                    
                }
            }
        }
    });
}
checkWishList();

$(".wishlist-caption").click(function() {
	$(this).parent().find(".wishlist").click();
});
$(".wishlist").click(function() {
	$(this).toggleClass("selected");
	var product_id = $(this).attr("product-id"), status;
	if($(this).hasClass("selected")) {
		$("#in_wishlist").hide();
		$("#not_in_wishlist").show();
		status = "add";
	}
	else {
		$("#not_in_wishlist").hide();
		$("#in_wishlist").show();		
		status = "remove";
	}
	$.ajax({
		type: "post",
		url: "<?=$templateFolder?>/ajax/wishlist.php",
		data: {productId: product_id, status: status},
		dataType: "json",
		success: function(json) {

		}
	});
});
</script>
<?endif;?>