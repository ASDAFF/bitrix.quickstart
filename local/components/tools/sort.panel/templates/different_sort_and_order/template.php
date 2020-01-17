<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

if (!empty($arResult['SORT']['PROPERTIES'])) { ?>
    <?= Loc::getMessage('CODEBLOGPRO_SORT_PANEL_COMPONENT_TEMPALTE_SORT_BY_VALUE') ?>:<br>
    <? foreach ($arResult['SORT']['PROPERTIES'] as $property) { ?>
        <? if ($property['ACTIVE']) { ?>
            <span><?= $property['NAME'] ?></span>&nbsp
        <? } else { ?>
            <a href="<?= $property['URL']; ?>"><?= $property['NAME'] ?></a>&nbsp
        <? }
    }
} ?>
    <br><br>
<? if (!empty($arResult['SORT']['ORDERS'])) { ?>
    <?= Loc::getMessage('CODEBLOGPRO_SORT_PANEL_COMPONENT_TEMPALTE_SORT_ORDER_VALUE') ?><br><br>
    <? foreach ($arResult['SORT']['ORDERS'] as $order) { ?>
        <? if ($order['ACTIVE']) { ?>
            <span><?= $order['CODE'] ?></span>&nbsp
        <? } else { ?>
            <a href="<?= $order['URL']; ?>"><?= $order['CODE'] ?></a>&nbsp
        <? }
    }
}