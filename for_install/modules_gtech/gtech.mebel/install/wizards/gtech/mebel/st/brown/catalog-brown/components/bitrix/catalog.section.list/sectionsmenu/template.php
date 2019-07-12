<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["SECTIONS"] as $arSection):?>
	<h3 style="margin:0 0 15px 10px; position:relative;">
		<a href="<?=$arSection['SECTION_PAGE_URL']?>" <?if($_GET["sectioncode"]==$arSection["CODE"]){?>class="sectionsselected"<?}else{?>class="sectionslink"<?}?>><?=$arSection["NAME"]?></a>
		<?if($_GET["sectioncode"]==$arSection["CODE"]){?><div class="arrow"></div><?}?>
	</h3>
<?endforeach;?>