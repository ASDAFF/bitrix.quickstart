<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>
<div class="row">
    <div class="col col-md-12">
        <table class="table table-striped table-condensed">
            <tbody>
                <tr>
                    <td><?=Loc::getMessage('RS.MSHOP.IDENTIFIR')?></td>
                    <td><?=$arResult["ID"]?></td>
                </tr>
                <tr>
                    <td><?=Loc::getMessage('LOGIN')?></td>
                    <td><?=$arResult["arUser"]["LOGIN"]?></td>
                </tr>
                <tr>
                    <td><?=Loc::getMessage('NAME')?></td>
                    <td><?=$arResult["arUser"]["NAME"]?></td>
                </tr>
                <tr>
                    <td><?=Loc::getMessage('LAST_NAME')?></td>
                    <td><?=$arResult["arUser"]["LAST_NAME"]?></td>
                </tr>
                <tr>
                    <td><?=Loc::getMessage('SECOND_NAME')?></td>
                    <td><?=$arResult["arUser"]["SECOND_NAME"]?></td>
                </tr>
                <tr>
                    <td><?=Loc::getMessage('EMAIL')?></td>
                    <td><?=$arResult["arUser"]["EMAIL"]?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>