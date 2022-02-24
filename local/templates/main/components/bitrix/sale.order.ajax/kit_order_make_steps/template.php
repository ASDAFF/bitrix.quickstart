<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$this->addExternalJs($templateFolder."/logic.js");

if($arResult["GRID"]["ROWS"])
{
    // counting the quantity of products
    foreach($arResult["GRID"]["ROWS"] as $product)
    {
        $quantity += $product["data"]["QUANTITY"];
    }

    $mod10 = $quantity % 10;
    $mod100 = $quantity % 100;
    $quantityLabel = Loc::getMessage("KIT_SOA_QUANTITY_LABEL_DEFAULT");
    if(($mod10 > 1 && $mod10 < 5) && ($mod100 < 12 || $mod100 > 14))
        $quantityLabel .= Loc::getMessage("KIT_SOA_QUANTITY_LABEL_V1");
    elseif($mod10 == 0 || ($mod10 > 4 && $mod10 < 10) || ($mod100 > 10 && $mod100 < 15))
        $quantityLabel .= Loc::getMessage("KIT_SOA_QUANTITY_LABEL_V2");
}

if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			$APPLICATION->RestartBuffer();
			?>
			<script>
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}
	}
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.order.ajax');
$messages = Loc::loadLanguageFile(__FILE__);

$arParams['MESS_USE_COUPON'] = Loc::getMessage("USE_COUPON_DEFAULT");
$arParams['MESS_COUPON'] = Loc::getMessage("COUPON_DEFAULT");
$arParams['MESS_ECONOMY'] = Loc::getMessage("SOA_SUM_DISCOUNT");
$arParams['MESS_PRICE_FREE'] = Loc::getMessage("PRICE_FREE_DEFAULT");
?>

