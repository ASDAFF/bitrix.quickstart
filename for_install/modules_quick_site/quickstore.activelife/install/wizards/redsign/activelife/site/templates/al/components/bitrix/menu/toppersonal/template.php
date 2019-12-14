<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$menuId = $this->getEditAreaId('dd');
?>

<div class="dropdown">
    <a class="auth_top__link dropdown-toggle" id="<?=$menuId;?>" href="<?=SITE_DIR?>personal/" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <svg class="icon icon-user icon-svg"><use xlink:href="#svg-user"></use></svg><span class="auth_top__text" id="<?=$menuId?>__title"><?php
        $frame = $this->createFrame($menuId.'__title', false)->begin();
        $frame->setBrowserStorage(true);
        
            if ($USER->IsAuthorized()) {
                
                $sUserName = htmlspecialcharsEx($USER->GetFormattedName(false, false));
                if ($sUserName != '') {
                    echo $sUserName;
                } else {
                    echo htmlspecialcharsEx($USER->GetLogin());
                }
            } else {
                echo Loc::getMessage('RS_SLINE.BM_TOPPERSONAL.PERSONAL');
            }
            
        $frame->beginStub();
            echo Loc::getMessage('RS_SLINE.BM_TOPPERSONAL.PERSONAL');
        $frame->end();
        ?>
        </span>
    </a>
    <?php if(!empty($arResult)): ?>
    <ul class="dropdown-menu" aria-labelledby="<?=$menuId;?>">
        <?php foreach($arResult as $arMenu): ?>
            <li><a href="<?=$arMenu['LINK']?>"><?=$arMenu['TEXT']?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>