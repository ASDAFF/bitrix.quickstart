<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if ($USER->IsAuthorized()): ?>

    <?php echo $arResult['ULOGIN_CODE']; ?>

<? endif; ?>