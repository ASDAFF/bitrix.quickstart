<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
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
?>

<?php if (!empty($arResult['ITEMS'])): ?>
	<section>
        <?php
        /*
        if ('Y' == $arParams['SHOW_PARENT']) {
			?><h2 class="head2"><?=$arResult['NAME'] ?></h2><?
		}
        */
        
        if ($arParams['DISPLAY_TOP_PAGER']) {
			echo $arResult['NAV_STRING']?><br /><?
		}
		?>
        <div>
        <?php
		$strEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
		$strDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
		$arDeleteParams = array('CONFIRM' => getMessage('RS_SLINE.BNL_TILES.ELEMENT_DELETE_CONFIRM'));
        ?>
        
			
            <?php foreach ($arResult['LETTERS'] as $arLetters): ?>
				<div class="row abc_row">
				<?php foreach ($arLetters as $sLetter => $arLetter): ?>
					<div class="abc col-xs-6 col-sm-4 col-md-3 col-lg-2d4">
						<div class="abc__letter"><?=$sLetter?></div>
                        <?php foreach ($arLetter as $iItemKey): ?>
							<div id="<?=$this->GetEditAreaId($arResult['ITEMS'][$iItemKey]['ID'])?>">
                            
                                <?php
								$this->AddEditAction($arResult['ITEMS'][$iItemKey]['ID'], $arResult['ITEMS'][$iItemKey]['EDIT_LINK'], $strEdit);
								$this->AddDeleteAction($arResult['ITEMS'][$iItemKey]['ID'], $arResult['ITEMS'][$iItemKey]['DELETE_LINK'], $strDelete, $arDeleteParams);

								if ($arResult['CATALOG_LINKED_PROP'] && !$arParams['SECTIONS_PROP']) {
									$arResult['ITEMS'][$iItemKey]['PRODUCT_URL'] = SITE_DIR.$arParams['CATALOG_URL'].'?'
										.$arParams['CATALOG_FILTER_NAME'].'_'.$arResult['CATALOG_LINKED_PROP']['ID'].'_'
										.abs(crc32($arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['LINKED_PROP']]['VALUE_ENUM_ID']
											? $arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['LINKED_PROP']]['VALUE_ENUM_ID']
											: htmlspecialcharsbx($arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['LINKED_PROP']]['VALUE']))
										).'=Y&amp;set_filter=Y';
								} elseif (!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])) {
									$arResult['ITEMS'][$iItemKey]['PRODUCT_URL'] = $arResult['ITEMS'][$iItemKey]['DETAIL_PAGE_URL'];
								}

                                $arItem = $arResult['ITEMS'][$iItemKey];
                                ?>
                                
                                <?php if ($arParams['DISPLAY_PICTURE'] != 'N' && is_array($arItem['PREVIEW_PICTURE'])): ?>

                                    <?php if ($arItem['PRODUCT_URL']): ?>
                                        <a href="<?=$arItem['PRODUCT_URL']?>">
                                            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
                                        </a>
                                    <?php else: ?>
                                        <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
                                    <?php endif; ?>

                                <?php endif; ?>

                                <?php if ($arParams['DISPLAY_NAME'] != 'N' && $arItem['NAME']): ?>
                                    <?php if ($arItem['PRODUCT_URL']): ?>
                                        <a class="abc__link" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                                    <?php else: ?>
                                        <?=$arItem['NAME']?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if ($arParams['DISPLAY_DATE'] != 'N' && $arItem['DISPLAY_ACTIVE_FROM']): ?>
                                    <time datetime="<?=ConvertDateTime($arItem['DISPLAY_ACTIVE_FROM'], 'YYYY-MM-DD')?>"><?=$arItem['DISPLAY_ACTIVE_FROM']?></time>
                                <?php endif; ?>
                                
                                <?php
                                if ($arParams['DISPLAY_PREVIEW_TEXT'] != 'N' && $arItem['PREVIEW_TEXT']) {
                                    echo $arItem['PREVIEW_TEXT'];
                                }
                                ?>
                                
                                <?php foreach ($arItem['FIELDS'] as $code => $value): ?>
                                    <div><?=getMessage('IBLOCK_FIELD_'.$code)?>:&nbsp;<?=$value;?></div><br />
                                <?php endforeach; ?>

                                <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $pid => $arProperty): ?>
                                    <?php if ($pid != $arParams['LINKED_PROP']): ?>
                                        <small>
                                            <?=$arProperty['NAME']?>:&nbsp;
                                            <?php 
                                            if (is_array($arProperty['DISPLAY_VALUE'])) {
                                                echo implode('&nbsp;/&nbsp;', $arProperty['DISPLAY_VALUE']);
                                            } else {
                                                echo $arProperty['DISPLAY_VALUE'];
                                            }
                                            ?>
                                        </small>
                                    <?php endif; ?>
                                <?php endforeach; ?>

							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				</div>
            <?php endforeach; ?>
            
		
        
		<?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
			<br /><? echo $arResult['NAV_STRING']?>
		<?php endif; ?>
        </div>
		<?php if ($arParams['SECTION_PAGE_MORE_URL']): ?>
			<a class="rs_more_link" href="<?=$arParams['SECTION_PAGE_MORE_URL']?>"><?=(strlen($arParams['SECTION_PAGE_MORE_TEXT']) > 0 ? $arParams['SECTION_PAGE_MORE_TEXT'] : getMessage('RS_SLINE.BNL_TILES.MORE_LINK'))?></a>
		<?php endif; ?>

	</section>
<?php endif; ?>