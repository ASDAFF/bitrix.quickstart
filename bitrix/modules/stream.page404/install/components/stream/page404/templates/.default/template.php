<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arParams['CLEAR_PAGE'] == 'Y')
{
    $APPLICATION->RestartBuffer();
    $APPLICATION->ShowHead();
}
?>
    <div class="e404-block">
        <h1>
            <?=$arParams['PHRASE_1']?>
        </h1>
        <img src="<?=(!empty($arParams['IMAGE']) ? $arParams['IMAGE'] : $templateFolder.'/images/trollface.jpg')?>" class="troll">
    <span class="fat">
        <?=$arParams['PHRASE_2']?>
    </span>
    </div>
<?
if ($arParams['REDIRECT_ONOFF'] == 'Y')
{
    ?><script>
    var redir_msec = '<?=CUtil::JSEscape($arParams['REDIRECT_MSEC'])?>';
    var redir_url = '<?=CUtil::JSEscape($arParams['REDIRECT_URL'])?>';
</script><?
}
?>
<?
if ($arParams['CLEAR_PAGE'] == 'Y')
    die;
?>