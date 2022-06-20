<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="b-sidebar-menu m-sidebar-block">
<li class="b-sidebar-menu__item has-child">
 <a class="b-sidebar-menu__link" href="<?=$arResult['SECTION']['SECTION_PAGE_URL'];?>"><span><?=$arResult['SECTION']['NAME'];?></span></a>
 <?if($arResult['SECTIONS']){?> 
 <ul class="b-sidebar-submenu">
   <?foreach($arResult['SECTIONS'] as $section){?>
      <li class="b-sidebar-submenu__item <?if($section['ID'] == $arParams['SECTION_ID']){?> selected<?}?>"><a class="b-sidebar-submenu__link" href="<?=$section['SECTION_PAGE_URL'];?>"><?=$section['NAME'];?></a></li>
   <?}?>
  </ul>
 <?}?>
 </li>   
</ul>