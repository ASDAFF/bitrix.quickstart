<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="sections_uslass">
	<?foreach($arResult["SECTIONS"] as $section):?>
		<div class="sections_uslass-sec">
			<a href="<?=$section["SECTION_PAGE_URL"]?>">
				<img src="<?=$section["PICTURE"]["SRC"]?>" width="<?=$section["PICTURE"]["TRUE_SIZE"][0]?>" height="<?=$section["PICTURE"]["TRUE_SIZE"][1]?>" border="0" alt="" />
			</a>
		</div>
	<?endforeach;?>
</div>