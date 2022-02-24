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

                                validation();

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

                                setWidthForTable();

                                <?if(CSaleLocation::isLocationProEnabled()):?>
                                    BX.saleOrderAjax.initDeferredControl();
                                <?endif?>
                            }

                            //BX.closeWait();

                            BX.onCustomEvent(orderForm, 'onAjaxSuccess');
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

                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");

                    ?>
                    <div class="row info_order no-padding">
                        <div class="col">
                            <div class="info_order_item">
                                <div class="info_order_item_content">
                                    <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php");?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row info_order no-padding">
                        <?
                        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
                        
                        if($arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
                        {
                            ?><div class="col-md-6 mb-4"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?></div><?
                            ?><div class="col-md-6 mb-4"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?></div><?
                        }
                        else
                        {
                            ?><div class="col-md-6 mb-4"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?></div><?
                            ?><div class="col-md-6 mb-4"><?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?></div><?
                        }
                        ?>
                    </div>
                    <?

                    //include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php");

                    if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
                        echo $arResult["PREPAY_ADIT_FIELDS"];
                    ?>

                    <div class="price_order_block">
                        <div class="price_order_block__item fonts__small_text">
                            <b><?=$quantity?></b> <?=$quantityLabel?> <?=Loc::getMessage("SOA_TEMPL_SUM_SUMMARY")?> <b class="kit_soa_order_price"><?=$arResult["ORDER_PRICE_FORMATED"]?></b>
                        </div>
                        <?
                        if(floatval($arResult['ORDER_WEIGHT']) > 0)
                        {
                            ?>
                            <div class="price_order_block__item fonts__small_text">
                                <?=Loc::getMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?> <b><?=$arResult["ORDER_WEIGHT_FORMATED"]?></b>
                            </div>
                            <?
                        }
                        if(doubleval($arResult["DISCOUNT_PRICE"]) > 0)
                        {
                            ?>
                            <div class="kit_soa_discount_total_block price_order_block__item fonts__small_text">
                                <?=Loc::getMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if(strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>
                                <b class="kit_soa_discount_value"><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></b>
                            </div>
                            <?
                        }
                        if(!empty($arResult["TAX_LIST"]))
                        {
                            foreach($arResult["TAX_LIST"] as $val)
                            {
                                ?>
                                <div class="price_order_block__item fonts__small_text">
                                    <?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:
                                    <b><?=$val["VALUE_MONEY_FORMATED"]?></b>
                                </div>
                                <?
                            }
                        }
                        if(doubleval($arResult["DELIVERY_PRICE"]) > 0)
                        {
                            ?>
                            <div class="price_order_block__item fonts__small_text">
                                <?=Loc::getMessage("SOA_TEMPL_SUM_DELIVERY")?> <b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b>
                            </div>
                            <?
                        }
                        if(strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
                        {
                            ?>
                            <div>
                                <?=Loc::getMessage("SOA_TEMPL_SUM_IT")?>
                                <?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?>
                            </div>
                            <div>
                                <?=Loc::getMessage("SOA_TEMPL_SUM_PAYED")?>
                                <?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?>
                            </div>
                            <div>
                                <?=Loc::getMessage("SOA_TEMPL_SUM_LEFT_TO_PAY")?>
                                <?=$arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"]?>
                            </div>
                            <?
                        }
                        else
                        {
                            ?>
                            <div class="price_order_block__item fonts__small_text">
                                <?=Loc::getMessage("SOA_TEMPL_SUM_IT")?> <b class="kit_soa_order_total_price"><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></b>
                            </div>
                            <?
                        }
                        ?>
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

                            <div class="order_block__ordering">
                                <a class="order_block__ordering_link fonts__middle_comment" href="<?=$arParams["PATH_TO_BASKET"]?>">
                                    <i class="fas fa-angle-double-left"></i>
                                    <?=Loc::getMessage("KIT_SOA_BACK_TO_BASKET");?>
                                </a>

                                <div class="cntr">
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
                                
                                <div id="kit-bx-soa-orderSave" class="order_block__ordering_btn">
                                    <input type="submit" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON" class="main_btn sweep-to-right" value="<?=Loc::getMessage("SOA_TEMPL_BUTTON")?>">
                                </div>
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

    $(document).ready(function() {

        (function($, sr) {
            var debounce = function(func, threshold, execAsap) {
                var timeout;
                return function debounced() {
                    var obj = this, args = arguments;
                    function delayed() {
                        if (!execAsap)
                            func.apply(obj, args);
                        timeout = null;
                    };

                    if (timeout)
                        clearTimeout(timeout);
                    else if (execAsap)
                        func.apply(obj, args);

                    timeout = setTimeout(delayed, threshold || 100);
                };
            }
            // smartresize
            jQuery.fn[sr] = function(fn) { return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };
        })(jQuery, 'smartresize');

        $(window).smartresize(setWidthForTable);

        setWidthForTable();

    });

    function validation() {
        var fieldsBlock = $(".sale_order_props div[data-property-id-row] .info_order_item_content__input_item");
        var elems = fieldsBlock.find("> input, > textarea");
        console.log(elems);

        elems.each(function(i, elem) {
            if($(elem).hasClass("required") && !elem.value) {
                $(elem).addClass("has-error");
            }
            else {
                $(elem).removeClass("has-error");
            }
        });
    }

    function onChangeValidation(el) {
        if(el.value != "")
            $(el).removeClass("has-error");
    }
</script>
