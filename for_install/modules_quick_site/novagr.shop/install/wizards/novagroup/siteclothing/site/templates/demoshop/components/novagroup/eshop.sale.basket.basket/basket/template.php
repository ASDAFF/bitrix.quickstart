<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
CModule::IncludeModule('novagr.jwshop');
?>
<form method="post" action="<?=POST_FORM_ACTION_URI ?>" name="basket_form_2">
    <?
    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/basket_items.php");
    ?>
</form>
