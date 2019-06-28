<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<a class="startshop-basket-small default startshop-link startshop-link-hover-dark<?=$arParams['DISPLAY_COUNT'] == "Y" ? ' startshop-with-count' : ''?>" href="<?=$arParams['URL_BASKET']?>">
	<?$frame = $this->createFrame()->begin()?>
        <?if ($arParams['USE_COUNT'] == "Y"):?>
		    <?if ($arResult['COUNT'] > 0 || $arParams['USE_COUNT_IF_EMPTY'] == "Y"):?>
				<div class="startshop-basket-small-count startshop-element-background">
					<div class="startshop-aligner-vertical"></div>
					<div class="startshop-basket-small-text">
						<?=$arResult['COUNT']?>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
		<div class="startshop-basket-small-icon"></div>
		<?if ($arParams['USE_SUM'] == "Y"):?>
			<div class="startshop-basket-small-text-total">
				<?=$arResult['SUM']['PRINT_VALUE']?>
			</div>
		<?endif;?>
	<?$frame->beginStub()?>
        <?if ($arParams['USE_COUNT'] == "Y"):?>
            <?if ($arResult['COUNT'] > 0 || $arParams['USE_COUNT_IF_EMPTY'] == "Y"):?>
                <div class="startshop-basket-small-count startshop-element-background">
                    <div class="startshop-aligner-vertical"></div>
                    <div class="startshop-basket-small-text">
                        0
                    </div>
                </div>
            <?endif;?>
        <?endif;?>
        <div class="startshop-basket-small-icon"></div>
        <?if ($arParams['USE_SUM'] == "Y"):?>
            <div class="startshop-basket-small-text-total">
                0
            </div>
        <?endif;?>
	<?$frame->end()?>	
</a>