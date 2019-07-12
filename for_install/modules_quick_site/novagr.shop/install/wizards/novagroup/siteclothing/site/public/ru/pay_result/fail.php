<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

__IncludeLang(__DIR__."/lang/ru/payment_result.php");

if (!empty($_REQUEST["InvId"]) && !empty($_REQUEST["OutSum"]) ) {
    $inv_id = htmlspecialcharsbx($_REQUEST["InvId"]);
    $summa = htmlspecialcharsbx($_REQUEST["OutSum"]);
    ?>
    <div class="notetext order-checkout">
	<h1><?=GetMessage("ERROR_LABEL")?></h1>
	<p><span class="ico-parent-not"></span> <span class="namb"><?=GetMessage("YOUR_ORDER")?> <b>№<?=$inv_id?></b> <?=GetMessage("NOT_PAYED")?>.</span></p><br />
	<div class="clear"></div>
	
	<p><a href="/catalog/" class="btn bt3"><?=GetMessage("CONTINUE")?></a> <?=GetMessage("OR")?> <a href="/cabinet/order/make/?ORDER_ID=<?=$inv_id?>"><?=GetMessage("CONTINUE_TO_ORDER")?> <b>№31</b></a></p>
		</div>
    <?
    
} else {
     @define("ERROR_404", "Y");
    ?>
    <div class="map1">
    <?
    $APPLICATION->IncludeFile(SITE_DIR.'include/content-not-found.php');
    ?></div><?  
}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>