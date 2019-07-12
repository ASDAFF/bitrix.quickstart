<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$count = count($arResult['SECTIONS']);
$arViewModeList = $arResult['VIEW_MODE_LIST'];

$arViewStyles = array(
	'LIST' => array(
		'CONT' => 'bx_sitemap',
		'TITLE' => 'bx_sitemap_title',
		'LIST' => 'bx_sitemap_ul',
	),
	'LINE' => array(
		'CONT' => 'bx_catalog_line',
		'TITLE' => 'bx_catalog_line_category_title',
		'LIST' => 'bx_catalog_line_ul',
		'EMPTY_IMG' => $this->GetFolder().'/images/line-empty.png'
	),
	'TEXT' => array(
		'CONT' => 'bx_catalog_text',
		'TITLE' => 'bx_catalog_text_category_title',
		'LIST' => 'bx_catalog_text_ul'
	),
	'TILE' => array(
		'CONT' => 'bx_catalog_tile',
		'TITLE' => 'bx_catalog_tile_category_title',
		'LIST' => 'bx_catalog_tile_ul',
		'EMPTY_IMG' => $this->GetFolder().'/images/tile-empty.png'
	)
);
$arCurView = $arViewStyles[$arParams['VIEW_MODE']];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
$bOpen = false;
?>
<div class="bj-block-group">
<?foreach ($arResult['SECTIONS'] as $key => &$arSection):?>
	<?$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);?>
	<?if (false === $arSection['PICTURE'])
		$arSection['PICTURE'] = array(
			'SRC' => $arCurView['EMPTY_IMG'],
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
	<?if($key % 3 == 0):$bOpen = true;?><div class="row"><?endif;?>
		<div class="col-sm-4" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
			<div class="bj-block">
				<?if($arSection['PICTURE']['SRC']):?><a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><img src="<? echo $arSection['PICTURE']['SRC']; ?>" class="img-responsive bj-block__img" title="<? echo $arSection['PICTURE']['TITLE']; ?>"></a><?endif;?>
				<div class="bj-block__title bj-table">
					<div class="bj-table-row">
						<div class="bj-table-cell">
							<h2 class="bj-block__title__wrapper">
								<a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><?=$arSection["NAME"]?></a>
							</h2>
						</div>
					</div>
				</div>
				<div><?=$arSection["DESCRIPTION"]?></div>
				<?if(!empty($arSection["SUBSECTIONS"])):
				?><div class="bj-hr"></div>
				<div class="bj-hr"></div>
				<div class="bj-block__list">
					<?foreach ($arSection["SUBSECTIONS"] as $arSub) {
					?><a href="<?=$arSub["SECTION_PAGE_URL"]?>"><span class="bj-block__list__text"><?=$arSub["NAME"]?></span> <span class="label label-default label-lg"><?=$arSub["ELEMENTS_CNT"]?></span></a> <?	
					}?>
				</div><?
				endif;?>
			</div>
		</div>
	<?if($key % 3 == 2):$bOpen = false;?>
	</div><hr>
	<?elseif($key+1 != $count):?>
	<hr class="clearfix visible-xs-block">
	<?endif;?>
<?endforeach;?>
<?if($bOpen):?></div><?endif;?>
</div>