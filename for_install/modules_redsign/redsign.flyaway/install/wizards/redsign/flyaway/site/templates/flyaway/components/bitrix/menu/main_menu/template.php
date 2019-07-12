<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(true);

if (!empty($arResult)):
?>
<ul class="nav navbar-nav list-unstyled mainmenu mainJS">
    <li class="dropdown mainmenu__other nav other invisible">
        <a href="javascript: void(0)" class="mainmenu__other-link">...</a>
        <ul class="list-unstyled dropdown-menu-right drop-panel mainmenu__other-list nav"></ul>
    </li>
    <?php
    $previousLevel = 0;
    foreach ($arResult as $key => $arItem):
        if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
            echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
        }
    ?>
    
        <?php if($arItem['DEPTH_LEVEL']==1): ?>
            <li class="dropdown lvl1 mainmenu__root-item" id="element<?=$key?>">
                <a href="<?=$arItem['LINK']?>" class="mainmenu__item-link element"><?=$arItem['TEXT']?></a>
        <?php else: ?>
            <?php if($previousLevel==1): ?> <ul class="dropdown-submenu nav drop-panel mainmenu__submenu"> <?php endif; ?>
                
                <li class="mainmenu__submenu-item<?php if($arItem['IS_PARENT']) echo ' dropdown-submenu'; ?> lvl<?=$arItem['DEPTH_LEVEL']?>" data-level="<?=$arItem['DEPTH_LEVEL']?>">
                    <a href="<?=$arItem['LINK']?>" class="mainmenu__item-link element"><?=$arItem['TEXT']?></a>
                 <?php if($arItem['IS_PARENT']==1): ?>
                        <ul class="dropdown-submenu nav drop-panel mainmenu__submenu">
                <?php endif; ?>
                
        <?php endif; $previousLevel = $arItem["DEPTH_LEVEL"];?>
    
    <?php endforeach;  if ($previousLevel > 1) echo str_repeat("</li></ul>", ($previousLevel-1) ); ?>
</ul>
<?php endif; /**

<!--<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (!empty($arResult)) {
  ?><ul class="main-nav mainJS nav navbar-nav list-unstyled"><?
    ?><li class="dropdown main-nav-other other invisible"><?
      ?><a href="#" class="main-nav-other__menu">...</a><?
      ?><ul class="list-unstyled dropdown-menu-right drop-panel"></ul><?
    ?></li><?
    $previousLevel = 0;
    foreach ($arResult as $key => $arItem) {
      if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
        echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
      }
      if($arItem['DEPTH_LEVEL']==1) {
        ?><li class="main-nav__item dropdown lvl1 <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>" id="element<?=$key?>"><?
          ?><a href="<?=$arItem['LINK']?>" class="main-nav__label element"><?
            ?><?=$arItem['TEXT']?><?
            if($arItem['IS_PARENT']==1) { ?><span class="hidden-md hidden-sm hidden-lg"><i></i></span><? }
          ?></a><?
      } else {
        if($previousLevel==1) { ?><ul class="mainnav-sub dropdown-submenu nav drop-panel"><? }
        if($arItem['IS_PARENT']==1) {
          ?><li class="mainnav-sub__item dropdown-submenu level<?print_r($arItem['DEPTH_LEVEL'])?> <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>" data-level="<?print_r($arItem['DEPTH_LEVEL'])?>"><?
            ?><a href="<?=$arItem['LINK']?>" class="mainnav-sub__label element"><?
              ?><?=$arItem['TEXT']?><?
              if($arItem['IS_PARENT']==1) { ?><span class="hidden-md hidden-sm hidden-lg"><i></i></span><? }
            ?></a><?
            ?><ul class="mainnav-sub nav drop-panel"><?
        } else {
          ?><li class="mainnav-sub__item level<?print_r($arItem['DEPTH_LEVEL'])?> <?if ($arItem['SELECTED']=='Y') {?>active<?}?>" data-level="<?print_r($arItem['DEPTH_LEVEL'])?>">
            <a href="<?=$arItem['LINK']?>" class="mainnav-sub__label element"><?=$arItem['TEXT']?>
            </a><?
        }
      }
      $previousLevel = $arItem["DEPTH_LEVEL"];
    }
    if ($previousLevel > 1) {
      echo str_repeat("</li></ul>", ($previousLevel-1) );
    }
  ?></ul><?
} **/