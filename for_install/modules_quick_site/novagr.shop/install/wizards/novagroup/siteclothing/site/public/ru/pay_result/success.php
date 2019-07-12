<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

__IncludeLang(__DIR__."/lang/ru/payment_result.php");


if (!empty($_REQUEST["InvId"]) && !empty($_REQUEST["OutSum"]) && !empty($_REQUEST["SignatureValue"])) {
    $inv_id = htmlspecialcharsbx($_REQUEST["InvId"]);
    $summa = htmlspecialcharsbx($_REQUEST["OutSum"]);
    ?>
    <div class="notetext">
	    <h1><?=GetMessage("PAYMENT_DONE")?></h1>
	    <p><span class="ico-parent"></span> <span class="namb"><?=GetMessage("YOUR_ORDER")?> <b>№<?=$inv_id?></b> <?=GetMessage("PAYED_SUCCESSFULLY")?>.</span></p><br />
	    <div class="clear"></div>
	    <p><?=GetMessage("TOTAL_AMOUNT_LABEL")?>: <?=$summa?> <?=GetMessage("RUB")?>.</p>
	    <p><a href="/catalog/" class="btn bt3"><?=GetMessage("CONTINUE")?></a></p>
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