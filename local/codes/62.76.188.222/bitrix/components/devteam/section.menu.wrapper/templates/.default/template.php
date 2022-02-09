<div class="b-section-menu-wrapper">
        
        
        <div class="b-section-menu"> 
            <?if($arResult['PARENT_SECTION']){?>
            <a class="b-section-menu__link" href="<?=$arResult['PARENT_SECTION']['SECTION_PAGE_URL']?>"><span><?=$arResult['PARENT_SECTION']['NAME']?></span></a>
            <?} else {?>  
            <a class="b-section-menu__link" href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>"><span><?=$arResult['SECTION']['NAME']?></span></a>
            <?}?>
            <div class="b-section-menu__line m-white"></div>
            <ul class="b-section-popup">
                <? foreach($arResult['LEVEL_1'] as $section){ ?>
                <li class="b-section-popup__item"><a class="b-section-popup__link" href="<?=$section['SECTION_PAGE_URL']?>"><?=$section['NAME']?></a></li>
                <?}?>   
             </ul>
        </div>
    
        <?if($arResult['LEVEL_2']){?>
        <div class="b-section-menu m-level3">
            <?if($arResult['PARENT_SECTION']){?>
            <a class="b-section-menu__link" href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>"><span><?=$arResult['SECTION']['NAME']?></span></a>
            <?} else {?>  
            <a class="b-section-menu__link" href="<?=$arResult['PARENT_SECTION']['SECTION_PAGE_URL']?>"><span><?=$arResult['PARENT_SECTION']['NAME']?></span></a>
            <?}?>
            <div class="b-section-menu__line"></div>
            <ul class="b-section-popup">
                <?foreach($arResult['LEVEL_2'] as $section){ ?>
                <li class="b-section-popup__item"><a class="b-section-popup__link" href="<?=$section['SECTION_PAGE_URL']?>"><?=$section['NAME'];?></a></li>
                <?}?>    
            </ul>
        </div><?}?>
    </div>
 

<?//prent($arResult);?>