<div id="kit_soa">

    <div id="order_form_div">

        <NOSCRIPT>
            <div class="errortext"><?=Loc::getMessage("SOA_NO_JS")?></div>
        </NOSCRIPT>

        <?
        if(!function_exists("getColumnName"))
        {
            function getColumnName($arHeader)
            {
                return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : Loc::getMessage("SALE_".$arHeader["id"]);
            }
        }
        if(!function_exists("cmpBySort"))
        {
            function cmpBySort($array1, $array2)
            {
                if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
                    return -1;

                if ($array1["SORT"] > $array2["SORT"])
                    return 1;

                if ($array1["SORT"] < $array2["SORT"])
                    return -1;

                if ($array1["SORT"] == $array2["SORT"])
                    return 0;
            }
        }
        ?>

        <div>
            <?
            if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
            {
                if(!empty($arResult["ERROR"]))
                {
                    foreach($arResult["ERROR"] as $v)
                        echo ShowError($v);
                }
                elseif(!empty($arResult["OK_MESSAGE"]))
                {
                    foreach($arResult["OK_MESSAGE"] as $v)
                        echo ShowNote($v);
                }

                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
            }
            else
            {
                if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
                {
                    if(strlen($arResult["REDIRECT_URL"]) == 0)
                    {
                        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
                    }
                }
                else
                {
                    ?>
                    <script>
                        <?if(CSaleLocation::isLocationProEnabled()):?>
                            <?
                            // spike: for children of cities we place this prompt
                            $city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
                            ?>
                            BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
                                'source' => $this->__component->getPath().'/get.php',
                                'cityTypeId' => intval($city['ID']),
                                'messages' => array(
                                    'otherLocation' => '--- '.Loc::getMessage('SOA_OTHER_LOCATION'),
                                    'moreInfoLocation' => '--- '.Loc::getMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                                    'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.Loc::getMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.Loc::getMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
                                        '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                                        '#ANCHOR_END#' => '</a>'
                                    )).'</div>'
                                )
                            ))?>);
                        <?endif?>

                        var BXFormPosting = false;
                        function submitForm(val)
                        {
	                        if(val == 'Y')
	                        {
		                        if (!validation())
			                        return false;
	                        }
                            BX.addCustomEvent("reject", function() { // bad solution
                                if(BXFormPosting)
                                    document.location.reload();
                            });

                            if(BXFormPosting === true)
                            {
                                return true;
                            }

                            BXFormPosting = true;
                            if(val != 'Y')
                            {
                                BX('confirmorder').value = 'N';
                                //console.log('not Y');
                                //BX.UserConsent.current.inputNode.checked = true;
                            }

                            var orderForm = BX('ORDER_FORM');
                            //BX.showWait();

                            <?if(CSaleLocation::isLocationProEnabled()):?>
                                BX.saleOrderAjax.cleanUp();
                            <?endif?>

                            BX.ajax.submit(orderForm, ajaxResult);

                            return true;
                        }

                        function ajaxResult(res)
                        {
                            var orderForm = BX('ORDER_FORM');
                            try
                            {
                                //console.log('try');

                                // if json came, it obviously a successfull order submit
                                var json = JSON.parse(res);

                                //BX.closeWait();

                                if(json.error)
                                {
                                    BXFormPosting = false;
                                    return;
                                }
                                else if(json.redirect)
                                {
                                    window.top.location.href = json.redirect;
                                }
                            }
                            catch(e)
                            {
                                //console.log('catch');
                                //console.log(BX.OrderAjaxLogic.result);

                                // json parse failed, so it is a simple chunk of html
                                BXFormPosting = false;
                                BX('order_form_content').innerHTML = res;

                                BX.OrderAjaxLogic.sendRequest('refreshOrderAjax');

                                /* new coupon block initialization when submit form */
                                BX.OrderAjaxLogic.init({
                                    result: <?=CUtil::PhpToJSObject($arResult['JS_DATA'])?>,
                                    params: <?=CUtil::PhpToJSObject($arParams)?>,
                                    signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
                                    siteID: '<?=$component->getSiteId()?>',
                                    ajaxUrl: '<?=CUtil::JSEscape($component->getPath().'/ajax.php')?>',
                                    templateFolder: '<?=CUtil::JSEscape($templateFolder)?>',

                                    orderBlockId: 'coupon_block',
                                    basketBlockId: 'coupon_block',
                                    basketRowsId: 'basket_rows',
                                    kitSoaBlockId: 'kit_soa',
                                    totalBlockId: 'kit_soa_total'
                                });

                                <?if(CSaleLocation::isLocationProEnabled()):?>
                                    BX.saleOrderAjax.initDeferredControl();
                                <?endif?>
                            }

                            //BX.closeWait();

                            BX.onCustomEvent(orderForm, 'onAjaxSuccess');

                            verticalTabs(soa_vt);
                            verticalTabsForMobile(false);
                        }

                        function SetContact(profileId)
                        {
                            BX("profile_change").value = "Y";
                            submitForm();
                        }
                    </script>
                    <?
                    if($_POST["is_ajax_post"] != "Y")
                    {
                        ?>
                        <form action="<?=POST_FORM_ACTION_URI?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
                            <?=bitrix_sessid_post()?>

                            <div id="order_form_content">
                        <?
                    }
                    else
                    {
                        $APPLICATION->RestartBuffer();
                    }
                    ?>

                    <div class="row main_order row-no-margin">
                        <div class="col-xl-9 col-lg-9 col-md-12">
                            <?
                            if($_REQUEST['PERMANENT_MODE_STEPS'] == 1)
                            {
                                ?>
                                <input type="hidden" name="PERMANENT_MODE_STEPS" value="1">
                                <?
                            }
                            if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
                            {
                                ?>
                                <div class="errors_list">
                                    <?
                                    foreach($arResult["ERROR"] as $v)
                                        echo '<span class="errortext">' . $v . '</span>';
                                    ?>
                                </div>
                                <script>
                                    top.BX.scrollToNode(top.BX('ORDER_FORM'));
                                </script>
                                <?
                            }
                            ?>

                            <div class="pay_info_block">
                                <div class="admin-panel">
                                    <div class="slide_bar_block">
                                        <ul class="slide_bar_block__list">
                                            <li class="slide_bar_tab1">
                                                <a href="#" name="tab1">
                                                    <span class="step_icons_item icon-wallet"></span>
                                                    <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_PAYMENT_INFORMATION")?></p>
                                                </a>
                                            </li>
                                            <?
                                            if($arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
                                            {
                                                ?>
                                                <li class="slide_bar_tab2">
                                                    <a href="#" name="tab2">
                                                        <span class="step_icons_item icon-shipped"></span>
                                                        <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_DELIVERY")?></p>
                                                    </a>
                                                </li>
                                                <li class="slide_bar_tab3">
                                                    <a href="#" name="tab3">
                                                        <span class="step_icons_item icon-money-bag"></span>
                                                        <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_PAYMENT")?></p>
                                                    </a>
                                                </li>
                                                <?
                                            }
                                            else
                                            {
                                                ?>
                                                <li class="slide_bar_tab2">
                                                    <a href="#" name="tab2">
                                                        <span class="step_icons_item icon-money-bag"></span>
                                                        <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_PAYMENT")?></p>
                                                    </a>
                                                </li>
                                                <li class="slide_bar_tab3">
                                                    <a href="#" name="tab3">
                                                        <span class="step_icons_item icon-shipped"></span>
                                                        <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_DELIVERY")?></p>
                                                    </a>
                                                </li>
                                                <?
                                            }
                                            ?>
                                            <li class="slide_bar_tab4">
                                                <a href="#" name="tab4">
                                                    <span class="step_icons_item icon-success_order"></span>
                                                    <p class="step_icons_comment fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_ORDERING")?></p>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="slide_bar_block__content">

                                        <div class="slide_bar_block__content_item" id="tab1">
                                            <?
                                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
                                            ?>
                                        </div>
                                        <?
                                        if($arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
                                        {
                                            ?><div class="slide_bar_block__content_item" id="tab2"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?></div><?
                                            ?><div class="slide_bar_block__content_item" id="tab3"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?></div><?
                                        }
                                        else
                                        {
                                            ?><div class="slide_bar_block__content_item" id="tab2"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?></div><?
                                            ?><div class="slide_bar_block__content_item" id="tab3"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?></div><?
                                        }
                                        ?>

                                        <div class="slide_bar_block__content_item" id="tab4">
                                            <h2 class="pay_info_block__step_title fonts__main_text"><?=Loc::getMessage("KIT_SOA_ORDERING")?></h2>

                                            <div class="slide_bar_block__content_item__info">

                                                <div class="content_promocod_block">
                                                    <div class="content_promocod_block_title fonts__middle_text">
                                                        <?=Loc::getMessage("COUPON_DEFAULT");?>
                                                    </div>
                                                    <div id="coupon_block" class="content_promocod_block_form" style="padding-top: 0;">
                                                        <div class="bx-soa-section-content"></div>
                                                    </div>
                                                    <div id="kit_soa_total" style="display: none;"></div>
                                                </div>

                                                <div class="cntr" style="width: 100%; margin-bottom: 1rem;">
                                                    <?
                                                    if($arParams['USER_CONSENT'] === 'Y')
                                                    {
                                                        $APPLICATION->IncludeComponent(
                                                            "bitrix:main.userconsent.request",
                                                            "kit_userconsent_request",
                                                            array(
                                                                'ID' => $arParams['USER_CONSENT_ID'],
                                                                'IS_CHECKED' => $arParams['USER_CONSENT_IS_CHECKED'],
                                                                'IS_LOADED' => $arParams['USER_CONSENT_IS_LOADED'],
                                                                'AUTO_SAVE' => 'N',
                                                                'REPLACE' => array(
                                                                    'button_caption' => Loc::getMessage("SOA_TEMPL_BUTTON"),
                                                                    'fields' => $arResult['USER_CONSENT_PROPERTY_DATA']
                                                                )
                                                            )
                                                        );
                                                    }
                                                    ?>
                                                </div>

                                                <div id="kit-bx-soa-orderSave" class="content_promocod_block_order">
                                                    <input type="submit" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON" class="main_btn btn-yellow sweep-to-right" value="<?=Loc::getMessage("SOA_TEMPL_BUTTON")?>">
                                                </div>

                                            </div>
                                        </div>

                                        <p id="blink" class="slide_bar_block__content__button_next">
                                            <span><?=Loc::getMessage("KIT_SOA_GO_TO_NEXT_STEP")?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?
                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
                            //include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php");

                            if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
                                echo $arResult["PREPAY_ADIT_FIELDS"];
                            ?>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-12 main_order_all_price">
                            <div class="box-all-price" id="all_price">
                            <div class="main_order_block__top_line top_line_all_price">
                                <span class="main_order_block__title fonts__main_text"><?=Loc::getMessage("KIT_SOA_YOUR_ORDER")?></span>
                                <a class="main_order_block__link" href="<?=$arParams["PATH_TO_BASKET"]?>"><?=Loc::getMessage("KIT_SOA_CHANGE")?></a>
                            </div>
                            <div class="main_order_block_feedback price_item">
                                <div class="main_order_block_feedback__price_item fonts__small_text">
                                    <span class="main_order_block_feedback__price_title">
                                        <b><?=$quantity?></b> <?=$quantityLabel?> <?=Loc::getMessage("SOA_TEMPL_SUM_SUMMARY")?>
                                    </span>
                                    <span class="main_order_block_feedback__price_title" style="text-align: right;">
                                        <b class="kit_soa_order_price"><?=$arResult["ORDER_PRICE_FORMATED"]?></b>
                                    </span>
                                </div>
                                <?
                                if(floatval($arResult['ORDER_WEIGHT']) > 0)
                                {
                                    ?>
                                    <div class="main_order_block_feedback__price_item fonts__small_text">
                                        <span class="main_order_block_feedback__price_title"><?=Loc::getMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></span>
                                        <span class="main_order_block_feedback__price_title"><b><?=$arResult["ORDER_WEIGHT_FORMATED"]?></b></span>
                                    </div>
                                    <?
                                }
                                if(doubleval($arResult["DISCOUNT_PRICE"]) > 0)
                                {
                                    ?>
                                    <div class="kit_soa_discount_total_block main_order_block_feedback__price_item fonts__small_text">
                                        <span class="main_order_block_feedback__price_title">
                                            <?=Loc::getMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if(strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>
                                        </span>
                                        <span class="main_order_block_feedback__price_title main_order_price_title_sale">
                                            <b class="kit_soa_discount_value"><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></b>
                                        </span>
                                    </div>
                                    <?
                                }
                                if(!empty($arResult["TAX_LIST"]))
                                {
                                    foreach($arResult["TAX_LIST"] as $val)
                                    {
                                        ?>
                                        <div class="main_order_block_feedback__price_item fonts__small_text">
                                            <span><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</span>
                                            <span style="text-align: right;"><b><?=$val["VALUE_MONEY_FORMATED"]?></b></span>
                                        </div>
                                        <?
                                    }
                                }
                                if(doubleval($arResult["DELIVERY_PRICE"]) > 0)
                                {
                                    ?>
                                    <div class="main_order_block_feedback__price_item fonts__small_text">
                                        <span class="main_order_block_feedback__price_title"><?=Loc::getMessage("SOA_TEMPL_SUM_DELIVERY")?></span>
                                        <span class="main_order_block_feedback__price_title"><b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b></span>
                                    </div>
                                    <?
                                }
                                if(strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
                                {
                                    ?>
                                    <div>
                                        <span><?=Loc::getMessage("SOA_TEMPL_SUM_IT")?></span>
                                        <span><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></span>
                                    </div>
                                    <div>
                                        <span><?=Loc::getMessage("SOA_TEMPL_SUM_PAYED")?></span>
                                        <span><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></span>
                                    </div>
                                    <div>
                                        <span><?=Loc::getMessage("SOA_TEMPL_SUM_LEFT_TO_PAY")?></span>
                                        <span><?=$arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"]?></span>
                                    </div>
                                    <?
                                }
                                else
                                {
                                    ?>
                                    <div class="main_order_block_feedback__price_item fonts__main_text all_price">
                                        <span class="main_order_block_feedback__price_title"><?=Loc::getMessage("SOA_TEMPL_SUM_IT")?></span>
                                        <span class="main_order_block_feedback__price_title"><span class="kit_soa_order_total_price"><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></span></span>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                            </div>
                        </div>
                    </div>

                    <?
                    if($_POST["is_ajax_post"] != "Y")
                    {
                        ?>
                            </div>
                            <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
                            <input type="hidden" name="profile_change" id="profile_change" value="N">
                            <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
                            <input type="hidden" name="json" value="Y">

                            <div class="order_block__basket_link">
                                <a class="order_block__ordering_link fonts__middle_comment" href="<?=$arParams["PATH_TO_BASKET"]?>">
                                    <i class="fas fa-angle-double-left"></i>
                                    <?=Loc::getMessage("KIT_SOA_BACK_TO_BASKET");?>
                                </a>
                            </div>

                        </form>
                        <?
                        if($arParams["DELIVERY_NO_AJAX"] == "N")
                        {
                            ?>
                            <div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
                            <?
                        }
                    }
                    else
                    {
                        ?>
                        <script>
                            top.BX('confirmorder').value = 'Y';
                            top.BX('profile_change').value = 'N';
                        </script>
                        <?
                        die();
                    }
                }
            }
            ?>
        </div>

    </div>

    <?if(CSaleLocation::isLocationProEnabled()):?>

        <div style="display: none">
            <?
            // we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it
            $APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.steps",
                ".default",
                array(),
                false
            );
            $APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.search",
                ".default",
                array(),
                false
            );
            ?>
        </div>

    <?endif?>

