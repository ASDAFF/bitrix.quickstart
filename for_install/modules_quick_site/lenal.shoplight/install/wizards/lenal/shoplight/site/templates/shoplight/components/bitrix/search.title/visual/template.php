<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
    $INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
    $CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams["SHOW_INPUT"] !== "N"):
    ?>
    <div id="<? echo $CONTAINER_ID ?>" class="bx_search_container">
        <form class="b-search-form" action="<? echo $arResult["FORM_ACTION"] ?>">
            <button class="b-search-form__submit" type="submit" name="s"></button>
            <input class="b-search-form__search" name="text" placeholder="<?=GetMessage("SEARCH_INPUT")?>" id="<? echo $INPUT_ID ?>" type="text" autocomplete="off" value="<?= htmlspecialcharsbx($_REQUEST["q"]) ?>">
        </form>
    </div>
<? endif ?>
<script type="text/javascript">
    var jsControl_<? echo md5($CONTAINER_ID) ?> = new JCTitleSearch({
        //'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
        'AJAX_PAGE': '<? echo CUtil::JSEscape(POST_FORM_ACTION_URI) ?>',
        'CONTAINER_ID': '<? echo $CONTAINER_ID ?>',
        'INPUT_ID': '<? echo $INPUT_ID ?>',
        'MIN_QUERY_LEN': 2
    });
</script>
