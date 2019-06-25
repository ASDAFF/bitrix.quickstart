<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true)?>
<?if(!empty($arResult['SECTIONS'])):?>
	<?
		switch ($arParams['GRID_CATALOG_SECTIONS_COUNT']) {
			case '3': $gridStyle = "startshop-33"; break;
			case '4': $gridStyle = "startshop-25"; break;
			case '5': $gridStyle = "startshop-20"; break;
			default : $gridStyle = "startshop-50"; break;
		}
	?>
	<div class="startshop-catalog<?=$arParams['ADAPTABLE'] == "Y" ? " adaptiv" : ""?>">
		<div class="startshop-catalog-sections-list startshop_parent_col">
		<?$frame = $this->createFrame()->begin()?>
		<?
			foreach ($arResult['SECTIONS'] as &$arSection)
			{
				if ($arSection['DEPTH_LEVEL'] == 1 || !($arParams['ROOT_SECTIONS'] == "Y"))
				{
					$picture = array();
					$picture['src'] = $this->GetFolder().'/images/product.noimage.png';
						
					if (!empty($arSection['PICTURE']))
					{
						$picture = CFile::ResizeImageGet($arSection['PICTURE']['ID'], array('width' => 300, 'height' => 300, BX_RESIZE_IMAGE_PROPORTIONAL_ALT));
					}
						
					$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
					$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
					?>
					<div class="startshop_col <?=$gridStyle?>">
						<div class="startshop-element" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
							<a class="startshop-wrapper startshop-hover-shadow" href="<? echo $arSection["SECTION_PAGE_URL"]; ?>">
								<div class="startshop-image-wrapper">
									<div>
										<div class="startshop-aligner-vertical"></div>
										<img src="<?=$picture['src']?>" />
									</div>
								</div>
								<div class="startshop-text">
									<div class="startshop-text-wrapper">
										<?=$arSection["NAME"];?>
										<?=$arParams["COUNT_ELEMENTS"] ? '('.$arSection["ELEMENT_CNT"].')' : ''?>
									</div>
								</div>
							</a>
						</div>
					</div>
					<?
				}
			}
				
			echo '<div class="description">'.$arResult['IBLOCK']['DESCRIPTION'].'</div>';
		?>
		<?$frame->end()?>
		</div>
		<?if (!empty($arResult["SECTION"]["DESCRIPTION"])):?>
			<div class="startshop-indents-vertical indent-20"></div>
			<div class="in_sec_desription">
				<?=$arResult["SECTION"]["DESCRIPTION"]?>
			</div>
		<?endif;?>
	</div>
<?endif;?>