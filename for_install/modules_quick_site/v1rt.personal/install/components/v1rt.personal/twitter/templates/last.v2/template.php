<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(count($arResult["TWITS"])):?>
    <?foreach($arResult["TWITS"] as $twit):?>
        <div class="block-7">
            <div><a href="http://twitter.com/<?=$arParams["ACCOUNT"]?>" onclick="window.open(this.href); return false;">@<?=$arParams["ACCOUNT"]?></a>:</div>
            <div>&nbsp;<?=$twit["TEXT"]?></div>
        </div>
    <?endforeach;?>
<?endif;?>