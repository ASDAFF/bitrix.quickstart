<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<noindex>
<? if($arParams['USEH1']=='Y'):?><h1><?=GetMessage("ZAKAZ_NA_OPLATU")?></h1><? endif;?>
<form id="quickpayform" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" <? if($arParams['FONT_RC']=='Y'):?>data-roboto="y"<? endif;?> <? if($arParams['COMMISSION']=='Y'):?>data-comission="y"<? endif;?>>
	<table>
    	<? if($arResult['ORDERCORRECT']):?>
    	<tr>
        	<td><?=GetMessage("NOMER_ZAKAZA")?>:</td>
            <td><?=$arResult['NAMEZAKAZA']?></td>
        </tr>
        <tr>
        	<td><?=GetMessage("STOIMOST_ZAKAZA")?>:</td>
            <td><?=$arResult['PRICE']?> <?=GetMessage("RUB")?>.</td>
        </tr>
        <tr>
        	<td><?=GetMessage("STATUS_ZAKAZA")?>:</td>
            <td><?=$arResult['STATUS']?></td>
        </tr>
        <? endif;?>
    	<tr>
        	<td><?=GetMessage("CHTO_POKUPAEM")?>:</td>
            <td><?=$arResult['NAME']?></td>
        </tr>
        <? if(!$arResult['ORDER']):?>
        <tr>
        	<td><?=GetMessage("KOLICHESTVO")?>:</td>
            <td><input type="number" min="1" step="1" name="count" value="1" /></td>
        </tr>
        <tr>
        	<td><?=GetMessage("PREDSTAVTES")?>:</td>
            <td><input type="text" class="styler qp_required" name="fio" value="<?=$arResult['FIO']?>" /></td>
        </tr>
        <tr>
        	<td><?=GetMessage("VASH_TELEFON")?>:</td>
            <td><input type="text" class="styler qp_required" name="phone" value="<?=$arResult['PHONE']?>" placeholder="+7 (___) ___-____" /></td>
        </tr>
        <tr>
        	<td><?=GetMessage("VASH_EMAIL")?>:</td>
            <td><input type="text" class="styler" name="email" value="<?=$arResult['EMAIL']?>" /></td>
        </tr>
        <tr>
        	<td><?=GetMessage("KOMMENTARII")?>:</td>
            <td><textarea class="styler" name="comment"><?=$arResult['KOMM']?></textarea></td>
        </tr>
        <? endif;?>
        <tr> 
            <td nowrap="nowrap"><?=GetMessage("SPOSOB_OPLATU")?>:</td> 
            <td id="paytype"> 
            	<? foreach($arParams['PAYTYPE'] as $n=>$arItem):?>
                <label><input type="radio" name="paymentType" value="<?=$arItem?>" <? if(($n=='0' && !$arResult['PAYTYPE']) || $arResult['PAYTYPE']==$arItem):?>checked="checked"<? endif;?> <? if($arResult['ORDERCORRECT']):?>disabled="disabled"<? endif;?>>
					<?=($arItem=='AC')?GetMessage("BANKOVSKOJ_KARTOJ"):""?>
                    <?=($arItem=='PC')?GetMessage("YANDEX_DENGAMI"):""?>
                    <?=($arItem=='MC')?GetMessage("S_BALANSA_MOBILNOGO"):""?>
                </label>
                <? endforeach;?> 
            </td> 
        </tr>
        <? if(!$arResult['ORDER']):?>
        <tr> 
            <td></td> 
            <td><div class="quickpaybtncontainer"><div class="quickpaybtn" id="sendquickpayform" data-bgcolor="<?=$arParams['COLOR_PAYBTN']?>" data-textcolor="<?=$arParams['COLOR_TEXTPAYBTN']?>" data-sendpath="<?=$arResult['SENDPATH']?>" data-otpravka="<?=GetMessage("OTPRAVKA")?>"><?=GetMessage("OPLATIT")?> <span><?=$arResult['PRICE']?></span> <?=GetMessage("RUB")?>.</div></div></td> 
        </tr>
        <? endif;?>
    </table>
	
    <? if(!$arResult['ORDER']):?>
    <input type="hidden" name="productname" value="<?=$arResult['NAME']?>" /> 
    <input type="hidden" name="productprice" value="<?=$arResult['PRICE']?>" />      
    <input type="hidden" name="receiver" value="<?=$arParams['YAMONEY']?>" /> 
    <input type="hidden" name="formcomment" value="<?=$_SERVER['HTTP_HOST']?>" />  
    <input type="hidden" name="short-dest" value="<?=$arResult['NAME']?>" /> 
    <input type="hidden" name="label" value="" />  
    <input type="hidden" name="quickpay-form" value="shop" /> 
    <input type="hidden" name="targets" value="" /> 
    <input type="hidden" name="defaultsum" value="<?=$arResult['PRICE']?>" data-type="number">
    <input type="hidden" name="sum" value="<?=$arResult['PRICE']?>" data-type="number"> 
    <input type="hidden" name="need-fio" value="false"> 
    <input type="hidden" name="need-email" value="false"> 
    <input type="hidden" name="need-phone" value="false">  
    <input type="hidden" name="need-address" value="false">
    <input type="hidden" name="thispath" value="<?=$arResult['THISPATH']?>"> 
    <input type="hidden" name="successURL" value="">
    <? endif;?>
</form>
</noindex>