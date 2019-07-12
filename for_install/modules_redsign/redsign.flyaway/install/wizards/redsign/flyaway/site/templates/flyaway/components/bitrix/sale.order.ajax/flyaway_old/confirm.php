<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<?php if(!empty($arResult["ORDER"])): ?>
<div class = "row">
    <div class = "col col-md-9">
        <p><b><?=Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE')?></b></p>
        <p><?=Loc::getMessage('SOA_TEMPL_ORDER_SUC', array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?></p>
        <p><?=Loc::getMessage('SOA_TEMPL_ORDER_SUC1', array("#LINK#" => $arParams["PATH_TO_PERSONAL"]))?></p>
        <?php if(!empty($arResult['PAY_SYSTEM'])): ?>
            <p>
                <?=Loc::getMessage('SOA_TEMPL_PAY')?>
                <?=$arResult['PAY_SYSTEM']['NAME']?>
            </p>
            <?php if(!empty($arResult["PAY_SYSTEM"]["ACTION_FILE"])): ?>
                <?php if($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y"): ?>
                <script>
                    window.location('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
                </script>
                <p>
                    <?=Loc::getMessage("SOA_TEMPL_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
                </p>
                <?php elseif(!empty($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])): ?>
                    <?php 
                    try {
                        include $arResult["PAY_SYSTEM"]["PATH_TO_ACTION"];
                    } catch(\Bitrix\Main\SystemException $e) {
                        if($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
                            $message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
                        else
                            $message = $e->getMessage();
                        echo '<span style="color:red;">'.$message.'</span>';
                    }
                    ?>
                <?php endif; ?>
            <?php endif; ?>
                <?php if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE'])): ?>
                    <?=Loc::getMessage("SOA_TEMPL_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
                <?php endif; ?>
        <?php else: ?>
            <p><b><?=Loc::getMessage("SOA_TEMPL_ERROR_ORDER")?></b></p>
            <p><?=Loc::getMessage("SOA_TEMPL_ERROR_ORDER_LOST", array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?></p>
            <p><?=Loc::getMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


