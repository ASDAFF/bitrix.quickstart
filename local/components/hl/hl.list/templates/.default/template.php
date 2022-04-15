<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (!empty($arResult['ERROR']))
{
    echo $arResult['ERROR'];
    return false;
}
?>
<div class="reports-result-list-wrap">
    <div class="content">
        <?if($arParams["DISPLAY_TOP_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?><br />
        <?endif;?>
        <div  class="column_brands">
            <? foreach ($arResult['rows'] as $row): ?>
                <div class="brand">
                    <?foreach ($row['DISPLAY'] as $key=>$value){?>
                        <?if($value['TYPE']=='file'){?>
                            <a href="<?=$row['DETAIL_URL']?>"><img src="<?= CFile::GetPath($value['VALUE'])?>"></a>
                        <?}else{?>
                            <span><?=$value['VALUE']?></span>
                        <?}?>
                    <?}?>
                </div>
            <? endforeach; ?>

        </div><div style="clear:both"></div>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <br /><?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
</div>
