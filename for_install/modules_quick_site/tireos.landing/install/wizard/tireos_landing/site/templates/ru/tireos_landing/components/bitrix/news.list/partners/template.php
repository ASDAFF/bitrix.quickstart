<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="partners-wrap">
    <div>
        <ul>
        
        <?$idIT = 16;
        foreach($arResult["ITEMS"] as $arItem):?>
            <li id="animIt<?=$idIT?>" class="col-md-2">
            
            <?$img_id = $arItem["PROPERTIES"]["PICTURE"]["VALUE"];
            $img_src = CFile::GetPath($img_id);
            $img = getResizedImgById($img_id, 170, 60);
            ?>
            
                <a href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>"><img src="<?=$img["src"]?>" alt="<?=$arItem["NAME"]?>" /></a>
                
            </li>
        <?$idIT++;
        endforeach?>
        
        </ul>
    </div>
    
    <a href="#" class="partner-next"></a>
    <a href="#" class="partner-prev"></a>
</div>

