<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="banner bline isections">
<? foreach($arResult["SECTIONS"] as $i => $arSection): ?>
	<?if(isset($arSection["PICTURE"]["SRC"])):?>
    <div<?=($i < (count($arResult["SECTIONS"])-1)?" class='nm'":'')?>>
        <a href="<?=$arSection["SECTION_PAGE_URL"];?>">
			<div><?=$arSection["NAME"];?></div>
            <img src="<?=$arSection["PICTURE"]["SRC"];?>" alt="<?=$arSection["NAME"];?>" title="<?=$arSection["NAME"];?>" />
		</a>
    </div>
	<?endif;?>
<? endforeach; ?>
</div>
<div style="clear:both;"></div>