<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['ITEMS'])){?>
	<ul class="list-questions">
		<?foreach ($arResult['ITEMS'] as $item){?>
			<li>
				<div class="question-js"><span><?=$item['DISPLAY_PROPERTIES']['COMMENT']['VALUE']['TEXT']?></span></div>
				<p><?=$item['DISPLAY_PROPERTIES']['ANSWER']['VALUE']['TEXT']?></p>
			</li>
		<?}?>
	</ul>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br /><?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?}?>