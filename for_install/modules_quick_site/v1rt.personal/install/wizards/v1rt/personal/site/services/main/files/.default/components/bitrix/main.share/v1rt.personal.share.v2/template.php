<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (strlen($arResult["PAGE_URL"]) > 0):?>
    <?if (is_array($arResult["BOOKMARKS"]) && count($arResult["BOOKMARKS"]) > 0):?>	
    <div class="bookmarks">
        <?foreach($arResult["BOOKMARKS"] as $name => $arBookmark)
        {?>
            <?=$arBookmark["ICON"]?>
        <?}?>
		<?endif;?>
    </div>
    <div style="clear: both; padding-bottom: 20px;"></div>
<?endif;?>