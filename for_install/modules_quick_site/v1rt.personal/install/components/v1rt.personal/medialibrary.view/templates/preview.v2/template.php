<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<script type="text/javascript">var idVarPv = '<?=md5(print_r($arParams["FOLDERS"], true))?>';</script>
<ul class="block-5">
    <?if(count($arResult["FOLDER_IMAGE"])):?>
        <?foreach($arResult["FOLDER_IMAGE"] as $img):?>
            <li><a href="<?=$img["URL"]["FILE"]?>" rel="pv_image_group_<?=md5(print_r($arParams["FOLDERS"], true))?>" id="pv_<?=$img["RAND_ID"]?>" class="preview-gal-img"><img src="<?=$img["URL"]["IMAGE"]?>"<?=$img["SIZE"]?> id="img_pv_<?=$img["RAND_ID"]?>" title="<?=$arResult["DESCRIPTION"][$img["ID"]];?>" alt="<?=$arResult["DESCRIPTION"][$img["ID"]];?>"/></a></li>       
        <?endforeach;?>
    <?endif;?>
</ul>
<div style="clear: both;"></div>