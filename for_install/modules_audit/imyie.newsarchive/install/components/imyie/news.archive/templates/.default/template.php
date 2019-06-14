<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div class="sidebar-title"><?=$arParams["NA_PARAM_NAME"]?></div>
<div class="sidebar-block">
<ul>
<?foreach($arResult["MONTH"] as $month):?>
	<?if($month["ISSET_ELEMENTS"]=="Y"):?>
		<li><a href="<?=$arParams["FILTER_LINK"]?>?F_VARIANT=<?=$arParams["ORDERT_VARIANT"]?>&F_YEAR=<?=$month["DATE_FORMATED"]["YEAR"]?>&F_MONTH=<?=$month["DATE_FORMATED"]["MONTH"]?>"><?=$month["DATE_FORMATED"]["MONTH_NAME"]?> <?=$month["DATE_FORMATED"]["YEAR"]?></a><?if($arParams["KNOW_CNT_ELEMENTS"]=="Y"):?> (<?=$month["CNT"]?>)<?endif;?></li>
	<?endif;?>
<?endforeach;?>
</ul>
</div>