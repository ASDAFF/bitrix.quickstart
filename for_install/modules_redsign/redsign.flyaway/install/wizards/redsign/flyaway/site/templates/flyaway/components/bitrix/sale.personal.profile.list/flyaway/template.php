<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="row profile-list">
    <div class="col col-xs-12">
        <?php if(strlen($arResult["ERROR_MESSAGE"])>0): ?>
            <?=ShowError($arResult["ERROR_MESSAGE"]); ?>
        <?php endif; ?>
        
        <?php if(strlen($arResult["NAV_STRING"]) > 0): ?>
            <p><?=$arResult["NAV_STRING"]?></p>
        <?php endif; ?>
    </div>
    <div class="col col-xs-12">
        <?php if(is_array($arResult["PROFILES"]) && count($arResult["PROFILES"])>0): ?>
        <table class="table table-striped profile-list__table">
            <tbody>
                <?php foreach($arResult["PROFILES"] as $profile): ?>
                <tr>
                    <td width="100%">
                        <a href="<?=$profile["URL_TO_DETAIL"]?>"><?=$profile["NAME"]?></a><br>
                        <?=Loc::getMessage('P_DATE_UPDATE'); ?>: <?=$profile["DATE_UPDATE"]?><br>
                        <?=Loc::getMessage('P_PERSON_TYPE'); ?>: <?=$profile["PERSON_TYPE"]["NAME"]?>
                        <div class="hidden-md hidden-lg">
                            <br>
                            <a href="javascript:if(confirm('<?= GetMessage("STPPL_DELETE_CONFIRM") ?>')) window.location='<?=$profile["URL_TO_DETELE"]?>'">
                                <?=Loc::getMessage("SALE_DELETE")?>
                            </a>
                        </div>
                    </td>
                    <td class="hidden-xs hidden-sm">
                        <a href="javascript:if(confirm('<?= GetMessage("STPPL_DELETE_CONFIRM") ?>')) window.location='<?=$profile["URL_TO_DETELE"]?>'">
                            <?=Loc::getMessage("SALE_DELETE")?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if(strlen($arResult["NAV_STRING"]) > 0): ?>
            <?=$arResult["NAV_STRING"]?>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="alert alert-info" role="alert"><?=Loc::getMessage('RS.MSHOP.EMPTY_PROFILES')?></div>
        <?php endif; ?>
    </div>
</div>