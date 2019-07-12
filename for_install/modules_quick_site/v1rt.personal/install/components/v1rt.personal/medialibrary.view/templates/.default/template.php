<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(count($arResult["FOLDER_IMAGE"])):?>
    <script type="text/javascript">var idVar = '<?=md5(print_r($arParams["FOLDERS"], true))?>';</script>
        <div class="photo-line">
            <?foreach($arResult["FOLDER_IMAGE"] as $img):?>
                <div>
                    <a href="<?=$img["URL"]["FILE"]?>" rel="image_group_<?=md5(print_r($arParams["FOLDERS"], true))?>" id="<?=$img["RAND_ID"]?>">
                        <img src="<?=$img["URL"]["IMAGE"]?>"<?=$img["SIZE"]?> id="img_<?=$img["RAND_ID"]?>" title="<?=$arResult["DESCRIPTION"][$img["ID"]];?>" alt="<?=$arResult["DESCRIPTION"][$img["ID"]];?>"/>
                    </a>
                </div>
                <div class="photo-separator"></div>         
            <?endforeach;?>
        </div>
        <div style="clear: both;"></div>
    <?if($arParams["PAGE_NAV_MODE"] == "Y"):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
<?endif;?>