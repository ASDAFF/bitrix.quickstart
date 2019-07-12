<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult['CAT'] as $arSection):
	$bHasPicture = is_array($arSection['PICTURE']);
	$bHasChildren = is_array($arSection['CHILDREN']) && count($arSection['CHILDREN']) > 0;
	if ($bHasPicture)
	{
		?><img src="<?=$arSection['PICTURE_PREVIEW']['SRC']?>" width="<?=$arSection['PICTURE_PREVIEW']['WIDTH']?>" height="<?=$arSection['PICTURE_PREVIEW']['HEIGHT']?>" style="float:right;"/><?
	}
	if ($arSection['NAME'] && $arResult['CAT']['SECTION']['ID'] != $arSection['ID'])
	{
		$url = "#SITE_DIR#".substr($arSection["LIST_PAGE_URL"], strlen(SITE_DIR));
		?><h2><a href="<?=$url?>"><?=$arSection["NAME"]?></a></h2><?
	}
	?>
	<?if ($arSection['DESCRIPTION']):?>
		<p><?=$arSection['DESCRIPTION']?></p>
	<?endif;?>
	<?if ($bHasChildren):?>
		<ul data-role="listview" data-inset="true">
			<?
			foreach ($arSection['CHILDREN'] as $key => $arChild)
			{
				$url = "#SITE_DIR#".substr($arChild["SECTION_PAGE_URL"], strlen(SITE_DIR));
				?><li><a href="<?=$url?>"><?=$arChild['NAME']?></a></li><?
			}?>
		</ul>
	<?endif;?>
<?endforeach;?>
