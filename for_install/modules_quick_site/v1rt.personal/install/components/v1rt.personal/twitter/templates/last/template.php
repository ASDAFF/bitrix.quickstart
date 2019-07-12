<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(count($arResult["TWITS"])):?>
    <tr>
        <td class="twitter">
        <?foreach($arResult["TWITS"] as $twit):?>
            <a href="http://twitter.com/<?=$arParams["ACCOUNT"]?>" target="_blank">@<?=$arParams["ACCOUNT"]?></a>:&nbsp;<?=$twit["TEXT"]?>
            <?$len = strlen($arParams["ACCOUNT"]) + 3 + strlen($twit["TEXT"])?>
            <?if($len < 160):?>
                <?for($i = 160 - $len; $i <= 160; $i++):?>&nbsp;<?endfor;?>
            <?endif;?>
        <?endforeach;?>
        </td>
    </tr>
<?endif;?>