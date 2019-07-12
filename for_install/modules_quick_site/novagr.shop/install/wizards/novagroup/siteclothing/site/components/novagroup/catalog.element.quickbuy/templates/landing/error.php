<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//deb($arResult['ERROR'])
?>

<? if (isset($arResult['ERROR'])): ?>
    <div id="alert" class="alert alert-error" >
        <?= implode("<br>", $arResult['ERROR']) ?>
    </div>
<? endif; ?>
<?
include('form.php');
?>
