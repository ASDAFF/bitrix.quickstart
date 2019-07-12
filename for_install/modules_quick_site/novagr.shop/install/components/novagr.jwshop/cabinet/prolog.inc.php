<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
if (count($arResult['URL_TEMPLATES']) > 0) {
    ?>
    <ul class="nav nav-tabs nav-tabs-m" id="myTab">
        <?php
        foreach ($arResult['URL_TEMPLATES'] as $KEY => $URL) {
            if (strlen($BUTTON_NAME = GetMessage($KEY)) > 0) {
                if (!$USER->IsAuthorized() and $KEY == 'userinfo') continue;
                ?>
                <li <?= ($KEY == $arResult['PAGE'] ? ' class="active"' : '') ?>>
                    <a href="<?= $arResult['FOLDER'] . $URL ?>"><?= $BUTTON_NAME ?></a>
                </li>
            <?php
            }
        }
        ?>
    </ul>
<?php
}
?>
<script type="text/javascript">
    BX.ajax.history.bPushState = true;
</script>
<div class="tab-content demo-tab" id="myTabContent">
    <div id="<?= $arResult['PAGE'] ?>" class="tab-pane fade in active">