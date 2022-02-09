<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    $INPUT_ID = trim($arParams["~INPUT_ID"]);
    if(strlen($INPUT_ID) <= 0)
        $INPUT_ID = "title-search-input";
    $INPUT_ID = CUtil::JSEscape($INPUT_ID);

    $CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
    if(strlen($CONTAINER_ID) <= 0)
        $CONTAINER_ID = "title-search";
    $CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

    if($arParams["SHOW_INPUT"] !== "N"):?>
    <div id="<?echo $CONTAINER_ID?>">
    <form action="<?echo $arResult["FORM_ACTION"]?>" style="overflow: hidden;">
        <div class="b-search-form">
            <input id="<?echo $INPUT_ID?>" class="b-search-form__text" type="text" name="q" value=""/>
            <input name="s" type="submit" value="" class="b-search-form__submit" />
        </div>
        <button class="b-search-form__lucky">Мне<br>повезет</button>
    </form>
    </div>
    <?endif?>
<script type="text/javascript">
    var jsControl = new JCTitleSearch({
            //'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
            'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
            'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
            'INPUT_ID': '<?echo $INPUT_ID?>',
            'MIN_QUERY_LEN': 2
    });
</script>
