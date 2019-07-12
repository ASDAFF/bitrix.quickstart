<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$frame = $this->createFrame()->begin();?>
    <div class="rss-form">
        <h3><?=GetMessage('INNET_SUBSCRIBE_NEWS_INDEX')?></h3>
        <form action="<?=$arResult["FORM_ACTION"]?>">
            <input class="inp-text" type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>"/>
            <input class="inp-submit" type="submit" name="OK" value="" />
        </form>
    </div>
<?$frame->end();?>