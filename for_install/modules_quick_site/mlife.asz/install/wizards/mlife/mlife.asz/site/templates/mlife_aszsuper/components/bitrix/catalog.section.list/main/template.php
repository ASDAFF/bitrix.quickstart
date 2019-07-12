<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<div class="sh_sectionList">
	<h1><a href="<? echo $arResult['SECTION']['SECTION_PAGE_URL']; ?>">
	<?if(!$arResult['SECTION']['NAME']) $arResult['SECTION']['NAME'] = GetMessage("MLIFE_ASZ_TEMPLATER_1");?>
	<?
		echo (
			isset($arResult['SECTION']["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) && $arResult['SECTION']["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != ""
			? $arResult['SECTION']["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]
			: $arResult['SECTION']['NAME']
		);
	?></a></h1>
	<div class="sh_contentList">
		<?foreach ($arResult['SECTIONS'] as &$arSection){?>
		<div class="item">
			<div class="image">
				<div class="img">
				<?
				if (false === $arSection['PICTURE'])
					$arSection['PICTURE'] = array(
						'SRC' => $templateFolder.'/images/tile-empty.png',
						'ALT' => (
							'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
							? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
							: $arSection["NAME"]
						),
						'TITLE' => (
							'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
							? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
							: $arSection["NAME"]
						)
					);
				?>
				<a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" title="<? echo $arSection['PICTURE']['TITLE']; ?>">
				<img src="<? echo $arSection['PICTURE']['SRC']; ?>" alt="<? echo $arSection['PICTURE']['TITLE']; ?>"/>
				</a>
				</div>
				<div class="count"><? echo $arSection['ELEMENT_CNT']; ?></div>
			</div>
			<div class="title"><a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><? echo $arSection['NAME']; ?></a></div>
		</div>
		<?}?>
	</div>
</div>