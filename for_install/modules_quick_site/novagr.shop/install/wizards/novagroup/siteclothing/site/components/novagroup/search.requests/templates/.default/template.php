<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();$this->setFrameMode(true);

if(is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])):
?>
<!--noindex-->
	<div class="search-tags-cloud" >
	<?=GetMessage("OFTEN_SEEKING")?>:
	<?
		foreach ($arResult["SEARCH"] as $res)
		{
            $HTML[] = '<a href="'.$arParams['ROOT_SEARCH'].$res["URL"].'" rel="nofollow">'.$res["NAME"].'</a>';
		}
    print implode(', ',$HTML);
	?></div>
<!--/noindex-->
<?
endif;
?><??>