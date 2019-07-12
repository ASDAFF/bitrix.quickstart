<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?if(count($arResult["LIB"]) > 0):?>
<ul>
    <?foreach($arResult["LIB"] as $lib):?>
        <li><a href="<?=$lib["DETAIL_PAGE_URL"]?>"><?=$lib["NAME"];?></a></li>
    <?endforeach;?>
</ul>
<?endif;?>