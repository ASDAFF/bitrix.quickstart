<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y") {
    if(
        $arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" ||
        $arResult["NEED_REDIRECT"] == "Y"
    ) {
        if(strlen($arResult["REDIRECT_URL"]) > 0) {
            $APPLICATION->RestartBuffer();
            ?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?php
			die();
        }
    }
}

if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N") {

    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
    return;
}

if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y") {
    if(strlen($arResult["REDIRECT_URL"]) == 0) {
        include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php";
    }
    return;
}


if (!function_exists("cmpBySort"))
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
<div id="order_form_div">
<script type="text/javascript">

<?php if(CSaleLocation::isLocationProEnabled()):?>

    <?php
    // spike: for children of cities we place this prompt
    $city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
    ?>

    BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
        'source' => $this->__component->getPath().'/get.php',
        'cityTypeId' => intval($city['ID']),
        'messages' => array(
            'otherLocation' => '--- '.GetMessage('SOA_OTHER_LOCATION'),
            'moreInfoLocation' => '--- '.GetMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
            'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
                '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                '#ANCHOR_END#' => '</a>'
            )).'</div>'
        )
    ))?>);

<?php endif ?>

var BXFormPosting = false;
function submitForm(val)
{
    if (BXFormPosting === true)
        return true;

    BXFormPosting = true;
    if(val != 'Y')
        BX('confirmorder').value = 'N';

    var orderForm = BX('ORDER_FORM');
     rsFlyaway.darken($(orderForm));

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
        // if json came, it obviously a successfull order submit

        var json = JSON.parse(res);
        rsFlyaway.darken($(orderForm));

        if (json.error)
        {
            BXFormPosting = false;
            return;
        }
        else if (json.redirect)
        {
            window.top.location.href = json.redirect;
        }
    }
    catch (e)
    {
        // json parse failed, so it is a simple chunk of html

        BXFormPosting = false;
        BX('order_form_content').innerHTML = res;

        <?if(CSaleLocation::isLocationProEnabled()):?>
            BX.saleOrderAjax.initDeferredControl();
        <?endif?>
    }

    rsFlyaway.darken($(orderForm));
    BX.onCustomEvent(orderForm, 'onAjaxSuccess');
}

function SetContact(profileId)
{
    BX("profile_change").value = "Y";
    submitForm();
}
</script>

<?php if(empty($_POST["is_ajax_post"]) || $_POST["is_ajax_post"] != "Y"): ?>
<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
    <?=bitrix_sessid_post()?>
    <div id="order_form_content">
