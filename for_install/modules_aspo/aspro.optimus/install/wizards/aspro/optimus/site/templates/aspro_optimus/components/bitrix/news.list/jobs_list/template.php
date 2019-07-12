<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<div class="border_block job">
	<div class="wrap_md">
		<div class="iblock text">
			<?$APPLICATION->IncludeFile(SITE_DIR."include/jobs_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("JOB_DETAIL_TEXT"), ));?>
		</div>
		<div class="iblock phone">
			<div class="wrap_phones">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/jobs_description_phone.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("JOB_PHONE"), ));?>
				<?//$APPLICATION->IncludeFile(SITE_DIR."include/jobs_description_email.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("JOB_EMAIL"), ));?>
			</div>
		</div>
		<div class="iblock but">
			<a class="button vbig_btn wides resume_send" data-jobs="<?=$arItem['NAME']?>">
				<span><?=GetMessage('SEND_RESUME')?></span>
			</a>
		</div>
	</div>
</div>
<?if($arResult["ITEMS"]){?>
	<h3 class="jobs"><?=GetMessage("ACTUAL_VACANCY");?></h3>
	<div class="jobs_wrapp">
		<?foreach($arResult["ITEMS"] as $key => $arItem){
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="name">
					<table>
						<tr>
							<td class="title">
								<h4><span class="link"><?=$arItem['NAME']?></span></h4>
								<div class="salary">
									<?if ($arItem["DISPLAY_PROPERTIES"]["SALARY"]){?>
										<?=GetMessage("SALARY");?> <?=number_format($arItem["DISPLAY_PROPERTIES"]["SALARY"]["VALUE"], 0, "", " ");?> <?=($arItem["DISPLAY_PROPERTIES"]["CURRENCY"]["VALUE"] ? $arItem["DISPLAY_PROPERTIES"]["CURRENCY"]["VALUE"] : GetMessage("CURRENCY") );?>
									<?}else{?>
										<?=GetMessage("NOT_SALARY");?>
									<?}?>
								</div>
							</td>
							<td class="salary_wrapp">
								<div class="salary">
									<?if ($arItem["DISPLAY_PROPERTIES"]["SALARY"]){?>
										<?=GetMessage("SALARY");?> <?=number_format($arItem["DISPLAY_PROPERTIES"]["SALARY"]["VALUE"], 0, "", " ");?> <?=($arItem["DISPLAY_PROPERTIES"]["CURRENCY"]["VALUE"] ? $arItem["DISPLAY_PROPERTIES"]["CURRENCY"]["VALUE"] : GetMessage("CURRENCY") );?>
									<?}else{?>
										<?=GetMessage("NOT_SALARY");?>
									<?}?>
								</div>
							</td>
							<td class="icon">
								<span class="slide opener_icon no_bg"><i></i></span>
							</td>
						</tr>
					</table>
				</div>
				<div class="description_wrapp" >
					<?if ($arItem['PREVIEW_TEXT']):?>
						<div class="description"><?=$arItem['PREVIEW_TEXT']?></div>
					<?elseif ($arItem['DETAIL_TEXT']): ?>
						<div class="description"><?=$arItem['DETAIL_TEXT']?></div>
					<?endif;?>
					<a class="button vbig_btn wides resume_send" data-jobs="<?=$arItem['NAME']?>">
						<span><?=GetMessage('SEND_RESUME')?></span>
					</a>
				</div>
			</div>
		<?}?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]){?>
			<?=$arResult["NAV_STRING"]?>
		<?}?>
	</div>
<?}?>