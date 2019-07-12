<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>
<div class="row order-list">
    <?php if(!empty($arResult['ERRORS']['FATAL'])): ?>
        <div class="col col-md-12">
            <?php foreach ($arResult['ERRORS']['FATAL'] as $error): ?>
                <?=ShowError($error);?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if(!empty($arResult['ERRORS']['NONFATAL'])): ?>
            <div class="col col-md-12">
            <?php foreach ($arResult['ERRORS']['NONFATAL'] as $error): ?>
                <?=ShowError($error);?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    
    <div class="col col-xs-12 order-list__filter">
        <?php $nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]); ?>
        <a href="<?=$arResult["CURRENT_PAGE"]?>?show_all=Y" class = "btn btn-default btn-button <?=$nothing || isset($_REQUEST["filter_history"])?'':'active'?>">
            <?=Loc::getMessage('SPOL_ORDERS_ALL');?>
        </a>
        <a href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=N" class = "btn btn-default btn-button <?=$_REQUEST["filter_history"] == 'Y' || $_REQUEST["show_all"] == 'Y'?'':'active'?>">
            <?=Loc::getMessage('SPOL_CUR_ORDERS');?>
        </a>
        <a href="<?=$arResult["CURRENT_PAGE"]?>?filter_history=Y" class = "btn btn-default btn-button <?=$nothing || $_REQUEST["filter_history"] == 'N' || $_REQUEST["show_all"] == 'Y'?'':'active'?>">
            <?=Loc::getMessage('SPOL_ORDERS_HISTORY');?>
        </a>
    </div>
    
    <div class="col col-xs-12">
        <table class="table table-striped order-list__table">
            <?php foreach($arResult['ORDERS'] as $index => $order): ?>
            <?php 
            $orderStatusId = $order['ORDER']['CANCELED'] == 'Y' ?
						'PSEUDO_CANCELLED' : $order["ORDER"]["STATUS_ID"];
            ?>
            <tr>
                <td class="hidden-xs">
                    <?=$index+1;?>
                </td>
                <td style="width: 100%">
                    <div class="order-list__itemname">       
                        <a href="<?=$order["ORDER"]["URL_TO_DETAIL"]?>">
                            <?=Loc::getMessage('SPOL_ORDER')?>
                            <?=Loc::getMessage('SPOL_NUM_SIGN')?><?=$order["ORDER"]["ACCOUNT_NUMBER"]?>
                            <?php if(strlen($order["ORDER"]["DATE_INSERT_FORMATED"])): ?>
                                <?=Loc::getMessage('SPOL_FROM') ?> <?= $order["ORDER"]["DATE_INSERT_FORMATED"]; ?>
                            <?php endif; ?>
                        </a>
                        <?php
                        $CSSClass = 'label label-success';
                        if($arResult["INFO"]["STATUS"][$orderStatusId]['COLOR']=='yellow'){
                            $CSSClass = 'label label-warning';
                        } elseif($arResult["INFO"]["STATUS"][$orderStatusId]['COLOR']=='red'){
                            $CSSClass = 'label label-danger';
                        } elseif($arResult["INFO"]["STATUS"][$orderStatusId]['COLOR']=='gray'){
                            $CSSClass = 'label label-info';
                        }
                        ?>
                        &nbsp;<span class="<?=$CSSClass;?>">
                            <?=$arResult["INFO"]["STATUS"][$orderStatusId]['NAME']?>
                        </span>
                    </div>
                    
                    <div class="order-list__itempayed">
                        <?=Loc::getMessage('SPOL_PAYED')?>:
                        <?=Loc::getMessage('SPOL_'.($order["ORDER"]["PAYED"] == "Y" ? 'YES' : 'NO'))?>
                    </div>
                    
                    <?php if(intval($order["ORDER"]["PAY_SYSTEM_ID"])): ?>
                        <div class="order-list__itempaysystem">
                            <?=Loc::getMessage('SPOL_PAYSYSTEM')?>:
                            <?=$arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(intval($order["ORDER"]["DELIVERY_ID"]) || strpos($order["ORDER"]["DELIVERY_ID"], ':') !== "false"): ?>
                    <div class="order-list__itemdelivery">
                        <?=Loc::getMessage('SPOL_DELIVERY')?>:
                        <?php if(intval($order["ORDER"]["DELIVERY_ID"])):?>
                            <?=$arResult["INFO"]["DELIVERY"][$order["ORDER"]["DELIVERY_ID"]]["NAME"] ?>
                        <?php elseif(strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false): $arId = explode(":", $order["ORDER"]["DELIVERY_ID"]); ?>
                            <?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"] ?>
                            (<?= $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"] ?>)
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <ul class="order-list__itembasket list-unstyled">
                        <?php foreach($order["BASKET_ITEMS"] as $item): ?>
                        <li><?=$item['NAME']?> - <?=$item['QUANTITY']?>
                            <?=(isset($item["MEASURE_NAME"]) ? $item["MEASURE_NAME"] : Loc::getMessage('SPOL_SHT'))?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="order-list__itemprice">
                        <?=Loc::getMessage('SPOL_PAY_SUM')?>: <?=$order["ORDER"]["FORMATED_PRICE"]?>
                    </div>
                    
                    <div class="order-list__itemcontrols visible-xs">
                        <?php if($order["ORDER"]['CAN_CANCEL'] == 'Y'): ?>
                        <a href="<?=$order["ORDER"]["URL_TO_CANCEL"]?>"><?=Loc::getMessage('SPOL_CANCEL_ORDER');?></a><br>
                        <?php endif; ?>
                        <a href="<?=$order["ORDER"]["URL_TO_COPY"]?>"><?=Loc::getMessage('SPOL_REPEAT_ORDER');?></a>
                    </div>
                </td>
                
                <td class="hidden-xs order-list__control">
                    <?php if($order["ORDER"]['CAN_CANCEL'] == 'Y'): ?>
                        <a href="<?=$order["ORDER"]["URL_TO_CANCEL"]?>"><?=Loc::getMessage('SPOL_CANCEL_ORDER');?></a>
                    <?php endif; ?>
                </td>
                <td class="hidden-xs order-list__control">
                    <a href="<?=$order["ORDER"]["URL_TO_COPY"]?>"><?=Loc::getMessage('SPOL_REPEAT_ORDER');?></a>
                </td>
                
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <?php endif; ?>
    
    <?php if(strlen($arResult['NAV_STRING'])): ?>
    <div class="col col-md-12">
        <?=$arResult['NAV_STRING'];?>
    </div>
    <?php endif; ?>
    
    <?php if(empty($arResult['ORDERS'])): ?>
    <div class="col col-md-12">
        <div class="alert alert-info" role="alert"><?=Loc::getMessage('SPOL_NO_ORDERS')?></div>
    </div>
    <?php endif; ?>
    
</div>