</div>

<script>
    BX.message(<?=CUtil::PhpToJSObject($messages)?>);
    BX.OrderAjaxLogic.init({
        result: <?=CUtil::PhpToJSObject($arResult['JS_DATA'])?>,
        params: <?=CUtil::PhpToJSObject($arParams)?>,
        signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
        siteID: '<?=$component->getSiteId()?>',
        ajaxUrl: '<?=CUtil::JSEscape($component->getPath().'/ajax.php')?>',
        templateFolder: '<?=CUtil::JSEscape($templateFolder)?>',

        orderBlockId: 'coupon_block',
        basketBlockId: 'coupon_block',
        basketRowsId: 'basket_rows',
        kitSoaBlockId: 'kit_soa',
        totalBlockId: 'kit_soa_total'
    });

    function verticalTabs(number) {
        number = number || 0;
        soa_vt = number;

        var soa_length = $(".slide_bar_block li").length - 1;
        var arrow = $(".slide_bar_block__content__button_next");

        if(soa_vt == soa_length)
            arrow.hide();

        $(".slide_bar_block__content > div").hide();
        $(".slide_bar_block li:eq(" + number + ")").attr("id", "active");
        $(".slide_bar_block__content > div:eq(" + number + ")").fadeIn();

        $('.slide_bar_block a').click(function(e) {
            e.preventDefault();

            if(!validation())
                return false;

            soa_vt = $(this).parent().index();

            if(soa_vt != soa_length)
                arrow.fadeIn();
            else
                arrow.hide();

            if($(this).closest("li").attr("id") == "active")
                return;
            else
            {
                $(".slide_bar_block__content > div").hide();
                $(".slide_bar_block li").attr("id", "");
                $(this).parent().attr("id", "active");
                $('#' + $(this).attr('name')).show();

                $("html, body").animate({scrollTop: $(".pay_info_block").offset().top + "px"}, "slow");
            }
        });
    }

    function calcHeightBlocks(arr) {
        let resultHeight = 0;
        arr.forEach(function(item){
            if(document.querySelector(item)) {
                resultHeight += document.querySelector(item).offsetHeight;
            };
        });

        return resultHeight;
    }



    function verticalTabsNextStepClick() {
        $('#order_form_content').on('click', '.slide_bar_block__content__button_next', function() {

            if(!validation())
                return false;

            var current = $(this).parent().children('div:visible');
            var next = current.next();
            var heightFixedBlocks = calcHeightBlocks(['#bx-panel', '.fix-header-two .header-two__main-wrapper', '.fix-header-two .header-two__nav.active']);
            soa_vt = next.index();

            var currentActive = $(".slide_bar_block li#active");
            currentActive.attr("id", "");
            currentActive.next().attr("id", "active");

            if(!$(".slide_bar_block li#active").next().length)
                $(this).hide();

            current.hide();
            $("html, body").animate({scrollTop: $(".pay_info_block").offset().top - heightFixedBlocks + "px"}, "slow");
            next.show();
        });
    }

    function verticalTabsForMobile(scroll = true) {
        if(window.matchMedia("(max-width: 767px)").matches) {
            if(scroll) {
                $(window).scroll(function(e) {
                    var scrollPos = $(window).scrollTop();
                    var topBlock = $(".pay_info_block").offset().top - $(".slide_bar_block").height();
                    if(scrollPos > topBlock && scrollPos < topBlock + $(".pay_info_block").height()) {
                        $(".slide_bar_block").addClass("fixed");
                    }
                    else {
                        $(".slide_bar_block").removeClass("fixed");
                    }
                });
            }
            else {
                var scrollPos = $(window).scrollTop();
                var topBlock = $(".pay_info_block").offset().top - $(".slide_bar_block").height();
                if(scrollPos > topBlock && scrollPos < topBlock + $(".pay_info_block").height()) {
                    $(".slide_bar_block").addClass("fixed");
                }
                else {
                    $(".slide_bar_block").removeClass("fixed");
                }
            }
        }
    }

    function validation() {
        var fieldsBlock = $("#sale_order_props div[data-property-id-row] .info_order_item_content__input_item");
        var elems = fieldsBlock.find("> input, > textarea");
        var form = $("#order_form_content");

        elems.each(function(i, elem) {
            if($(elem).hasClass("required") && !elem.value) {
                $(elem).addClass("has-error");
                form.addClass("has-errors");
            }
            else {
                $(elem).removeClass("has-error");
            }

            $(elem).change();
        });

        if(!form.find(".has-error").length) {
            form.removeClass("has-errors");
        }

        if(form.hasClass("has-errors")) {
            var heightFixedBlocks = calcHeightBlocks(['#bx-panel', '.fix-header-two .header-two__main-wrapper', '.fix-header-two .header-two__nav.active']);
            var OFFSET = 20;
            $("html, body").animate({scrollTop: $(".has-error").offset().top - heightFixedBlocks - OFFSET + "px"}, "slow");
            return false;
        } else {
            return true;
        }
    }

    function onChangeValidation(el) {
        if($(el).hasClass("emailfield")) {
            var pattern = /^.+@.+[.].{2,}$/i;
            if (!pattern.test(el.value)) {
                $(el).addClass("has-error");
            }
            else {
                $(el).removeClass("has-error");
            }
        }
        else if($(el).hasClass("phonefield")) {
            var pattern = /^[\d\s\(\)\-\+]+$/;
            if (!pattern.test(el.value)) {
                $(el).addClass("has-error");
            }
            else {
                $(el).removeClass("has-error");
            }
        }
        else
        {
            if(el.value != "")
                $(el).removeClass("has-error");
        }
    }

    $(document).ready(function() {
        verticalTabs();
        verticalTabsForMobile();
	    verticalTabsNextStepClick();
    });
</script>