<?php else: $APPLICATION->RestartBuffer(); endif; ?>


        <div class="row" style="position: relative">
            <div class="col-xs-12 col-lg-9">
                <?php
                if(!empty($arResult["ERROR"])  && $arResult["USER_VALS"]["FINAL_STEP"] == "Y") {
                    ?><div class="alert alert-danger"><?
                    foreach($arResult["ERROR"] as $v) {
                        echo ShowError($v);
                    }
                    ?></div><?
                }
                ?>
            </div>
            <div class="col-xs-12 col-lg-9">
                <div class="panel-group personal-makeorder" data-toggle = "accordion" id="order_form" role="tablist" aria-multiselectable="true">

                    <div class="panel panel-order">

                        <div class="panel-heading" role="tab" id="headingCustomerInfo">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#customerInfo" aria-expanded="true" aria-controls="customerInfo">
                                    <?=Loc::getMessage('SOA_TEMPL_BUYER_INFO'); ?>
                                </a>
                            </h4>
                        </div>

                        <div id="customerInfo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingCustomerInfo">
                            <div class="panel-body">
                                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php"; ?>
                                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php"; ?>
                            </div>
                        </div>

                    </div>

                    <?php ob_start(); ?>
                    <div class="panel panel-order">
                        <div class="panel-heading" role="tab" id="headingPaySystem">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#paySystem" aria-expanded="true" aria-controls="paySystem">
                                    <?=Loc::getMessage('SOA_TEMPL_PAY_SYSTEM'); ?>
                                </a>
                            </h4>
                        </div>
                        <div id="paySystem" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingPaySystem">
                            <div class="panel-body">
                                <div class = "row">
                                    <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php"; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $paySystemBuffer = ob_get_clean(); ?>


                    <?php ob_start(); ?>
                    <div class="panel panel-order">
                        <div class="panel-heading" role="tab" id="headingDelivery">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#delivery" aria-expanded="true" aria-controls="delivery">
                                    <?=Loc::getMessage('SOA_TEMPL_DELIVERY'); ?>
                                </a>
                            </h4>
                        </div>
                        <div id="delivery" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingDelivery">
                            <div class="panel-body">
                                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php"; ?>
                            </div>
                        </div>
                    </div>
                    <?php $deliveryBuffer = ob_get_clean(); ?>

                    <?php if($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d"): ?>
                        <?=$paySystemBuffer?>
                        <?=$deliveryBuffer?>
                    <?php else: ?>
                        <?=$deliveryBuffer?>
                        <?=$paySystemBuffer?>
                    <?php endif; ?>

                    <?php if(is_array($arResult["ORDER_PROP"]["RELATED"]) && count($arResult["ORDER_PROP"]["RELATED"])): ?>
                    <div class="panel panel-order">

                        <div class="panel-heading" role="tab" id="headingRelatedProps">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#relatedProps" aria-expanded="true" aria-controls="relatedProps">
                                    <?=Loc::getMessage('SOA_TEMPL_RELATED_PROPS'); ?>
                                </a>
                            </h4>
                        </div>
                        <div id="relatedProps" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingRelatedProps">
                            <div class="panel-body">
                                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php"; ?>
                            </div>
                        </div>

                    </div>
                    <?php endif; ?>

                    <div class="panel panel-order">

                        <div class="panel-heading" role="tab" id="headingComment">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#comment" aria-expanded="true" aria-controls="comment">
                                    <?=Loc::getMessage('SOA_TEMPL_SUM_COMMENTS'); ?>
                                </a>
                            </h4>
                        </div>
                        <div id="comment" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingComment">
                            <div class="panel-body">
                                <div class = "form-group">
                                    <div class = "form-group">
                                        <label for = "ORDER_DESCRIPTION"><?=Loc::getMessage('SOA_TEMPL_SUM_COMMENTS_TEXT');?></label>
                                        <textarea class = "form-control" name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style="max-width: 100%"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?> </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel panel-order">
                        <div class="panel-heading" role="tab" id="headingBasket">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" class="collapsed" href="#basket" aria-expanded="true" aria-controls="basket">
                                    <?=Loc::getMessage('SOA_TEMPL_SUM_TITLE');?>
                                </a>
                            </h4>
                        </div>
                        <div id="basket" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingBasket">
                            <div class="panel-body">

                                <table class="table basket-table basket-table--order">
                                    <thead class="hidden-xs hidden-sm">
                                        <tr>
                                            <th><?=Loc::getMessage('SOA_TEMPL_SUM_NAME');?></th>
                                            <th><?=Loc::getMessage('SOA_TEMPL_SUM_PRICE');?></th>
                                            <th><?=Loc::getMessage('SOA_TEMPL_SUM_QUANTITY'); ?></th>
                                            <th><?=Loc::getMessage('SALE_SUM'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($arResult["GRID"]["ROWS"] as $k => $arItem): $arData = $arItem['data'] ?>
                                        <tr id="<?=$arItem['ID']?>" class="js-element">
                                            <td class="basket-table__item">
                                                <div class="row">

                                                    <div class="basket-table__name col-xs-11 col-md-12">
                                                        <?php if(strlen($arData["DETAIL_PAGE_URL"]) > 0): ?>
                                                            <a href="<?=$arData["DETAIL_PAGE_URL"] ?>">
                                                                <?=$arData["NAME"]?>
                                                            </a>
                                                        <?php else: ?>
                                                            <?=$arData["NAME"]?>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="col col-xs-8  hidden-md hidden-lg">
                                                        <div class="basket-table__itemprice">
                                                            <i class="small"><?=$arData["NOTES"]?></i><br>
                                                            <span class="js-item-price prices__val prices__val_normal"><?=$arData["PRICE_FORMATED"]?></span>
                                                        </div>
                                                        <div class="basket-table__itemsum">
                                                            <i class="small"><?=Loc::getMessage('SALE_SUM');?></i><br>
                                                            <span class="h4"><b class="js-item-sum prices__val prices__val_cool"><?=$arData["SUM"]?></b></span>
                                                        </div>
                                                    </div>

                                                    <div class="col col-xs-4 hidden-md hidden-lg text-right">
                                                        <br><br><br><?=$arData['QUANTITY']?>
                                                        <?php if(!empty($arData["MEASURE_TEXT"])): ?>
                                                            <?=$arData["MEASURE_TEXT"]?>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </td>
                                            <td class="hidden-xs hidden-sm basket-table__price">
                                                <i class="small"><?=$arData["NOTES"]?></i>
                                                <div><span class="h4"><b class="js-item-price prices__val prices__val_cool"><?=$arData["PRICE_FORMATED"]?></b></span></div>
                                            </td>
                                            <td class="hidden-xs hidden-sm basket-table__quantity text-center text-nowrap">
                                                <?=$arData['QUANTITY']?>
                                                <?php if(!empty($arData["MEASURE_TEXT"])): ?>
                                                    <?=$arData["MEASURE_TEXT"]?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="hidden-xs hidden-sm basket-table__sum">
                                                <div><span class="h4"><b class="js-item-sum prices__val prices__val_cool"><?=$arData['SUM']?></b></span></div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="float-basket-order visible-lg">
                <div class="float-basket-order__arrow"></div>
                <div class="float-basket">
                    <h4><?=Loc::getMessage('SOA_TEMPL_SUM_TITLE');?></h4>
                    <ol class="float-basket__itemlist js-fb-itemlist">
                        <?php foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):?>
                        <li class="float-basket__item js-fb-item">
                            <div class="float-basket__itemname">
                                <a href="<?=$arItem['data']['DETAIL_PAGE_URL']?>"><?=$arItem['data']['NAME']?></a>
                            </div>
                            <div class="float-basket__itemquantity">
                                <?=Loc::getMessage('SOA_TEMPL_SUM_QUANTITY');?>:
                                <?=$arItem['data']['QUANTITY']?>
                                <?=!empty($arItem['data']['MEASURE_TEXT']) ? $arItem['data']['MEASURE_TEXT'] : ''?>
                                <pre style="display: none"><?php var_dump($arItem); ?></pre>
                            </div>
                            <?php if(!empty($arItem['data']['PROPS']) && is_array($arItem['data']['PROPS'])): ?>
                            <div class="float-basket__itemprops">
                                <?php foreach($arItem['data']['PROPS'] as $arProp): ?>
                                <div class="float-basket__itemprop">
                                    <?=$arProp['NAME']?>: <?=$arProp['VALUE']?>
                                </div>
                                <?php endforeach ?>
                            </div>
                            <?php endif; ?>
                            <div class="float-basket__itemsum">
                                <?=Loc::getMessage('SOA_TEMPL_SUM_PRICE');?>:
                                <?=$arItem['data']['SUM']?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="float-basket__editbasket-link">
                        <a href="<?=$arParams['PATH_TO_BASKET']?>"><?=Loc::getMessage('SOA_TEMPL_EDIT_BASKET');?></a>
                    </div>
                </div>
            </div>
        </div>

<?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php"; ?>

        <?php if(empty($_POST["is_ajax_post"]) || $_POST["is_ajax_post"] != "Y"): ?>
    </div>
    <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
    <input type="hidden" name="profile_change" id="profile_change" value="N">
    <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
    <input type="hidden" name="json" value="Y">
</form>
<?php else: ?>
<script>
    top.BX('confirmorder').value = 'Y';
    top.BX('profile_change').value = 'N';
</script>
<?php die(); endif; ?>
</div>
<script>submitForm();</script>

<?php if(CSaleLocation::isLocationProEnabled()): ?>

	<div style="display: none">
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.steps",
			".default",
			array(
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.search",
			".default",
			array(
			),
			false
		);?>
	</div>

<?php endif;
